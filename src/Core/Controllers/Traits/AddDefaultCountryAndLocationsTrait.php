<?php

declare(strict_types=1);

namespace FWK\Core\Controllers\Traits;

use FWK\Core\Resources\Language;
use FWK\Enums\Services;
use FWK\Core\Resources\Loader;
use FWK\Core\Resources\Session;
use SDK\Services\Parameters\Groups\Geolocation\LocationParametersGroup;
use SDK\Application;
use SDK\Dtos\Settings\CountrySettings;

/**
 * This is the add default country locations
 *
 * @see AddDefaultCountryAndLocationsTrait::getDefaultCountry()
 * @see AddDefaultCountryAndLocationsTrait::getDefaultCountryLocations()
 *
 * @package FWK\Core\Controllers\Traits
 */
trait AddDefaultCountryAndLocationsTrait {

    /**
     * returns default country
     * 
     * @return CountrySettings
     */
    protected function getDefaultCountry(): CountrySettings {
        $languageSheet = Language::getInstance();
        $countries = Application::getInstance()->getCountriesSettings($languageSheet->getLanguage())->getItems();
        $defaultCountryCode = Session::getInstance()->getGeneralSettings()->getCountry();
        $defaultCountry = $countries[0];
        foreach ($countries as $country) {
            if ($country->getCode() === $defaultCountryCode) {
                $defaultCountry = $country;
                break;
            }
        }
        return $defaultCountry;
    }

    /**
     * returns default selected country locations
     * 
     * @return array
     */
    protected function getDefaultCountryLocations(): array {
        $locationParametersGroup = new LocationParametersGroup();
        $locationParametersGroup->setCountryCode(Session::getInstance()->getGeneralSettings()->getCountry());
        $locations = Loader::service(Services::GEOLOCATION)->getLocations($locationParametersGroup)->getItems();
        return $locations;
    }
}
