<?php

namespace FWK\Services;

use FWK\Core\Resources\Registries\RegistryService;
use SDK\Services\ContactService as ContactServiceSDK;
use FWK\Services\Traits\ServiceTrait;

/**
 * This is the ContactService class.
 * Remember that a service is an extension of a SDK model that allows to add additional
 * actions to a model request or create new methods to simplify some common requests.
 * In this case, the ContactService extends the SDK\Services\ContactService.
 *
 * @see LegalTextService
 *
 * @package FWK\Services
 */
class ContactService extends ContactServiceSDK {

    use ServiceTrait;

    private const REGISTRY_KEY = RegistryService::CONTACT_SERVICE;

    private const ADD_FILTER_INTERVAL_PARAMETERS = [];

    private const ADD_FILTER_ID_VALUE_PARAMETERS = [];
}
