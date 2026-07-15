<?php

declare(strict_types=1);

namespace FWK\Core\Controllers\Traits;

use SDK\Core\Resources\BatchRequests;
use FWK\Core\Resources\Session;
use FWK\Core\Resources\Loader;
use FWK\Enums\Services;
use SDK\Enums\PluginConnectorType;
use SDK\Core\Dtos\ElementCollection;
use SDK\Core\Resources\PluginTriggers;
use SDK\Enums\PluginEvents;
use SDK\Services\Parameters\Groups\PluginConnectorTypeParametersGroup;

/**
 * This is the get Plugins Reward Points trait.
 *
 * @see AddPluginRewardPointsTrait::getAddPluginsRewardPoints()
 *
 * @package FWK\Core\Controllers\Traits
 */
trait AddPluginRewardPointsTrait {

    public const PLUGIN = 'plugin';
    public const PROPERTIES = 'properties';

    private ?ElementCollection $rewardPointsPlugins = null;

    /**
     * Add plugins RewardPoints
     * 
     */
    protected function getAddPluginsRewardPoints(BatchRequests $requests): void {
        $params = new PluginConnectorTypeParametersGroup();
        $params->setConnectorType(PluginConnectorType::REWARD_POINTS);
        $params->setNavigationHash(Session::getInstance()->getNavigationHash());
        /** @var \SDK\Service\PluginService */
        $pluginService = Loader::service(Services::PLUGIN);
        $this->rewardPointsPlugins = $pluginService->getPlugins($params);
        foreach (($this->rewardPointsPlugins ?? []) as $rewardPointsPlugin) {
            $batchName = Services::PLUGIN . '_' . PluginConnectorType::REWARD_POINTS . '_' . $rewardPointsPlugin->getId();
            $pluginService->addGetPluginProperties($requests, $batchName, $rewardPointsPlugin->getId());
        }
    }

    protected function getPluginsRewardPoints(): mixed {
        $rewardPointsPlugins = [];
        foreach (($this->rewardPointsPlugins ?? []) as $rewardPointsPlugin) {
            $plugin = [];
            $batchName = Services::PLUGIN . '_' . PluginConnectorType::REWARD_POINTS . '_' . $rewardPointsPlugin->getId();
            $plugin[self::PLUGIN] = $rewardPointsPlugin;
            $pluginProperties = $this->getControllerData($batchName);
            $interfaces = class_implements(get_class($pluginProperties));
            if (isset($interfaces['SDK\Core\Interfaces\PluginPropertyTriggers'])) {
                $triggerResults = PluginTriggers::execute(PluginEvents::SETTINGS, $rewardPointsPlugin, Session::getInstance()->getBasket());
                $pluginProperties->setEventResults(PluginEvents::SETTINGS, $triggerResults);
            }
            $plugin[self::PROPERTIES] = $pluginProperties;
            $rewardPointsPlugins[] = $plugin;
        }
        return $rewardPointsPlugins;
    }

    protected function getAccountPluginsRewardPoints(): mixed {
        $rewardPointsPlugins = [];
        foreach (($this->rewardPointsPlugins ?? []) as $rewardPointsPlugin) {
            $plugin = [];
            $batchName = Services::PLUGIN . '_' . PluginConnectorType::REWARD_POINTS . '_' . $rewardPointsPlugin->getId();
            $plugin[self::PLUGIN] = $rewardPointsPlugin;
            $pluginProperties = $this->getControllerData($batchName);
            $interfaces = class_implements(get_class($pluginProperties));
            if (isset($interfaces['SDK\Core\Interfaces\PluginPropertyTriggers'])) {
                $triggerResults = PluginTriggers::execute(PluginEvents::SETTINGS, $rewardPointsPlugin, Session::getInstance()->getBasket());
                $pluginProperties->setEventResults(PluginEvents::SETTINGS, $triggerResults);
                $triggerResults = PluginTriggers::execute(PluginEvents::CUSTOMER, $rewardPointsPlugin, Session::getInstance()->getBasket());
                $pluginProperties->setEventResults(PluginEvents::CUSTOMER, $triggerResults);
            }
            $plugin[self::PROPERTIES] = $pluginProperties;
            $rewardPointsPlugins[] = $plugin;
        }
        return $rewardPointsPlugins;
    }
}
