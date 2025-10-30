<?php

declare(strict_types=1);

namespace FWK\Core\Controllers\Traits;

use SDK\Core\Dtos\Element;
use SDK\Core\Resources\BatchRequests;
use FWK\Core\Resources\Session;
use FWK\Core\Resources\Loader;
use FWK\Enums\Services;
use SDK\Core\Resources\PluginTriggers;
use SDK\Enums\PluginEvents;
use SDK\Enums\PluginConnectorType;
use SDK\Core\Dtos\ElementCollection;
use SDK\Services\Parameters\Groups\PluginConnectorTypeParametersGroup;

/**
 * This is the set delivery trait.
 *
 * @see AddPluginPaymentSystemTrait::getAddPluginsPaymentSystems()
 * @see AddPluginPaymentSystemTrait::getAddPluginsPaymentProperties()
 *
 * @package FWK\Core\Controllers\Traits
 */
trait AddPluginPaymentSystemTrait {

    private ?ElementCollection $paymentSystemPlugins = null;
    /**
     * Add plugins PaymentSystems
     * 
     */
    protected function getAddPluginsPaymentSystems(BatchRequests $requests): void {
        $params = new PluginConnectorTypeParametersGroup();
        $params->setType(PluginConnectorType::PAYMENT_SYSTEM);
        $params->setNavigationHash(Session::getInstance()->getNavigationHash());
        /** @var \SDK\Service\PluginService */
        $pluginService = Loader::service(Services::PLUGIN);
        $this->paymentSystemPlugins = $pluginService->getPlugins($params);
        foreach ($this->paymentSystemPlugins as $paymentSystemPlugin) {
            $pluginService->addGetPluginProperties($requests, Services::PLUGIN . '_' . PluginConnectorType::PAYMENT_SYSTEM . '_' . $paymentSystemPlugin->getId(), $paymentSystemPlugin->getId());
        }
    }

    protected function getAddPluginsPaymentProperties(?Object &$data): void {
        $pluginProperties = [];
        if ($this->paymentSystemPlugins !== null) {
            foreach ($this->paymentSystemPlugins as $paymentSystemPlugin) {
                $pluginProperty = $this->getControllerData(Services::PLUGIN . '_' . PluginConnectorType::PAYMENT_SYSTEM . '_' . $paymentSystemPlugin->getId());
                $interfaces = class_implements(get_class($pluginProperty));
                if (isset($interfaces['SDK\Core\Interfaces\PluginPropertyTriggers'])) {
                    $triggerResults = PluginTriggers::execute(PluginEvents::SELECT_PAYMENT_SYSTEM, $paymentSystemPlugin, Session::getInstance()->getBasket());
                    $pluginProperty->setEventResults(PluginEvents::SELECT_PAYMENT_SYSTEM, $triggerResults);
                }
                $key = $pluginProperty->getPluginModule() . "_" . $pluginProperty->getPluginId();
                $pluginProperties[$key] = $pluginProperty;
            }
        }
        if ($data !== null) {
            $this->basketService::mergePaymentSystemsWithPluginProperties($data, $pluginProperties);
        }
    }
}
