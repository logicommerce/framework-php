<?php

namespace FWK\Controllers\Geolocation\Internal;

use FWK\Core\Controllers\BaseJsonController;
use FWK\Enums\Services;
use FWK\Core\Resources\Loader;
use SDK\Core\Dtos\Element;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Common\Route;
use FWK\Core\FilterInput\FilterInputFactory;
use SDK\Services\Parameters\Groups\Geolocation\LocalitiesParametersGroup;
use FWK\Enums\Parameters;

/**
 * This is the AddCommentController class.
 * This class extends BaseJsonController (FWK\Core\Controllers\BaseJsonController), see this class.
 * <br>This controller returns the subdivisions of a country that fit the search criteria. The level
 * of the subdivision will the one corresponding to the zip code.
 *
 * @see BaseJsonController
 *
 * @package FWK\Controllers\Geolocation\Internal
 */
class GetLocationsLocalitiesController extends BaseJsonController {

    /**
     * This attribute is an LocationPostalCodesParametersGroup instance needed to communicate with 
     * the SDK.
     */
    private ?LocalitiesParametersGroup $getLocalitiesParameters = null;

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->getLocalitiesParameters = new LocalitiesParametersGroup();
    }

    /**
     * This method returns an array of parameters, indicating in each node the parameter name and 
     * the filter to apply. This method must be overridden in extended controllers to add new 
     * parameters to self::requestParams.
     * 
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FilterInputFactory::getLocationsLocalitiesParameters();
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
                $this->getLocalitiesParameters, $parameters
            )
        ];
        return new class($this->getLocalitiesParameters) extends Element {

            private ?LocalitiesParametersGroup $getLocalitiesParameters = null;

            public function __construct(LocalitiesParametersGroup $getLocalitiesParameters) {
                $this->getLocalitiesParameters = $getLocalitiesParameters;
            }

            public function jsonSerialize(): mixed {
                /** @var GeolocationService $service */
                $service = Loader::service(Services::GEOLOCATION);
                return $service->getLocalities($this->getLocalitiesParameters)->toArray();
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
