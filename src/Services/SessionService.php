<?php

namespace FWK\Services;

use FWK\Core\Resources\Language;
use FWK\Core\Resources\Registries\RegistryService;
use SDK\Services\SessionService as SessionServiceSDK;
use FWK\Services\Traits\ServiceTrait;
use SDK\Dtos\Basket\Basket;

/**
 * This is the SessionService class.
 * Remember that a service is an extension of a SDK model that allows to add additional
 * actions to a model request or create new methods to simplify some common requests.
 * In this case, the SessionService extends the SDK\Services\SessionService.
 *
 * @see LegalTextService
 *
 * @package FWK\Services
 */
class SessionService extends SessionServiceSDK {

    use ServiceTrait;

    private const REGISTRY_KEY = RegistryService::SESSION_SERVICE;

    private const ADD_FILTER_INTERVAL_PARAMETERS = [];

    private const ADD_FILTER_ID_VALUE_PARAMETERS = [];

    /**
     * Sets a new navigation language for the current session
     *
     * @param string $languageCode
     *
     * @return Basket|NULL
     */
    public function setLanguage(string $languageCode): ?Basket {
        $response = parent::setLanguage($languageCode);
        if (is_null($response->getError())) {
            Language::reloadInstance($languageCode);
        }
        return $response;
    }
}
