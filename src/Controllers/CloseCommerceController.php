<?php

namespace FWK\Controllers;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\Resources\LcFWK;
use SDK\Core\Application;
use SDK\Core\Resources\BatchRequests;

/**
 * This is the CloseCommerceController Class
 * This class extends BaseHtmlController, see this class.
 *
 * @controllerData: self::CONTROLLER_ITEM: self::MAINTENANCE or self::CLOSED
 * 
 * @twigContent: \themes\{{themeName}}\{{version}}\Content\CloseCommerce\default.html.twig
 * 
 * @RouteType: \FWK\Enums\RouteType::CLOSE_COMMERCE
 * 
 * @see BaseHtmlController
 *
 * @package FWK\Controllers
 */
class CloseCommerceController extends BaseHtmlController {

    public const MAINTENANCE = 'MAINTENANCE';

    public const CLOSED = 'CLOSED';

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
     */
    final protected function setControllerBaseData(): void {
        $statusCommerce = self::CLOSED;
        if (Application::getInstance()->getEcommerceSettings()->getGeneralSettings()->getActive() && LcFWK::getMaintenance()) {
            $statusCommerce = self::MAINTENANCE;
        }
        $this->setDataValue(self::CONTROLLER_ITEM, $statusCommerce);
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
