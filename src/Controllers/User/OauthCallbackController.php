<?php

namespace FWK\Controllers\User;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\Resources\Loader;
use FWK\Enums\Parameters;
use FWK\Enums\Services;
use SDK\Core\Resources\BatchRequests;

/**
 * This is the user oauth callback controller.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @controllerData: 
 *  <p>self::CONTROLLER_ITEM[self::PLUGIN_MODULE] => $this->getRequestParam(Parameters::PLUGIN_MODULE)</p>
 *  <p>self::CONTROLLER_ITEM[self::CODE] => $this->getRequestParam(Parameters::CODE)</p>
 *  <p>self::CONTROLLER_ITEM[self::RESPONSE] => \SDK\Dtos\User\UserOauth</p>
 * 
 * @twigContent: \themes\{{themeName}}\{{version}}\Content\User\OauthCallback\default.html.twig
 * 
 * @RouteType: \SDK\Enums\RouteType::USER_OAUTH_CALLBACK
 *
 * @filterParams: \FWK\Core\FilterInput\FilterInputFactory::getPluginModuleCodeParameter()
 * 
 * @package FWK\Controllers\User
 */
class OauthCallbackController extends BaseHtmlController {

    public const PLUGIN_MODULE = 'pluginModule';

    public const CODE = 'code';

    public const RESPONSE = 'response';

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FilterInputFactory::getPluginModuleCodeParameter();
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
        $controllerResponse = [];
        $code = $this->getRequestParam(Parameters::CODE);
        if (empty($code)) {
            $code = $this->getRequestParam(Parameters::BUYER_TOKEN);
            if (empty($code)) {
                $code = "";
            }
        }
        $pluginModule = $this->getPluginModule();
        $oauthResponse = Loader::service(Services::USER)->setOauth($pluginModule, $code);
        $controllerResponse[self::PLUGIN_MODULE] = $pluginModule;
        $controllerResponse[self::CODE] = $code;
        $controllerResponse[self::RESPONSE] = $oauthResponse;
        $this->setDataValue(self::CONTROLLER_ITEM, $controllerResponse);
    }

    /**
     * This method returns the plugin module.
     */
    protected function getPluginModule(): string {
        return $this->getRequestParam(Parameters::PLUGIN_MODULE);
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
