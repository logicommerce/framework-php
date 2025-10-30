<?php

namespace FWK\Controllers\Account;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\Form\FormFactory;
use SDK\Core\Resources\BatchRequests;

/**
 * This is the account change password controller.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @controllerData: self::FORM_CHANGE_PASSWORD: \FWK\Core\Form\FormFactory::getUpdatePassword()
 * 
 * @twigContent: \themes\{{themeName}}\{{version}}\Content\Account\ChangePassword\default.html.twig
 * 
 * @RouteType: \SDK\Enums\RouteType::REGISTERED_USER_CHANGE_PASSWORD
 * 
 * @package FWK\Controllers\Account
 */
class RegisteredUserChangePasswordController extends BaseHtmlController {

    public const FORM_CHANGE_PASSWORD = 'formChangePassword';

    protected bool $simulatedUserForbbiden = true;

    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
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
        $this->setDataValue(self::FORM_CHANGE_PASSWORD, FormFactory::getUpdatePassword());
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
