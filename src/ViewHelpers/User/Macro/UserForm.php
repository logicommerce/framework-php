<?php

namespace FWK\ViewHelpers\User\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Theme\Theme;
use SDK\Dtos\Settings\CountrySettings;
use SDK\Enums\RouteType;
use FWK\Core\Resources\Session;
use SDK\Dtos\User\User;
use SDK\Core\Dtos\ElementCollection;
use FWK\Core\Form\Form as CoreForm;
use FWK\Core\Resources\Utils;
use FWK\Services\LmsService;

/**
 * This is the UserForm class, a macro class for the userViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the user's form.
 *
 * @see UserForm::getViewParameters()
 *
 * @package FWK\ViewHelpers\User\Macro
 */
class UserForm {

    public string $routeType = '';

    public ?CoreForm $form = null;

    public ?CountrySettings $selectedCountry = null;

    public array $selectedCountryLocations = [];

    public bool $showCreateAccountCheck = true;

    public bool $showShippingAddress = false;

    public bool $showCustomTagsTitle = false;

    public bool $forceUseShippingAddress = false;

    public ?ElementCollection $billingAddresses = null;

    public ?ElementCollection $shippingAddresses = null;

    private ?string $defaultUserType = null;

    private string $class = '';

    private bool $isGuest = false;

    private ?User $sessionUser = null;

    public string $selectMode = AddressBook::SELECT_MODE_BUTTON;

    public bool $showAddNewBilling = true;

    public bool $showAddNewShipping = true;

    public bool $showEditBilling = true;

    public bool $showEditShipping = true;

    public bool $showDeleteBilling = true;

    public bool $showDeleteShipping = true;

    /**
     * Constructor method for UserForm.
     *
     * @see UserForm
     *
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        if (!isset($arguments['routeType']) || $arguments['routeType'] == RouteType::CHECKOUT_CUSTOMER || $arguments['routeType'] == RouteType::CHECKOUT || $arguments['routeType'] == RouteType::CHECKOUT_GUEST) {
            $this->showShippingAddress = true;
        }
        ViewHelper::mergeArguments($this, $arguments);
    }

    /**
     * This method returns all calculated arguments and new parameters for UserViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        if (is_null($this->form)) {
            throw new CommerceException("The value of [form] argument: '" . $this->form . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }
        if (is_null($this->selectedCountry) && ($this->routeType === RouteType::USER_CREATE_ACCOUNT || $this->routeType === RouteType::ACCOUNT_CREATE || $this->routeType === RouteType::CHECKOUT_CREATE_ACCOUNT)) {
            throw new CommerceException("The value of [selectedCountry] argument: '" . $this->selectedCountry . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }
        $session = Session::getInstance();
        if ($session !== null && $session->getUser()->getId() !== 0) {
            if (is_null($this->billingAddresses) && ($this->routeType === RouteType::CHECKOUT_CUSTOMER || $this->routeType === RouteType::CHECKOUT_CUSTOMER_NEW_REGISTER)) {
                throw new CommerceException("The value of [billingAddresses] argument: '" . $this->billingAddresses . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
            }
            if (is_null($this->shippingAddresses) && ($this->routeType === RouteType::CHECKOUT_CUSTOMER || $this->routeType === RouteType::CHECKOUT_CUSTOMER_NEW_REGISTER)) {
                throw new CommerceException("The value of [shippingAddresses] argument: '" . $this->shippingAddresses . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
            }
        }

        if ($this->selectMode !== AddressBook::SELECT_MODE_BUTTON && $this->selectMode !== AddressBook::SELECT_MODE_RADIO) {
            throw new CommerceException("The value of [selectMode] argument: '" . $this->selectMode . "' isn't valid. Only are valid AddressBook::SELECT_MODE_RADIO or AddressBook::SELECT_MODE_BUTTON. " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }

        $this->setIsGuest();
        $this->setDefaultUserType();
        $this->setClass();

        return $this->getProperties();
    }

    /**
     * Set class property
     *
     * @return void
     */
    private function setClass(): void {
        $this->class = str_replace('_', '', ucwords(strtolower($this->routeType), '_'));
    }

    /**
     * Set isGuest property
     *
     * @return void
     */
    private function setIsGuest(): void {
        $this->sessionUser = Session::getInstance()->getUser();
        $userName = Utils::getUserName($this->sessionUser);
        $this->isGuest = $this->sessionUser->getId() === 0 && strlen($userName) > 0;
    }

    /**
     * Set defaultUserType property
     *
     * @return void
     */
    private function setDefaultUserType(): void {
        if (!is_null($this->sessionUser->getDefaultBillingAddress())) {
            $this->defaultUserType = $this->sessionUser->getDefaultBillingAddress()->getUserType();
        } else {
            $this->defaultUserType = Theme::getInstance()->getConfiguration()->getForms()->getSetUser()->getDefaultUserType();
        }
    }

    private function getLocationMode(): string {
        if (LmsService::getLocationSearchZipCityLicense()) {
            return LmsService::LOCATION_SEARCH_ZIP_CITY;
        } elseif (LmsService::getLocationSearchCityLicense()) {
            return LmsService::LOCATION_SEARCH_CITY;
        }
        return '';
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        return [
            'form' => $this->form,
            'selectedCountry' => $this->selectedCountry,
            'selectedCountryLocations' => $this->selectedCountryLocations,
            'showCreateAccountCheck' => $this->showCreateAccountCheck,
            'showCustomTagsTitle' => $this->showCustomTagsTitle,
            'showShippingAddress' => $this->showShippingAddress,
            'forceUseShippingAddress' => $this->forceUseShippingAddress,
            'defaultUserType' => $this->defaultUserType,
            'class' => $this->class,
            'isGuest' => $this->isGuest,
            'billingAddresses' => $this->billingAddresses,
            'shippingAddresses' => $this->shippingAddresses,
            'selectMode' => $this->selectMode,
            'showAddNewBilling' => $this->showAddNewBilling,
            'showAddNewShipping' => $this->showAddNewShipping,
            'showEditBilling' => $this->showEditBilling,
            'showEditShipping' => $this->showEditShipping,
            'showDeleteBilling' => $this->showDeleteBilling,
            'showDeleteShipping' => $this->showDeleteShipping,
            'locationMode' => $this->getLocationMode()
        ];
    }
}
