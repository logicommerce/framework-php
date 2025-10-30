<?php

namespace FWK\Controllers\PhysicalLocation;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Enums\Services;
use FWK\Core\Resources\Loader;
use FWK\Core\Resources\Session;
use FWK\Services\PhysicalLocationsService;
use SDK\Application;
use SDK\Core\Dtos\ElementCollection;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Common\Route;
use SDK\Services\Parameters\Groups\PhysicalLocationParametersGroup;

/**
 * This is the physical location stores controller class.
 * This class extends PhysicalLocationController (FWK\Controllers\PhysicalLocation\PhysicalLocationController), see this class.
 *
 * @see PhysicalLocationController
 *
 * @package FWK\Controllers\PhysicalLocation
 */
class PhysicalLocationStoresController extends BaseHtmlController {

    protected PhysicalLocationsService $physicalLocationsService;

    protected PhysicalLocationParametersGroup $physicalLocationParametersGroup;

    protected array $physicalLocationsFilter = [];

    protected bool $getAllPhysicalLocations = true;

    /**
     *
     * @see \FWK\Core\Controllers\Controller::__construct
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->physicalLocationsService = Loader::service(Services::PHYSICAL_LOCATIONS);
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
     * This method initialize applied parameters, runs previously to run preSendControllerBaseBatchData
     *
     */
    protected function initializeAppliedParameters(): void {
        $this->physicalLocationsFilter = $this->physicalLocationsService->generateParametersGroupFromArray($this->physicalLocationParametersGroup, $this->getRequestParams());
    }

    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
        if (!$this->getAllPhysicalLocations) {
            $this->physicalLocationsService->addGetPhysicalLocations($requests, self::CONTROLLER_ITEM, $this->physicalLocationParametersGroup);
        }
    }

    /**
     * This method runs after the batch requests (defined in the setBatchData methods) are resolved,
     * so here you can work with the response of the batch requests and calculate and set more needed data.
     *
     * @return void
     */
    protected function setControllerBaseData(): void {
        if ($this->getAllPhysicalLocations) {
            $this->physicalLocationParametersGroup->setLanguageCode(Session::getInstance()->getGeneralSettings()->getLanguage());
            $this->setDataValue(self::CONTROLLER_ITEM, $this->physicalLocationsService->getAllPhysicalLocations($this->physicalLocationParametersGroup));
        }
        $this->setDataValue('countries', $this->getCountries());
        $this->setDataValue('defaultCountry', $this->getDefaultCountry());
        $this->setDataValue('physicalLocationsFilter', $this->physicalLocationsFilter);
    }

    /**
     * Returns the countries to show in the selector countries
     *
     * @return NULL|ElementCollection
     */
    protected function getCountries(): ?ElementCollection {
        return Application::getInstance()->getCountriesSettings($this->getRoute()->getLanguage());
    }

    /**
     * Returns the default country to show 
     * 
     * @return string
     */
    protected function getDefaultCountry(): string {
        return Session::getInstance()->getGeneralSettings()->getCountry();
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
