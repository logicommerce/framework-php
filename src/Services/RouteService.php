<?php

namespace FWK\Services;

use FWK\Core\Resources\Registries\RegistryService;
use FWK\Core\Resources\Utils;
use SDK\Services\RouteService as RouteServiceSDK;
use SDK\Core\Enums\Resource;
use SDK\Dtos\Common\Route;
use FWK\Services\Traits\ServiceTrait;
use SDK\Core\Resources\Environment;

/**
 * This is the RouteService class.
 * Remember that a service is an extension of a SDK model that allows to add additional
 * actions to a model request or create new methods to simplify some common requests.
 * In this case, the RouteService extends the SDK\Services\RouteService.
 *
 * @see RouteService::getStoreURL()
 *
 * @see RouteService
 *
 * @package FWK\Services
 */
class RouteService extends RouteServiceSDK {
    use ServiceTrait;

    private const REGISTRY_KEY = RegistryService::ROUTE_SERVICE;

    private const ADD_FILTER_INTERVAL_PARAMETERS = [];

    private const ADD_FILTER_ID_VALUE_PARAMETERS = [];

    /**
     * This method returns the storeURL Route.
     * 
     * @return array
     */
    public function getStoreURL(?Route $route = null): array {
        if (!is_null($route)) {
            $params = $this->getParams(
                parse_url($route->getUrlPrefix(), PHP_URL_PATH),
                parse_url($route->getUrlPrefix(), PHP_URL_HOST),
                parse_url($route->getUrlPrefix(), PHP_URL_SCHEME)
            );
        } else {
            $params = $this->getParams('');
        }

        $route = $this->getResourceElement(Route::class, Resource::ROUTE, $params);

        if ($route->getStatus() === 301) {
            $route = $this->getResourceElement(Route::class, Resource::ROUTE, $this->getParams(
                parse_url($route->getRedirectUrl(), PHP_URL_PATH),
                parse_url($route->getRedirectUrl(), PHP_URL_HOST),
                parse_url($route->getRedirectUrl(), PHP_URL_SCHEME)
            ));
        }

        $storeURL = Utils::interceptURL($route->getUrlPrefix());
        $response['route'] = $route;
        $response['storeURL'] = $storeURL;

        return $response;
    }
}
