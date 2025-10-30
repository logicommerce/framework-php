<?php

namespace FWK\Controllers\Geolocation\Internal;

use FWK\Core\Controllers\BaseJsonController;
use FWK\Enums\Services;
use FWK\Core\Resources\Loader;
use SDK\Core\Dtos\Element;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Common\Route;
use FWK\Core\FilterInput\FilterInputFactory;
use SDK\Services\Parameters\Groups\Geolocation\LocationPathParametersGroup;
use FWK\Enums\Parameters;

/**
 * This is the AddCommentController class.
 * This class extends BaseJsonController (FWK\Core\Controllers\BaseJsonController), see this class.
 * <br>This controller returns the full path of a country's subdivisions. The number of elements 
 * will depend on the administrative level to which the value of the locationId parameter belongs,
 * taking into account that the first element will always be the one corresponding to the
 * administrative subdivision of level 1.
 *
 * @see BaseJsonController
 *
 * @package FWK\Controllers\Geolocation\Internal
 */
class GetLocationsPathController extends BaseJsonController {

    /**
     * This attribute is an LocationPathParametersGroup instance needed to communicate with the SDK.
     */
    private ?LocationPathParametersGroup $getLocationPathParameters = null;

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->getLocationPathParameters = new LocationPathParametersGroup();
    }

    /**
     * This method returns an array of parameters, indicating in each node the parameter name and
     * the filter to apply. This function must be overridden in extended controllers toadd new
     * parameters to self::requestParams.  
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FilterInputFactory::getLocationsPathParameters();
    }

    /**
     * This method launches the adequate actions against the SDK (through the FWK services) and 
     * returns the response data. 
     *
     * @return Element
     */
    protected function getResponseData(): ?Element {
        $parameters = $this->getRequestParams();
        $parameters[Parameters::LANGUAGE_CODE] = $this->getRequestParam(
            Parameters::LANGUAGE_CODE, false, $this->getRoute()->getLanguage()
        );

        /** @var GeolocationService $geolocationService */
        $geolocationService = Loader::service(Services::GEOLOCATION);
        $this->appliedParameters = [
            $geolocationService->generateParametersGroupFromArray(
                $this->getLocationPathParameters, $parameters
            )
        ];

        return new class($this->getLocationPathParameters) extends Element {

            private ?LocationPathParametersGroup $getLocationPathParameters = null;

            public function __construct(LocationPathParametersGroup $getLocationPathParameters) {
                $this->getLocationPathParameters = $getLocationPathParameters;
            }

            public function jsonSerialize(): mixed {
                /** @var GeolocationService $service */
                $service = Loader::service(Services::GEOLOCATION);
                return $service->getLocationPath($this->getLocationPathParameters)->toArray();
            }
        };
    }

    /**
     * This method is the one in charge of defining all the data batch requests that are needed for
     * the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     * @return void
     */
    protected function setBatchData(BatchRequests $request): void {
    }

    /**
     * This method is the one in charge of defining all the data batch requests that are basic for
     * the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
    }
}
