<?php

namespace FWK\Controllers;

use FWK\Core\Controllers\BaseHtmlController;
use SDK\Core\Resources\BatchRequests;

/**
 * This is the ErrorController Class.
 * This class extends BaseHtmlController, see this class.
 *
 * @twigContent: \themes\{{themeName}}\{{version}}\Content\Error\default.html.twig
 * 
 * @RouteType: \FWK\Enums\RouteType::ERROR
 * 
 * @see BaseHtmlController
 *
 * @package FWK\Controllers
 */
class ErrorController extends BaseHtmlController {

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
