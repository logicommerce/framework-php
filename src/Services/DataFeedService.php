<?php

namespace FWK\Services;

use FWK\Core\Resources\Registries\RegistryService;
use SDK\Services\DataFeedService as DataFeedServiceSDK;
use FWK\Services\Traits\ServiceTrait;

/**
 * This is the DataFeedService class.
 * Remember that a service is an extension of a SDK model that allows to add additional
 * actions to a model request or create new methods to simplify some common requests.
 * In this case, the DataFeedService extends the SDK\Services\DataFeedService.
 *
 * @see LegalTextService
 *
 * @package FWK\Services
 */
class DataFeedService extends DataFeedServiceSDK {

    use ServiceTrait;

    private const REGISTRY_KEY = RegistryService::DATA_FEED_SERVICE;

    private const ADD_FILTER_INTERVAL_PARAMETERS = [];

    private const ADD_FILTER_ID_VALUE_PARAMETERS = [];
}
