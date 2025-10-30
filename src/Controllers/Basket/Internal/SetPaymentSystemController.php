<?php

namespace FWK\Controllers\Basket\Internal;

use FWK\Core\Controllers\BaseJsonController;
use FWK\Core\Controllers\Traits\SetPaymentSystemTrait;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\Resources\Loader;
use FWK\Enums\LanguageLabels;
use FWK\Enums\Services;
use FWK\Services\BasketService;
use SDK\Core\Dtos\Element;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Common\Route;

/**
 * This is the SetPaymentSystemController controller class.
 * This class extends FWK\Core\Controllers\BaseJsonController, see this class.
 *
 * @controllerData: 
 *  <p>self::RESPONSE[self::DATA] => \SDK\Dtos\Basket\Basket</p>
 * 
 * @RouteType: \FWK\Enums\RouteTypes\InternalBasket::SET_PAYMENT_SYSTEM
 *
 * @responseMessageSuccess = \FWK\Enums\LanguageLabels\LanguageLabels::PAYMENT_SYSTEM_ADD_OK;
 * @responseMessageError = \FWK\Enums\LanguageLabels\LanguageLabels::PAYMENT_SYSTEM_ADD_ERROR;
 *
 * @package FWK\Controllers\Basket\Internal
 */
class SetPaymentSystemController extends BaseJsonController {
    use SetPaymentSystemTrait;

    private ?BasketService $basketService = null;

    /**
     * Constructor method.
     * 
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->basketService = Loader::service(Services::BASKET);
        $this->responseMessage = $this->language->getLabelValue(LanguageLabels::PAYMENT_SYSTEM_ADD_OK, $this->responseMessage);
        $this->responseMessageError = $this->language->getLabelValue(LanguageLabels::PAYMENT_SYSTEM_ADD_ERROR, $this->responseMessage);
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return array_merge(FilterInputFactory::getEditPaymentSystemParameters());
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
        return FilterInputHandler::PARAMS_FROM_POST_DATA_OBJECT;
    }

    /**
     * This method launches the adequate actions against the SDK (through the FWK services) and returns the response data. 
     *
     * @return Element
     */
    protected function getResponseData(): ?Element {
        return $this->getPaymentSystemResponseData();
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
