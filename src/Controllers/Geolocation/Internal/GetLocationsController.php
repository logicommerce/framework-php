<?php

namespace FWK\Controllers\Geolocation\Internal;

use FWK\Core\Controllers\BaseJsonController;
use FWK\Enums\Services;
use FWK\Core\Resources\Loader;
use SDK\Core\Dtos\Element;
use SDK\Core\Resources\BatchRequests;
use SDK\Services\Parameters\Groups\Geolocation\LocationParametersGroup;
use SDK\Dtos\Common\Route;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Enums\Parameters;
use FWK\Services\GeolocationService;

/**
 * This is the GetLocationsController class.
 * This class extends BaseJsonController (FWK\Core\Controllers\BaseJsonController), see this class.
 * <br>This controller returns the subdivisions of a country. The level of the returned subdivision
 * will depend on the parentId parameter. If this parameter is not sent, the level 1 administrative
 * subdivision is returned.
 *
 * @see BaseJsonController
 *
 * @package FWK\Controllers\Geolocation\Internal
 */
class GetLocationsController extends BaseJsonController {

    /**
     * This attribute is an LocationParametersGroup instance needed to communicate with the SDK.
     */
    private ?LocationParametersGroup $getLocationParameters = null;

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->getLocationParameters = new LocationParametersGroup();
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the
     * filter to apply. This function must be override in extended controllers to add new parameters
     * to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FilterInputFactory::getLocationsParameters();
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
                $this->getLocationParameters, $parameters
            )
        ];

        return new class($this->getLocationParameters) extends Element {

            private ?LocationParametersGroup $getLocationParameters = null;

            public function __construct(LocationParametersGroup $getLocationParameters) {
                $this->getLocationParameters = $getLocationParameters;
            }

            public function jsonSerialize(): mixed {
                /** @var GeolocationService $geolocationService */
                $geolocationService = Loader::service(Services::GEOLOCATION);
                return $geolocationService->getLocations($this->getLocationParameters)->toArray();
            }
        };
    }

    /**
     * This method is the one in charge of defining all the data batch requests that are needed for
     * the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request where the method will add the batch requests.
     * @return void
     */
    protected function setBatchData(BatchRequests $request): void {
    }

    /**
     * This method is the one in charge of defining all the data batch requests that are basic for
     * the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
    }
}
