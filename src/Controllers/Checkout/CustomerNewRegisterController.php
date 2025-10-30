<?php

namespace FWK\Controllers\Checkout;

use SDK\Core\Resources\BatchRequests;

/**
 * This is the checkout customer new register controller.
 * This class extends CustomerController (FWK\Controllers\Checkout\CustomerController), see this class.
 *
 * @see CustomerController
 *
 * @package FWK\Controllers\Checkout
 */
class CustomerNewRegisterController extends CustomerController {

    /**
     * This private method sends the batch requests of the controller to the SDK to obtain the data and returns the batch result
     *
     * @return array with the result of the batch request
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
