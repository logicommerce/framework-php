<?php

namespace FWK\Controllers;

use FWK\Core\Controllers\BaseHtmlController;
use SDK\Core\Resources\BatchRequests;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Enums\Parameters;
use FWK\Core\Resources\Loader;
use FWK\Enums\Services;
use FWK\Core\Form\FormFactory;
use SDK\Core\Dtos\Error;

/**
 * This is the Change Password Anonymous controller class.
 * This class extends BaseHtmlController, see this class.
 *
 * @controllerData: self::FORM_CHANGE_PASSWORD: \FWK\Core\Form\FormFactory::getNewPassword()
 * 
 * @twigContent: \themes\{{themeName}}\{{version}}\Content\ChangePasswordAnonymous\default.html.twig
 * 
 * @RouteType: \SDK\Enums\RouteType::CHANGE_PASSWORD_ANONYMOUS
 * 
 * @see BaseHtmlController
 *
 * @package FWK\Controllers
 */
class ChangePasswordAnonymousController extends BaseHtmlController {

    public const FORM_CHANGE_PASSWORD = 'formChangePassword';

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FilterInputFactory::getHashParameter();
    }

    /**
     * This method returns if the request should be run as a forbidden request.
     *
     * @return bool
     */
    protected function isForbidden(): bool {
        if (parent::isForbidden() || $this->getSession()->getUser()->getId() > 1 || !strlen($this->getRequestParam(Parameters::HASH, false, '')) || Loader::service(Services::USER)->validateRecoverPasswordHash(urlencode($this->getRequestParam(Parameters::HASH)))->getError() instanceof Error) {
            return true;
        }
        return false;
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
        $this->setDataValue(self::FORM_CHANGE_PASSWORD, FormFactory::getNewPassword($this->getRequestParam(Parameters::HASH)));
    }
}
