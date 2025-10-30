<?php

namespace FWK\Services;

use FWK\Core\Resources\Registries\RegistryService;
use SDK\Services\OrderService as OrderServiceSDK;
use FWK\Services\Traits\ServiceTrait;
use SDK\Core\Dtos\ElementCollection;
use SDK\Dtos\Catalog\PhysicalLocation;
use SDK\Services\Parameters\Groups\PaginableParametersGroup;

/**
 * This is the OrderService class.
 * Remember that a service is an extension of a SDK model that allows to add additional
 * actions to a model request or create new methods to simplify some common requests.
 * In this case, the OrderService extends the SDK\Services\OrderService.
 *
 * @see OrderService
 *
 * @package FWK\Services
 */
class OrderService extends OrderServiceSDK {
    use ServiceTrait;

    private const REGISTRY_KEY = RegistryService::ORDER_SERVICE;

    private const ADD_FILTER_INTERVAL_PARAMETERS = [];

    private const ADD_FILTER_ID_VALUE_PARAMETERS = [];


    /**
     * Returns all return points identified by the given order id
     *
     * @param int $id
     *
     * @return ElementCollection|NULL
     */
    public function getAllReturnPoints(int $id, PaginableParametersGroup $params = null): ?ElementCollection {
        if (is_null($params)) {
            $params = new PaginableParametersGroup();
        }
        return $this->getAllElementCollectionItems(PhysicalLocation::class, 'ReturnPoints', $params, $id);
    }
}
