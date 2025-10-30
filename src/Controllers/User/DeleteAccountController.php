<?php

namespace FWK\Controllers\User;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\Form\FormFactory;
use FWK\Core\Resources\Response;
use FWK\Core\Resources\RoutePaths;
use FWK\Enums\RouteType;
use SDK\Core\Resources\BatchRequests;

/**
 * This is the user delete account controller.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @controllerData: 
 *  <p>self::FORM_DELETE_ACCOUNT: \FWK\Core\Form\FormFactory::getDeleteAccount()</p>
 * 
 * @twigContent: \themes\{{themeName}}\{{version}}\Content\User\DeleteAccount\default.html.twig
 * 
 * @RouteType: \SDK\Enums\RouteType::USER_DELETE_ACCOUNT
 * 
 * @simulatedUserForbbiden: true
 *
 * @package FWK\Controllers\User
 */
class DeleteAccountController extends BaseHtmlController {

    public const FORM_DELETE_ACCOUNT = 'formDeleteAccount';

    protected bool $simulatedUserForbbiden = true;

    /**
     * This method is the one in charge of defining all the data batch requests that
     * are needed for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     * @return void
     */
    protected function setBatchData(BatchRequests $requests): void {
    }

    /**
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     *
     */
    protected function setControllerBaseData(): void {
        $this->setDataValue(self::FORM_DELETE_ACCOUNT, FormFactory::getDeleteAccount());
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
