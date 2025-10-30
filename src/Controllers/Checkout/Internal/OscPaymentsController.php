<?php

namespace FWK\Controllers\Checkout\Internal;

use SDK\Core\Resources\BatchRequests;
use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\Resources\Loader;
use FWK\Enums\Services;

use FWK\Core\Controllers\Traits\AddPluginPaymentSystemTrait;
use FWK\Services\BasketService;

use FWK\Services\PluginService;
use SDK\Core\Dtos\ElementCollection;

/**
 * This is the OSC internal payments controller.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @see BaseHtmlController
 *
 * @package FWK\Controllers\Checkout\Internal
 */
class OscPaymentsController extends BaseHtmlController {
    use AddPluginPaymentSystemTrait;

    protected const PAYMENT_SYSTEMS = 'paymentSystems';

    private ?BasketService $basketService = null;

    private ?PluginService $pluginService = null;

    private ?ElementCollection $paymentSystemPlugins = null;

    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
        $this->basketService = Loader::service(Services::BASKET);
        $this->basketService->addGetPaymentSystems($requests, self::PAYMENT_SYSTEMS);
        $this->getAddPluginsPaymentSystems($requests);
    }

    /**
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     */
    final protected function setControllerBaseData(): void {
        $paymentSystems = $this->getControllerData(self::PAYMENT_SYSTEMS);
        $this->checkCriticalServiceLoaded($paymentSystems);
        $this->getAddPluginsPaymentProperties($paymentSystems);
        $this->setDataValue(self::CONTROLLER_ITEM, [
            self::PAYMENT_SYSTEMS => $paymentSystems
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
