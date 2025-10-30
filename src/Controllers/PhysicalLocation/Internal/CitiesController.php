<?php

namespace FWK\Controllers\PhysicalLocation\Internal;

use FWK\Core\Controllers\BaseJsonController;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\Resources\Loader;
use FWK\Enums\Parameters;
use FWK\Enums\Services;
use SDK\Core\Dtos\Element;
use SDK\Core\Dtos\ElementCollection;
use SDK\Core\Resources\BatchRequests;
use SDK\Services\Parameters\Groups\Geolocation\LocationParametersGroup;

/**
 * This is the banner CitiesController class.
 * This class extends BaseJsonController, see this class.
 *
 * @see BaseJsonController
 *
 * @package FWK\Controllers\PhysicalLocation\Internal
 */
class CitiesController extends BaseJsonController {

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FilterInputFactory::getUserLocationsPathParameters();
    }

    /**
     * This method returns the origin of the params (see FilterInputHandler::PARAMS_FROM_GET, FilterInputHandler::PARAMS_FROM_QUERY_STRING or FilterInputHandler::PARAMS_FROM_POST,...).
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     *
     * @see FilterInputHandler
     */
    protected function getOriginParams() {
        return FilterInputHandler::PARAMS_FROM_GET;
    }

    /**
     * This method launches the adequate actions against the SDK (through the FWK services) and returns the response data. 
     *
     * @return Element
     */
    protected function getResponseData(): ?Element {
        $geolocationService = Loader::service(Services::GEOLOCATION);
        $appliedParameters = [
            Parameters::COUNTRY_CODE => $this->getRequestParam(Parameters::COUNTRY_CODE, true),
            Parameters::PARENT_ID => $this->getRequestParam(Parameters::STATE, true),
            Parameters::LANGUAGE_CODE => $this->getRequestParam(Parameters::LANGUAGE_CODE, false, $this->getRoute()->getLanguage())
        ];
        $locationParametersGroup = new LocationParametersGroup();
        $geolocationService->generateParametersGroupFromArray($locationParametersGroup, $appliedParameters);
        return new class($geolocationService->getLocations($locationParametersGroup)) extends Element {
            private ?array $responseData = [];
            public function __construct(ElementCollection $data) {
                $this->responseData = $data->getItems();
            }
            public function jsonSerialize(): mixed {
                return $this->responseData;
            }
        };
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
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
    }
}
