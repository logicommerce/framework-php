<?php

namespace FWK\Services;

use FWK\Core\Resources\Registries\RegistryService;
use FWK\Core\Resources\Session;
use SDK\Core\Dtos\ElementCollection;
use SDK\Services\SettingsService as SettingsServiceSDK;
use FWK\Services\Traits\ServiceTrait;
use SDK\Dtos\Settings\BlogSettings;

/**
 * This is the SettingsService class.
 * Remember that a service is an extension of a SDK model that allows to add additional
 * actions to a model request or create new methods to simplify some common requests.
 * In this case, the SettingsService extends the SDK\Services\SettingsService.
 *
 * @see SettingsService::getCountryCurrenciesById()
 *
 * @see SettingsService
 *
 * @package FWK\Services
 */
class SettingsService extends SettingsServiceSDK {
    use ServiceTrait;

    private const REGISTRY_KEY = RegistryService::SETTINGS_SERVICE;

    private const ADD_FILTER_INTERVAL_PARAMETERS = [];

    private const ADD_FILTER_ID_VALUE_PARAMETERS = [];

    /**
     * This method returns the Dtos of the country currencies whose id matches the given one.
     * 
     * @param int $id
     * 
     * @return ElementCollection|NULL
     */
    public function getCountryCurrenciesById(int $id): ?ElementCollection {
        return $this->getCountryCurrencies($id);
    }

    /**
     * Returns the website blog settings
     *
     * @return BlogSettings
     */
    public function getBlogSettings(string $languageCode = null): BlogSettings {
        if (is_null($languageCode)) {
            $languageCode = Session::getInstance()->getGeneralSettings()->getLanguage();
        }
        return parent::getBlogSettings($languageCode);
    }
}
