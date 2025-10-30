<?php

namespace FWK\Controllers\Checkout;

use FWK\Core\Controllers\Traits\CheckoutRedirectTrait;
use SDK\Core\Resources\BatchRequests;
use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\Resources\Loader;
use FWK\Enums\Services;
use SDK\Dtos\Common\Route;

/**
 * This is the Express Checkout Cancel Controller.
 *
 * @see CustomerController
 *
 * @package FWK\Controllers\Checkout
 */
class ExpressCheckoutCancelController extends BaseHtmlController {
    use CheckoutRedirectTrait {
        __construct as __constructCheckoutRedirectTrait;
    }

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        Loader::service(Services::BASKET)->validateExpressCheckout();
        $this->__constructCheckoutRedirectTrait($route);
    }

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

    /**
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     *
     */
    protected function setControllerBaseData(): void {
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
