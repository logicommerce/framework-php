<?php

namespace FWK\Services;

use FWK\Core\Resources\Registries\RegistryService;
use SDK\Services\FormService as FormServiceSDK;
use FWK\Services\Traits\ServiceTrait;

/**
 * This is the FormService class.
 * Remember that a service is an extension of a SDK model that allows to add additional
 * actions to a model request or create new methods to simplify some common requests.
 * In this case, the FormService extends the SDK\Services\FormService.
 *
 * @see FormService
 *
 * @package FWK\Services
 */
class FormService extends FormServiceSDK {
    use ServiceTrait;
    
    private const REGISTRY_KEY = RegistryService::FORM_SERVICE;
    
    private const ADD_FILTER_INTERVAL_PARAMETERS = [];
    
    private const ADD_FILTER_ID_VALUE_PARAMETERS = [];
    
    
}
