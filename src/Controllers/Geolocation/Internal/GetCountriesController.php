<?php

namespace FWK\Controllers\Geolocation\Internal;

use FWK\Core\Controllers\BaseJsonController;
use FWK\Enums\Services;
use FWK\Core\Resources\Loader;
use SDK\Core\Dtos\Element;
use SDK\Core\Resources\BatchRequests;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Enums\Parameters;
use FWK\Services\GeolocationService;

/**
 * This is the GetCountriesController class.
 * This class extends BaseJsonController (FWK\Core\Controllers\BaseJsonController), see this class.
 * <br>This controller returns all the available countries of the platform.
 *
 * @see BaseJsonController
 *
 * @package FWK\Controllers\Geolocation\Internal
 */
class GetCountriesController extends BaseJsonController {

    /**
     * This attribute is a CountriesParametersGroup instance needed to communicate with the SDK.
     */
    private ?string $languageCode = null;

    /**
     * This method returns an array of parameters, indicating in each node the parameter name and
     * the filter to apply. This method must be overridden in extended controllers to add new 
     * parameters to self::requestParams.  
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FilterInputFactory::getCountriesParameters();
    }

    /**
     * This method launches the adequate actions against the SDK (through the FWK services) and 
     * returns the response data. 
     *
     * @return Element
     */
    protected function getResponseData(): ?Element {
        $this->appliedParameters = [];
        $this->languageCode = $this->getRequestParam(
            Parameters::LANGUAGE_CODE, false, $this->getRoute()->getLanguage()
        );
        $this->appliedParameters[Parameters::LANGUAGE_CODE] = $this->languageCode;

        return new class($this->languageCode) extends Element {

            private ?string $languageCode = null;

            public function __construct(?string $languageCode) {
                $this->languageCode = $languageCode;
            }

            public function jsonSerialize(): mixed {
                /** @var GeolocationService $geolocationService */
                $geolocationService = Loader::service(Services::GEOLOCATION);
                return $geolocationService->getCountries($this->languageCode)->toArray();
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
