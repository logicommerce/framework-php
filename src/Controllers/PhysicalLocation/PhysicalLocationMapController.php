<?php

namespace FWK\Controllers\PhysicalLocation;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Enums\Services;
use FWK\Core\Resources\Loader;
use FWK\Services\GeolocationService;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Common\Route;
use SDK\Services\Parameters\Groups\PhysicalLocationParametersGroup;

/**
 * This is the physical locations map controller class.
 * This class extends PhysicalLocationController (FWK\Controllers\PhysicalLocation\PhysicalLocationController), see this class.
 *
 * @see PhysicalLocationController
 *
 * @package FWK\Controllers\PhysicalLocation
 */
class PhysicalLocationMapController extends BaseHtmlController {

    private GeolocationService $geolocationService;

    private PhysicalLocationParametersGroup $physicalLocationParametersGroup;

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->geolocationService = Loader::service(Services::GEOLOCATION);
        $this->physicalLocationParametersGroup = new PhysicalLocationParametersGroup();
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FilterInputFactory::getPhysicalLocationParameters();
    }

    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
        $this->geolocationService->generateParametersGroupFromArray($this->physicalLocationParametersGroup, $this->getRequestParams());
        $this->physicalLocationParametersGroup->setVisibleOnMap(true);
        Loader::service(Services::PHYSICAL_LOCATIONS)->addGetPhysicalLocations($requests, self::CONTROLLER_ITEM, $this->physicalLocationParametersGroup);
    }

    /**
     * This private method sends the batch requests of the controller to the SDK to obtain the data and returns the batch result
     *
     * @return array with the result of the batch request
     */
    protected function setBatchData(BatchRequests $request): void {
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
}
