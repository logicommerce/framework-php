<?php

namespace FWK\ViewHelpers\User\Macro;

use FWK\Core\Theme\Theme;
use SDK\Core\Dtos\ElementCollection;
use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use SDK\Enums\AccountKey;

/**
 * This is the AddressBook class, a macro class for the userViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the user address book.
 *
 * @see AddressBook::getViewParameters()
 *
 * @package FWK\ViewHelpers\User\Macro
 */
class AddressBook {

    public const SELECT_MODE_RADIO = 'selectModeRadio';

    public const SELECT_MODE_BUTTON = 'selectModeButton';

    public ?ElementCollection $billingAddresses = null;

    public ?ElementCollection $shippingAddresses = null;

    private ?string $defaultUserType = null;

    public string $selectMode = self::SELECT_MODE_BUTTON;

    public bool $showAddNewBilling = true;

    public bool $showAddNewShipping = true;

    public bool $showEditBilling = true;

    public bool $showEditShipping = true;

    public bool $showDeleteBilling = true;

    public bool $showDeleteShipping = true;

    public bool $showSelectAddressBilling = true;

    public bool $showSelectAddressShipping = true;

    public string $accountId = AccountKey::USED;


    /**
     * Constructor method for Form.
     *
     * @see Form
     *
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        ViewHelper::mergeArguments($this, $arguments);
    }

    /**
     * This method returns all calculated arguments and new parameters for UserViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        if (is_null($this->billingAddresses)) {
            throw new CommerceException("The value of [billingAddresses] argument: '" . $this->billingAddresses . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }
        if (is_null($this->shippingAddresses)) {
            throw new CommerceException("The value of [shippingAddresses] argument: '" . $this->shippingAddresses . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }
        $this->defaultUserType = Theme::getInstance()->getConfiguration()->getForms()->getSetUser()->getDefaultUserType();

        if ($this->selectMode !== self::SELECT_MODE_BUTTON && $this->selectMode !== self::SELECT_MODE_RADIO) {
            throw new CommerceException("The value of [selectMode] argument: '" . $this->selectMode . "' isn't valid. Only are valid AddressBook::SELECT_MODE_RADIO or AddressBook::SELECT_MODE_BUTTON. " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }

        return $this->getProperties();
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        return [
            'billingAddresses' => $this->billingAddresses,
            'shippingAddresses' => $this->shippingAddresses,
            'defaultUserType' => $this->defaultUserType,
            'selectMode' => $this->selectMode,
            'showAddNewBilling' => $this->showAddNewBilling,
            'showAddNewShipping' => $this->showAddNewShipping,
            'showEditBilling' => $this->showEditBilling,
            'showEditShipping' => $this->showEditShipping,
            'showDeleteBilling' => $this->showDeleteBilling,
            'showDeleteShipping' => $this->showDeleteShipping,
            'showSelectAddressBilling' => $this->showSelectAddressBilling,
            'showSelectAddressShipping' => $this->showSelectAddressShipping,
            'accountId' => $this->accountId
        ];
    }
}
