<?php

namespace FWK\ViewHelpers\Account\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Resources\Session;
use FWK\Enums\RouteType;
use FWK\Enums\RouteTypes\InternalUser;
use FWK\Services\LmsService;
use SDK\Dtos\Accounts\RegisteredUserSimpleProfile;
use SDK\Enums\AccountType;
use SDK\Enums\SessionUsageModeType;

/**
 * This is the Panel class, a macro class for the userViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the user's panel.
 *
 * @see Panel::getViewParameters()
 *
 * @package FWK\ViewHelpers\Account\Macro
 */
class Panel {

    public const KEY_ACCOUNT = 'ADVCA_account';

    public const KEY_DATA_FOR_ACCOUNT = 'data_for_account';

    public const KEY_PROFILE = 'my_profile';

    public const KEYS = [
        self::KEY_ACCOUNT,
        "",
        "",
        self::KEY_DATA_FOR_ACCOUNT,
        self::KEY_PROFILE,
        "",
        ""
    ];

    public const ITEMS_LIST = [
        [
            'user',
            'addressBook',
            'accountCompanyStructure',
            'accountCompanyRoles',
            'accountRegisteredUsers'
        ],
        [
            'orders',
            'rmas',
            'rewardPoints'
        ],
        [
            'deleteAccount'
        ],
        [
            'accountRegisteredUser',
            'shoppingLists',
            'stockAlerts',
            'subscriptions',
            'paymentCards'
        ],
        [
            'registeredUser',
            'changePassword'
        ],
        [
            'registeredUserSalesAgent',
            'registeredUserSalesAgentCustomers',
            'registeredUserSalesAgentSales'
        ],
        [
            'logout',
            'usedAccountSwitch'
        ]
    ];

    public array $itemsList = self::ITEMS_LIST;

    public array $icons = [];

    private array $keys = self::KEYS;

    private ?RegisteredUserSimpleProfile $user = null;

    /**
     * Constructor for Panel.
     * 
     * @see Panel
     * 
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        ViewHelper::mergeArguments($this, $arguments);
        $this->user = Session::getInstance()->getBasket()->getRegisteredUser();
    }

    /**
     * This method returns all calculated arguments and new parameters for UserViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        $this->pruneBlockForSalesAgent();
        $this->pruneItemsForAdvca();
        $this->pruneItemsForSalesAgentSim();

        if (!Session::getInstance()->getAssociatedAccounts()) {
            $this->removeItems(['usedAccountSwitch']);
        }

        $this->validateList($this->itemsList);
        return $this->getProperties();
    }

    private function pruneBlockForSalesAgent(): void {
        if ($this?->user?->isSalesAgent() !== true) {
            $this?->removeByIndex(5);
        }
    }

    private function pruneItemsForAdvca(): void {
        $basket = Session::getInstance()->getBasket();
        $type   = $basket?->getAccount()?->getType();
        $mode   = $basket?->getMode()?->getType();

        $useADVCA = LmsService::getAdvcaLicense()
            && in_array($type, [AccountType::COMPANY, AccountType::COMPANY_DIVISION], true)
            && $mode !== SessionUsageModeType::SALES_AGENT_SIMULATION;

        if ($useADVCA) {
            return;
        }

        // TODO: ADD - AND RegisteredFrontOfficeSession.Employee.role.permissions[ROLES_READ] is true
        $this->removeItems(['accountCompanyStructure', 'companyRoles']);
    }

    private function pruneItemsForSalesAgentSim(): void {
        if (Session::getInstance()?->getBasket()?->getMode()?->getType() !== SessionUsageModeType::SALES_AGENT_SIMULATION) {
            return;
        }

        $this->removeItems(['accountRegisteredUsers', 'rmas', 'paymentCards', 'accountRegisteredUser', 'usedAccountSwitch', 'changePassword']);
        $this->removeByIndex(2);
    }

    private function removeItems(array $needles): void {
        $this->itemsList = array_map(
            static fn(array $group) =>
            array_values(
                array_filter(
                    $group,
                    static fn($item) => !in_array($item, $needles, true)
                )
            ),
            $this->itemsList
        );
    }

    private function removeByIndex(int $index): void {
        array_splice($this->itemsList, $index, 1);
        array_splice($this->keys, $index, 1);
    }

    /**
     * Loops items list, checks that each item exists as constant (valid item)
     *
     * @param array $elements
     *
     * @return void
     */
    private function validateList(array $groups): void {
        foreach ($groups as $elements) {
            foreach ($elements as $element) {
                $constantPiece = implode('_', array_map('strtoupper', preg_split('/(?=[A-Z])/', $element)));
                if (!$this->getItemListConstant($constantPiece)) {
                    throw new CommerceException("'" . $element . "' item list not exists in " . RouteType::class . ' or ' . InternalUser::class, CommerceException::VIEW_HELPER_UNDEFINED_ARGUMENT);
                }
            }
        }
    }

    /**
     * Check if exists constant into RouteType or InternalUser
     *
     * @param string $constantPiece
     *
     * @return bool
     */
    private function getItemListConstant(string $constantPiece): bool {
        $routeType = new \ReflectionClass('FWK\\Enums\\RouteType');
        $internalUser = new \ReflectionClass('FWK\\Enums\\RouteTypes\\InternalUser');

        if (array_key_exists($constantPiece, $routeType->getConstants())) {
            return true;
        } elseif (array_key_exists('USER_' . $constantPiece, $routeType->getConstants())) {
            return true;
        } elseif (array_key_exists($constantPiece, $internalUser->getConstants())) {
            return true;
        }
        return false;
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        $shoppingLists = Session::getInstance()->getShoppingList()?->getShoppingLists();
        return [
            'itemsList' => $this->itemsList,
            'keys' => $this->keys,
            'icons' => $this->icons,
            'availableShoppingLists' => (!is_null($shoppingLists) && $shoppingLists->count() > 1) ? true : false
        ];
    }
}
