<?php

namespace FWK\Controllers\Resources\Internal;

use FWK\Core\Controllers\BaseJsController;
use SDK\Core\Resources\BatchRequests;
use FWK\Core\FilterInput\FilterInputFactory;

/**
 * This is the Customize js controller.
 * This class extends BaseJsController (FWK\Core\Controllers\BaseJsController), see this class.
 *
 * @see BaseJsController
 *
 * @package FWK\Controllers\Resources\Internal
 */
class CustomizeJsController extends BaseJsController {

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FilterInputFactory::getCustomizeJsParameters();
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
