<?php

namespace FWK\Controllers\Checkout;

use FWK\Core\Controllers\Traits\CheckoutRedirectTrait;
use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\Exceptions\CommerceException;
use SDK\Core\Resources\BatchRequests;
use FWK\Enums\Services;
use FWK\Core\Resources\Loader;
use FWK\Services\BasketService;
use FWK\Services\SettingsService;
use SDK\Dtos\Common\Route;

/**
 * This is the BasketController controller class, it is an extension of the framework class \FWK\Controllers\Checkout\BasketController, see this class.
 *
 * @see BaseHtmlController
 *
 * @package FWK\Controllers\Checkout
 */
class BasketController extends BaseHtmlController {

    use CheckoutRedirectTrait {
        __construct as __constructCheckoutRedirectTrait;
    }

    public const BASKET = 'basket';

    private ?SettingsService $settingsService = null;

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        $this->__constructCheckoutRedirectTrait($route);
        $this->settingsService = Loader::service(Services::SETTINGS);
    }

    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
        if (!$this->settingsService->getBasketStockLockingSettings()->getActive()) {
            $this->getBasketService()->addGetBasket($requests, self::BASKET);
        }
    }

    /**
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     */
    final protected function setControllerBaseData(): void {
        if ($this->settingsService->getBasketStockLockingSettings()->getActive()) {
            $basket = $this->getBasketService()->recalculate();
            if (!is_null($basket->getError())) {
                $this->breakControllerProcess('Missing data on the Service response', CommerceException::CONTROLLER_UNDEFINED_CRITICAL_DATA);
            }
            $this->setDataValue(self::BASKET, $basket);
        }
    }

    private function getBasketService(): BasketService {
        return Loader::service(Services::BASKET);
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
