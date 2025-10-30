<?php

namespace FWK\Services;

use FWK\Core\Resources\Registries\RegistryService;
use SDK\Services\SitemapService as SitemapServiceSDK;
use FWK\Services\Traits\ServiceTrait;

/**
 * This is the SitemapService class.
 * Remember that a service is an extension of a SDK model that allows to add additional
 * actions to a model request or create new methods to simplify some common requests.
 * In this case, the SitemapService extends the SDK\Services\SitemapService.
 *
 * @see LegalTextService
 *
 * @package FWK\Services
 */
class SitemapService extends SitemapServiceSDK {

    use ServiceTrait;

    private const REGISTRY_KEY = RegistryService::SITEMAP_SERVICE;

    private const ADD_FILTER_INTERVAL_PARAMETERS = [];

    private const ADD_FILTER_ID_VALUE_PARAMETERS = [];
}
