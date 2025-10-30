<?php

namespace FWK\Controllers\PhysicalLocation;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Enums\Services;
use FWK\Core\Resources\Loader;
use SDK\Core\Resources\BatchRequests;
use SDK\Services\Parameters\Groups\Geolocation\LocationParametersGroup;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Enums\Parameters;
use SDK\Dtos\Common\Route;
use FWK\Services\GeolocationService;

/**
 * This is the physical locations states controller class.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @see BaseHtmlController
 *
 * @package FWK\Controllers\PhysicalLocation
 */
class PhysicalLocationStatesController extends BaseHtmlController {

    private array $appliedParameters = [];

    private GeolocationService $geolocationService;

    private LocationParametersGroup $locationParametersGroup;

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->geolocationService = Loader::service(Services::GEOLOCATION);
        $this->locationParametersGroup = new LocationParametersGroup();
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FilterInputFactory::getLocationsParameters();
    }

    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
    }

    /**
     * This private method sends the batch requests of the controller to the SDK to obtain the data and returns the batch result
     *
     * @return array with the result of the batch request
     */
    protected function setControllerBaseData(): void {
        $parameters = $this->getRequestParams();
        $parameters[Parameters::LANGUAGE_CODE] = $this->getRequestParam(Parameters::LANGUAGE_CODE, false, $this->getRoute()->getLanguage());
        $this->appliedParameters = [
            Loader::service(Services::GEOLOCATION)->generateParametersGroupFromArray($this->locationParametersGroup, $parameters)
        ];
        $this->setDataValue(self::CONTROLLER_ITEM, $this->geolocationService->getLocations($this->locationParametersGroup));
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
