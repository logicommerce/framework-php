<?php

namespace FWK\Controllers\Checkout\Internal;

use FWK\Core\Controllers\BaseJsonController;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\Resources\Loader;
use FWK\Enums\Parameters;
use FWK\Enums\Services;
use FWK\Services\OrderService;
use SDK\Core\Dtos\Element;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Common\Route;
use SDK\Services\Parameters\Groups\Document\PickupPointProvidersParametersGroup;

/**
 * This is the SetPickupPointProviders controller class.
 * This class extends FWK\Core\Controllers\BaseJsonController, see this class.
 *
 * @see BaseJsonController
 *
 * @package FWK\Controllers\Checkout\Internal
 */
class SetPickupPointProvidersController extends BaseJsonController {

    private ?OrderService $orderService = null;

    protected ?PickupPointProvidersParametersGroup $pickupPointProvidersParametersGroup = null;

    /**
     * Constructor method.
     * 
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->orderService = Loader::service(Services::ORDER);
        $this->pickupPointProvidersParametersGroup = new PickupPointProvidersParametersGroup();
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FilterInputFactory::getCountryCodeParameter();
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
     * This method launches the adequate actions against the SDK (through the FWK services) and returns the response data. 
     *
     * @return Element
     */
    protected function getResponseData(): ?Element {
        if (!empty($this->getRequestParam(Parameters::COUNTRY_CODE))) {
            $this->pickupPointProvidersParametersGroup->setCountryCode($this->getRequestParam(Parameters::COUNTRY_CODE));
        }
        return $this->orderService->getPickupPointProviders($this->pickupPointProvidersParametersGroup);
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
