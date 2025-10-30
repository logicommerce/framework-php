<?php

namespace FWK\Controllers\User;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Enums\Services;
use FWK\Core\Resources\Loader;
use FWK\Dtos\Documents\Document;
use SDK\Core\Resources\BatchRequests;

/**
 * This is the user order controller.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @controllerData: self::CONTROLLER_ITEM => \FWK\Dtos\Documents\Document
 * 
 * @twigContent: \themes\{{themeName}}\{{version}}\Content\User\Order\default.html.twig
 * 
 * @RouteType: \SDK\Enums\RouteType::USER_ORDER
 *
 * @package FWK\Controllers\User
 */
class OrderController extends BaseHtmlController {

    private const ORDER = 'order';

    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {        
        Loader::service(Services::ORDER)->addGetOrder($requests, self::ORDER, $this->getRoute()->getId());
    }

    /**
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     *
     */
    protected function setControllerBaseData(): void {
        $document = $this->getControllerData(self::ORDER);        
        $document = Document::fillFromParent($document);
        $this->setDataValue(self::CONTROLLER_ITEM, $document);
        $this->deleteControllerData(self::ORDER);
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
