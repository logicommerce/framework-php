<?php

namespace FWK\ViewHelpers\User\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Resources\Session;
use SDK\Application;
use SDK\Core\Dtos\ElementCollection;
use SDK\Dtos\CountryLocation;
use SDK\Enums\PostalCodeType as PostalCodeType;

/**
 * This is the LocationsPath class, a macro class for the userViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the user's change password form.
 *
 * @see LocationsPath::getViewParameters()
 *
 * @package FWK\ViewHelpers\User\Macro
 */
class LocationsPath {

    public ?string $fieldName = '';

    public string $countryCode = '';

    public string $country = '';

    public string $state = '';

    public string $city = '';

    public string $postalCode = '';

    private int $locationMaxLevel = 0;

    private int $fieldsMaxLevel = 0;

    private int $fieldsMaxRequiredLevel = 0;

    public ?ElementCollection $selectedIds = null;

    /**
     * Constructor method for LocationsPath class.
     * 
     * @see LocationsPath
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
        if (is_null($this->selectedIds)) {
            throw new CommerceException("The value of selectedIds argument is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }
        if (!($this->selectedIds instanceof ElementCollection)) {
            throw new CommerceException('The value of selectedIds argument must be a instance of ' . ElementCollection::class . '. ' . ' Instance of ' . get_class($this->selectedIds) . ' sended.', CommerceException::VIEW_HELPER_INVALID_ARGUMENT);
        }
        $this->locationMaxLevel = 0;
        foreach ($this->selectedIds as $countryLocation) {
            if (!($countryLocation instanceof CountryLocation)) {
                throw new CommerceException('Each element of selectedIds must be a instance of ' . CountryLocation::class . '. ' . ' Instance of ' . get_class($countryLocation) . ' sended.', CommerceException::VIEW_HELPER_INVALID_ARGUMENT);
            }
            $this->locationMaxLevel = $countryLocation->getLevel() > $this->locationMaxLevel ? $countryLocation->getLevel() : $this->locationMaxLevel;
        }
        $postalCodeType = null;
        $sessionGeneralSettings = Session::getInstance()->getGeneralSettings();
        foreach (Application::getInstance()->getCountriesSettings($sessionGeneralSettings->getLanguage()) as $country) {
            if ($this->countryCode == $country->getCode()) {
                $postalCodeType = $country->getPostalCodeType();
                break;
            }
        }
        if (is_null($postalCodeType)) {
            throw new CommerceException('Undefined countryCode: "' . $this->countryCode . '", in settings commerce countries', CommerceException::VIEW_HELPER_INVALID_ARGUMENT);
        }

        if (in_array($postalCodeType, [PostalCodeType::STATE_CITY_POSTAL_CODE, PostalCodeType::POSTAL_CODE_MANDATORY])) {
            $this->fieldsMaxLevel = 3;
            $this->fieldsMaxRequiredLevel = 3;
        } elseif ($postalCodeType == PostalCodeType::POSTAL_CODE_OPTIONAL) {
            $this->fieldsMaxLevel = 3;
            $this->fieldsMaxRequiredLevel = 2;
        } elseif ($postalCodeType == PostalCodeType::STATE_CITY_WITHOUT_POSTAL_CODE) {
            $this->fieldsMaxLevel = 2;
            $this->fieldsMaxRequiredLevel = 2;
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
            'fieldName' => $this->fieldName,
            'selectedIds' => $this->selectedIds,
            'countryCode' => $this->countryCode,
            'country' => $this->country,
            'state' => $this->state,
            'city' => $this->city,
            'postalCode' => $this->postalCode,
            'locationMaxLevel' => $this->locationMaxLevel,
            'fieldsMaxLevel' => $this->fieldsMaxLevel,
            'fieldsMaxRequiredLevel' => $this->fieldsMaxRequiredLevel
        ];
    }
}
