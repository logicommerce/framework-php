<?php

namespace FWK\Controllers\User;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\Resources\Loader;
use FWK\Core\Resources\Session;
use FWK\Enums\Services;
use FWK\Services\PluginService;
use FWK\ViewHelpers\User\Macro\PaymentCards;
use SDK\Core\Dtos\ElementCollection;
use SDK\Core\Resources\BatchRequests;
use SDK\Enums\PluginConnectorType;
use SDK\Services\Parameters\Groups\PluginConnectorTypeParametersGroup;

/**
 * This is the user payment cards controller.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @controllerData: self::CONTROLLER_ITEM => array 
 *   <p>self::CONTROLLER_ITEM[{{module}}][FWK\ViewHelpers\User\Macro\PaymentCards::USER_PLUGIN_PAYMENT_TOKENS] => \SDK\Core\Dtos\Factories\UserPluginPaymentTokenFactory<p>
 *   <p>self::CONTROLLER_ITEM[{{module}}][FWK\ViewHelpers\User\Macro\PaymentCards::PLUGINS_PROPERTIES] => \SDK\Core\Dtos\Factories\PluginPropertiesFactory<p>
 * 
 * @twigContent: \themes\{{themeName}}\{{version}}\Content\User\PaymentCards\default.html.twig
 * 
 * @RouteType: \SDK\Enums\RouteType::USER_PAYMENT_CARDS
 *
 * @package FWK\Controllers\User
 */
class PaymentCardsController extends BaseHtmlController {

    protected ?PluginService $pluginService = null;

    protected ElementCollection $paymentSystemPlugins;

    protected bool $simulatedUserForbbiden = true;

    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
        $this->pluginService = Loader::service(Services::PLUGIN);
        $params = new PluginConnectorTypeParametersGroup();
        $params->setType(PluginConnectorType::PAYMENT_SYSTEM);
        $params->setNavigationHash($this->getSession()->getNavigationHash());
        $this->paymentSystemPlugins = $this->pluginService->getPlugins($params);
        foreach ($this->paymentSystemPlugins as $paymentSystemPlugin) {
            $this->pluginService->addGetUserPluginPaymentTokens($requests, PaymentCards::USER_PLUGIN_PAYMENT_TOKENS . '_' . $paymentSystemPlugin->getId(), $paymentSystemPlugin->getId());
        }
        foreach ($this->paymentSystemPlugins as $paymentSystemPlugin) {
            $this->pluginService->addGetPluginProperties($requests, PaymentCards::PLUGINS_PROPERTIES . '_' . $paymentSystemPlugin->getId(), $paymentSystemPlugin->getId());
        }
    }

    /**
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     */
    final protected function setControllerBaseData(): void {
        $paymentCards = [];
        foreach ($this->paymentSystemPlugins as $paymentSystemPlugin) {
            $pluginPaymentTokenCollection = $this->getControllerData(PaymentCards::USER_PLUGIN_PAYMENT_TOKENS . '_' . $paymentSystemPlugin->getId());
            $paymentCards[$pluginPaymentTokenCollection->getModule()][PaymentCards::USER_PLUGIN_PAYMENT_TOKENS] = $pluginPaymentTokenCollection;
        }
        $pluginsProperties = [];
        foreach ($this->paymentSystemPlugins as $paymentSystemPlugin) {
            $pluginsProperties = $this->getControllerData(PaymentCards::PLUGINS_PROPERTIES . '_' . $paymentSystemPlugin->getId());
            $paymentCards[$pluginsProperties->getPluginModule()][PaymentCards::PLUGINS_PROPERTIES] = $pluginsProperties;
        }
        $this->setDataValue(self::CONTROLLER_ITEM, $paymentCards);
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
