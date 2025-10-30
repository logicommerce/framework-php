<?php

namespace FWK\Controllers;

use FWK\Core\Controllers\BaseHtmlController;
use SDK\Core\Resources\BatchRequests;
use FWK\Enums\Services;
use FWK\Core\Resources\Loader;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Dtos\Documents\Document;
use FWK\Enums\Parameters;

/**
 * This is the base order controller
 * This class extends BaseHtmlController, see this class.
 *
 * @controllerData: self::CONTROLLER_ITEM: \SDK\Dtos\Documents\Order or null
 * 
 * @twigContent: \themes\{{themeName}}\{{version}}\Content\Order\default.html.twig
 * 
 * @RouteType: \SDK\Enums\RouteType::ORDER
 * 
 * @see BaseHtmlController
 *
 * @package FWK\Controllers
 */
class OrderController extends BaseHtmlController {

    private const ORDER = 'order';

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FilterInputFactory::getTokenParameter();
    }

    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
        if ($this->getRequestParam(Parameters::TOKEN) !== null) {
            Loader::service(Services::ORDER)->addGetOrder($requests, self::ORDER, $this->getRoute()->getId(), $this->getRequestParam(Parameters::TOKEN));
        } else {
            $this->setDataValue(self::CONTROLLER_ITEM, null);
        }
    }

    /**
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     *
     */
    protected function setControllerBaseData(): void {
        if ($this->getRequestParam(Parameters::TOKEN) !== null) {
            $order = $this->getControllerData(self::ORDER);
            $order = Document::fillFromParent($order);
            $this->setDataValue(self::CONTROLLER_ITEM, $order);
            $this->deleteControllerData(self::ORDER);
        }
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
