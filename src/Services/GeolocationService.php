<?php

namespace FWK\Services;

use FWK\Core\Resources\Registries\RegistryService;
use SDK\Services\GeolocationService as GeolocationServiceSDK;
use FWK\Services\Traits\ServiceTrait;

/**
 * This is the GeolocationService class.
 * Remember that a service is an extension of a SDK model that allows to add additional
 * actions to a model request or create new methods to simplify some common requests.
 * In this case, the GeolocationService extends the SDK\Services\GeolocationService.
 *
 * @see GeolocationService
 *
 * @package FWK\Services
 */
class GeolocationService extends GeolocationServiceSDK {
    use ServiceTrait;
    
    private const REGISTRY_KEY = RegistryService::GEOLOCATION_SERVICE;
    
    private const ADD_FILTER_INTERVAL_PARAMETERS = [];
    
    private const ADD_FILTER_ID_VALUE_PARAMETERS = [];
    
    
}
