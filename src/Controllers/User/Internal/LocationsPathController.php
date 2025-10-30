<?php

namespace FWK\Controllers\User\Internal;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\Resources\Loader;
use FWK\Enums\Services;
use FWK\Services\GeolocationService;
use SDK\Core\Dtos\ElementCollection;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Common\Route;
use SDK\Services\Parameters\Groups\Geolocation\LocationPathParametersGroup;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Enums\Parameters;

/**
 * This is the LocationsPathController controller class.
 * This class extends FWK\Core\Controllers\BaseHtmlController, see this class.
 *
 * @see BaseHtmlController
 *
 * @package FWK\Controllers\User\Internal
 */
class LocationsPathController extends BaseHtmlController {

    private ?GeolocationService $geolocationService = null;

    private ?LocationPathParametersGroup $locationPathParametersGroup = null;

    private ?ElementCollection $locationsList = null;

    public const FIELD_NAME = 'fieldName';
    public const SELECTED_IDS = 'selectedIds';
    public const COUNTRY_CODE = 'countryCode';
    public const COUNTRY = 'country';
    public const STATE = 'state';
    public const CITY = 'city';
    public const POSTAL_CODE = 'postalCode';


    /**
     * Constructor method.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->geolocationService = Loader::service(Services::GEOLOCATION);
        $this->locationPathParametersGroup = new LocationPathParametersGroup();
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
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FilterInputFactory::getUserLocationsPathParameters();
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
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     *
     */
    protected function setControllerBaseData(): void {
        $this->geolocationService->generateParametersGroupFromArray($this->locationPathParametersGroup, $this->getRequestParams());
        $this->locationsList = $this->geolocationService->getLocationPath($this->locationPathParametersGroup);

        if (!empty($this->getRequestParam(Parameters::LOCATION_ID, false, '')) && empty($this->locationsList->getItems())) {
            $this->breakControllerProcess('A request with locationId must return at least one result', CommerceException::CONTROLLER_UNDEFINED_CRITICAL_DATA);
        }

        $this->setDataValue(self::CONTROLLER_ITEM, [
            self::FIELD_NAME => $this->getRequestParam(Parameters::FIELD_NAME),
            self::COUNTRY_CODE => $this->getRequestParam(Parameters::COUNTRY_CODE),
            self::SELECTED_IDS => $this->locationsList,
            self::COUNTRY => $this->getRequestParam(Parameters::COUNTRY, false, ''),
            self::STATE => $this->getRequestParam(Parameters::STATE, false, ''),
            self::CITY => $this->getRequestParam(Parameters::CITY, false, ''),
            self::POSTAL_CODE => $this->getRequestParam(Parameters::POSTAL_CODE, false, '')
        ]);
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
