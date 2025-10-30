<?php

namespace FWK\Controllers;

use FWK\Core\Controllers\BaseHtmlController;
use SDK\Core\Resources\BatchRequests;
use FWK\Core\Form\FormFactory;
use FWK\Core\Resources\Loader;
use FWK\Enums\Services;

/**
 * This is the base contact controller
 * This class extends BaseHtmlController, see this class.
 *
 * @controllerData: 
 *  <p>self::CONTROLLER_ITEM: \SDK\Dtos\Catalog\Page\Page or null</p>
 *  <p>self::FORM_CONTACT: \FWK\Core\Form\FormFactory::getContact()</p>
 * 
 * @twigContent: \themes\{{themeName}}\{{version}}\Content\Contact\default.html.twig
 * 
 * @RouteType: \SDK\Enums\RouteType::CONTACT
 * 
 * @see BaseHtmlController
 *
 * @package FWK\Controllers
 */
class ContactController extends BaseHtmlController {

    public const FORM_CONTACT = 'form';

    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
        if ($this->getRoute()->getId() > 0) {
            Loader::service(Services::PAGE)->addGetPageById($requests, self::CONTROLLER_ITEM, $this->getRoute()->getId());
        } else {
            $this->setDataValue(self::CONTROLLER_ITEM, null);
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
    protected function setControllerBaseData(): void {
        $this->setDataValue(self::FORM_CONTACT, FormFactory::getContact());
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
