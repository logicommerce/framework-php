<?php

namespace FWK\Controllers\PhysicalLocation;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Enums\Services;
use FWK\Core\Resources\Loader;
use FWK\Enums\Parameters;
use SDK\Core\Resources\BatchRequests;

/**
 * This is the physical locations countries controller class.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @see BaseHtmlController
 *
 * @package FWK\Controllers\PhysicalLocation
 */
class PhysicalLocationCountriesController extends BaseHtmlController {

    private array $appliedParameters = [];

    private string $languageCode;

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
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     *
     */
    protected function setControllerBaseData(): void {
        $this->appliedParameters = [];
        $this->languageCode = $this->getRequestParam(Parameters::LANGUAGE_CODE, false, $this->getRoute()->getLanguage());
        $this->appliedParameters[Parameters::LANGUAGE_CODE] = $this->languageCode;
        $this->setDataValue(self::CONTROLLER_ITEM, Loader::service(Services::GEOLOCATION)->getCountries($this->languageCode));
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
