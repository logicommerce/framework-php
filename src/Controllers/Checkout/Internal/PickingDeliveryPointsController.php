<?php

namespace FWK\Controllers\Checkout\Internal;

use SDK\Core\Resources\BatchRequests;
use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\Controllers\Traits\SetPhysicalLocationsFromDeliveries;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\Resources\Loader;
use FWK\Enums\Parameters;
use FWK\Enums\Services;
use FWK\Services\BasketService;
use SDK\Dtos\Common\Route;
use SDK\Services\Parameters\Groups\Basket\DeliveriesParametersGroup;
use SDK\Services\Parameters\Groups\Basket\ProviderPickupPointPickingDeliveriesParametersGroup;

/**
 * This is the Picking delivery points controller.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @see BaseHtmlController
 *
 * @package FWK\Controllers\Checkout\Internal
 */
class PickingDeliveryPointsController extends BaseHtmlController {
    use SetPhysicalLocationsFromDeliveries;

    protected const PICKING_DELIVERIES = 'pickingDeliveries';

    protected const PHYSICAL_LOCATIONS_FILTER = 'physicalLocationsFilter';

    protected const PHYSICAL_LOCATIONS = 'physicalLocations';

    protected const DEFAULT_PHYSICAL_LOCATION_ID = 'defaultPhysicalLocationId';

    protected const IS_PROVIDER_PICKUP_POINT_REQUEST = 'isProviderPickupPointRequest';

    protected ProviderPickupPointPickingDeliveriesParametersGroup $providerPickupPointPickingDeliveriesParametersGroup;

    protected DeliveriesParametersGroup $deliveriesParametersGroup;

    protected bool $isProviderPickupPointRequest = false;

    protected ?BasketService $basketService = null;

    protected array $physicalLocationsFilter = [];

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->basketService = Loader::service(Services::BASKET);
        $this->providerPickupPointPickingDeliveriesParametersGroup = new ProviderPickupPointPickingDeliveriesParametersGroup();
        $this->deliveriesParametersGroup = new DeliveriesParametersGroup();
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FilterInputFactory::getPickingDeliveryPointsParameters();
    }

    /**
     * This method initialize applied parameters, runs previously to run preSendControllerBaseBatchData
     *
     */
    protected function initializeAppliedParameters(): void {
        $this->isProviderPickupPointRequest = !is_null($this->getRequestParam(Parameters::PICKUP_POINT_PROVIDER_ID));
        if ($this->isProviderPickupPointRequest) {
            $this->physicalLocationsFilter = $this->basketService->generateParametersGroupFromArray($this->providerPickupPointPickingDeliveriesParametersGroup, $this->getRequestParams());
        } else {
            $this->physicalLocationsFilter = $this->basketService->generateParametersGroupFromArray($this->deliveriesParametersGroup, $this->getRequestParams());
        }
    }

    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
        if ($this->isProviderPickupPointRequest) {
            $this->basketService->addGetProviderPickupPointPickingDeliveries($requests, self::PICKING_DELIVERIES, $this->providerPickupPointPickingDeliveriesParametersGroup);
        } else {
            $this->basketService->addGetPhysicalLocationPickingDeliveries($requests, self::PICKING_DELIVERIES, $this->deliveriesParametersGroup);
        }
    }

    /**
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     */
    final protected function setControllerBaseData(): void {
        $deliveries = $this->getControllerData(self::PICKING_DELIVERIES);
        $this->setPhysicalLocationsFromDeliveries($deliveries, self::PHYSICAL_LOCATIONS, self::DEFAULT_PHYSICAL_LOCATION_ID);
        $this->setDataValue(self::PHYSICAL_LOCATIONS_FILTER, $this->physicalLocationsFilter);
        $this->setDataValue(self::IS_PROVIDER_PICKUP_POINT_REQUEST, $this->isProviderPickupPointRequest);
    }

    /**
     * This method runs after the batch requests (defined in the setBatchData methods) are resolved,
     * so here you can work with the response of the batch requests and calculate and set more needed data.
     *
     * @param array $additionalData
     *              Set additiona data to the controller data
     * 
     * @return void
     */
    protected function setData(array $additionalData = []): void {
    }

    /**
     * This method is the one in charge of defining all the data batch requests that
     * are needed for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     * @return void
     */
    protected function setBatchData(BatchRequests $request): void {
    }
}
