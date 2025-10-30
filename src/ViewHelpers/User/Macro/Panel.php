<?php

namespace FWK\ViewHelpers\User\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Resources\Session;
use SDK\Dtos\User\User;
use FWK\Enums\RouteType;
use FWK\Enums\RouteTypes\InternalUser;

/**
 * This is the Panel class, a macro class for the userViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the user's panel.
 *
 * @see Panel::getViewParameters()
 *
 * @package FWK\ViewHelpers\User\Macro
 */
class Panel {

    public const KEY_DATA = 'data';

    public const KEY_ORDERS = 'orders';

    public const KEY_SALES_AGENT = 'sales_agent';

    public const KEY_ACCOUNT = 'account';

    public const KEYS = [
        self::KEY_DATA,
        self::KEY_ORDERS,
        self::KEY_SALES_AGENT,
        self::KEY_ACCOUNT,
    ];

    public const ITEMS_LIST = [
        [
            'user',
            'addressBook',
            'accountCompanyStructure',
            'changePassword',
            'accountRegisteredUsers',
        ],
        [
            'shoppingLists',
            'wishlist',
            'stockAlerts',
            'subscriptions',
            'orders',
            'rmas',
            'salesAgent',
            'paymentCards',
            'rewardPoints',
            'voucherCodes',
        ],
        [
            'salesAgent',
            'salesAgentCustomers',
            'salesAgentSales',
            'registeredUserSalesAgent',
            'registeredUserSalesAgentCustomers',
            'registeredUserSalesAgentSales',
        ],
        [
            'logout',
            'deleteAccount',
            'usedAccountSwitch',
        ]
    ];

    public array $itemsList = self::ITEMS_LIST;

    public array $icons = [];

    private array $keys = self::KEYS;

    private ?User $user = null;

    /**
     * Constructor for Panel.
     * 
     * @see Panel
     * 
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        ViewHelper::mergeArguments($this, $arguments);
        $this->user = Session::getInstance()->getUser();
    }

    /**
     * This method returns all calculated arguments and new parameters for UserViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        // Is sales agent is false remove third items list block
        if ($this->user->getSalesAgent() !== true) {
            array_splice($this->itemsList, 2, 1);
            array_splice($this->keys, 2, 1);
        }
        $this->validateList($this->itemsList);

        return $this->getProperties();
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
