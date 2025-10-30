<?php

namespace FWK\Controllers\User;

use FWK\Enums\Parameters;

/**
 * This is the user oauth callback path controller.
 * This class extends OauthCallbackController (FWK\Core\Controllers\User\OauthCallbackController), see this class.
 *
 * @controllerData: 
 *  <p>self::CONTROLLER_ITEM[self::PLUGIN_MODULE] => $module (Path variable)</p>
 *  <p>self::CONTROLLER_ITEM[self::CODE] => $this->getRequestParam(Parameters::CODE)</p>
 *  <p>self::CONTROLLER_ITEM[self::RESPONSE] => \SDK\Dtos\User\UserOauth</p>
 * 
 * @twigContent: \themes\{{themeName}}\{{version}}\Content\User\OauthCallbackPathController\default.html.twig
 * 
 * @RouteType: \SDK\Enums\RouteType::USER_OAUTH_CALLBACK_PATH
 *
 * @filterParams: \FWK\Core\FilterInput\FilterInputFactory::getPluginModuleCodeParameter()
 * 
 * @package FWK\Controllers\User
 */
class OauthCallbackPathController extends OauthCallbackController {

    /**
     * This method returns the plugin module.
     *
     * @return string
     */
    protected function getPluginModule(): string {
        $path = $this->getRequestParam(Parameters::PATH);
        $splitPath = explode('/', $path);
        $module = end($splitPath);
        return $module;
    }
}
