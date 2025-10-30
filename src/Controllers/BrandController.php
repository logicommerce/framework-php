<?php

namespace FWK\Controllers;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\Resources\Loader;
use FWK\Enums\Services;
use SDK\Core\Resources\BatchRequests;
use FWK\Core\FilterInput\FilterInputHandler;


/**
 * This is the base brand controller
 * This class extends BaseHtmlController, see this class.
 *
 * @controllerData: self::CONTROLLER_ITEM: \SDK\Dtos\Catalog\Brand
 * 
 * @twigContent: \themes\{{themeName}}\{{version}}\Content\Brand\default.html.twig
 * 
 * @RouteType: \SDK\Enums\RouteType::BRAND
 * 
 * @see BaseHtmlController
 *
 * @package FWK\Controllers
 */
class BrandController extends BaseHtmlController {

    /**
     * This method returns the origin of the params (see FilterInputHandler::PARAMS_FROM_GET, FilterInputHandler::PARAMS_FROM_QUERY_STRING or FilterInputHandler::PARAMS_FROM_POST,...).
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     *
     * @see FilterInputHandler
     */
    protected function getOriginParams() {
        return FilterInputHandler::PARAMS_FROM_QUERY_STRING;
    }

    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
        Loader::service(Services::BRAND)->addGetBrandById($requests, self::CONTROLLER_ITEM, $this->getRoute()->getId());
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
