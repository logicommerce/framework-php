<?php

namespace FWK\Services;

use FWK\Core\Resources\Registries\RegistryService;
use SDK\Services\LegalTextService as LegalTextServiceSDK;
use FWK\Services\Traits\ServiceTrait;

/**
 * This is the LegalTextService class.
 * Remember that a service is an extension of a SDK model that allows to add additional
 * actions to a model request or create new methods to simplify some common requests.
 * In this case, the LegalTextService extends the SDK\Services\LegalTextService.
 *
 * @see LegalTextService
 *
 * @package FWK\Services
 */
class LegalTextService extends LegalTextServiceSDK {

    use ServiceTrait;

    private const REGISTRY_KEY = RegistryService::LEGAL_TEXT_SERVICE;

    private const ADD_FILTER_INTERVAL_PARAMETERS = [];

    private const ADD_FILTER_ID_VALUE_PARAMETERS = [];
}
