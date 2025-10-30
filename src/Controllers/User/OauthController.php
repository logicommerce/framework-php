<?php

namespace FWK\Controllers\User;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\Resources\Loader;
use FWK\Core\Resources\Response;
use FWK\Enums\Parameters;
use FWK\Enums\Services;
use SDK\Core\Resources\BatchRequests;

/**
 * This is the user oauth controller.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 * 
 * @redirect: \FWK\Core\Resources\Loader::service(\FWK\Enums\Services::USER)->getOauth($this->getRequestParam(\FWK\Enums\Parameters::PLUGIN_MODULE))->getUrl()
 * 
 * @RouteType: \SDK\Enums\RouteType::USER_OAUTH
 *
 * @filterParams: \FWK\Core\FilterInput\FilterInputFactory::getPluginModuleParameter()
 *
 * @package FWK\Controllers\User
 */
class OauthController extends BaseHtmlController {

    protected bool $loggedInRequired = false;

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FilterInputFactory::getPluginModuleParameter();
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
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     */
    protected function setControllerBaseData(): void {
        $response = Loader::service(Services::USER)->getOauth($this->getRequestParam(Parameters::PLUGIN_MODULE));
        Response::redirect($response->getUrl());
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
