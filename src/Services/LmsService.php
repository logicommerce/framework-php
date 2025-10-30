<?php

namespace FWK\Services;

use FWK\Core\Resources\Registries\RegistryService;
use SDK\Services\LmsService as LmsServiceSDK;
use FWK\Services\Traits\ServiceTrait;
use SDK\Application;
use SDK\Dtos\License;
use SDK\Enums\LicenseType;

/**
 * This is the LmsService class.
 * Remember that a service is an extension of a SDK model that allows to add additional
 * actions to a model request or create new methods to simplify some common requests.
 * In this case, the LmsService extends the SDK\Services\LmsService.
 *
 * @see LmsService
 *
 * @package FWK\Services
 */
class LmsService extends LmsServiceSDK {
    use ServiceTrait;

    private const REGISTRY_KEY = RegistryService::LMS_SERVICE;

    private const ADD_FILTER_INTERVAL_PARAMETERS = [];

    private const ADD_FILTER_ID_VALUE_PARAMETERS = [];

    public const LOCATION_SEARCH_ZIP_CITY = 'locationSearchZipCity';

    public const LOCATION_SEARCH_CITY = 'locationSearchCity';

    private static ?bool $advcaLicense = null;

    private static ?bool $shoppingListLicense = null;

    private static ?bool $locationSearchZipCity = null;

    private static ?bool $locationSearchCity = null;

    private static ?array $licenses = null;

    private static function getApplicationLicenses(): array {
        if (is_null(self::$licenses)) {
            self::$licenses = Application::getInstance()->getEcommerceLicenses()->getLicenses();
        }
        return self::$licenses;
    }

    public static function getAdvcaLicense(): bool {
        if (is_null(self::$advcaLicense)) {
            self::$advcaLicense = false;
            foreach (self::getApplicationLicenses() as $license) {
                if ($license->getPId() === 'ADVCA') {
                    self::$advcaLicense = true;
                    break;
                }
            }
        }
        return self::$advcaLicense;
    }

    public static function getShoppingListLicense(): bool {
        if (is_null(self::$shoppingListLicense)) {
            self::$shoppingListLicense = false;
            foreach (self::getApplicationLicenses() as $license) {
                if ($license->getPId() === 'LSTADV') {
                    self::$shoppingListLicense = true;
                    break;
                }
            }
        }
        return self::$shoppingListLicense;
    }

    public static function getLocationSearchZipCityLicense(): bool {
        if (is_null(self::$locationSearchZipCity)) {
            self::$locationSearchZipCity = false;
            foreach (self::getApplicationLicenses() as $license) {
                if ($license->getPId() === 'ADEXT') {
                    self::$locationSearchZipCity = true;
                    break;
                }
            }
        }
        return self::$locationSearchZipCity;
    }

    public static function getLocationSearchCityLicense(): bool {
        if (is_null(self::$locationSearchCity)) {
            self::$locationSearchCity = false;
            foreach (self::getApplicationLicenses() as $license) {
                if ($license->getPId() === 'ADBAS') {
                    self::$locationSearchCity = true;
                    break;
                }
            }
        }
        return self::$locationSearchCity;
    }

    public static function getLicense(LicenseType $licenseType): ?License {
        foreach (self::getApplicationLicenses() as $license) {
            if ($licenseType->name == $license->getPId()) {
                return $license;
            }
        }
        return null;
    }

    public static function hasLicense(LicenseType $licenseType): ?bool {
        return self::getLicense($licenseType) != null;
    }
}
