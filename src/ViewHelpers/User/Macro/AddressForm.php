<?php

namespace FWK\ViewHelpers\User\Macro;

use FWK\Core\Theme\Theme;
use SDK\Dtos\Settings\CountrySettings;
use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Form\Form;
use FWK\Services\LmsService;

/**
 * This is the AddressForm class, a macro class for the userViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the user's address form.
 *
 * @see AddressForm::getViewParameters()
 *
 * @package FWK\ViewHelpers\User\Macro
 */
class AddressForm {

    public const BILLING_PREFIX = 'billing';

    public const SHIPPING_PREFIX = 'shipping';

    public ?Form $form = null;

    public ?CountrySettings $selectedCountry = null;

    public array $selectedCountryLocations = [];

    public string $prefix = self::BILLING_PREFIX;

    public bool $addressBook = true;

    private ?string $defaultUserType = null;


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
        if (is_null($this->form)) {
            throw new CommerceException("The value of [form] argument: '" . $this->form . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }

        $this->setDefaultUserType();

        return $this->getProperties();
    }

    /**
     * Set defaultUserType property
     *
     * @return void
     */
    private function setDefaultUserType(): void {
        $this->defaultUserType = Theme::getInstance()->getConfiguration()->getForms()->getSetUser()->getDefaultUserType();
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
            'prefix' => $this->prefix,
            'addressBook' => $this->addressBook,
            'defaultUserType' => $this->defaultUserType,
            'locationMode' => $this->getLocationMode()
        ];
    }
}
