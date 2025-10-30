<?php

namespace FWK\Services;

use FWK\Core\Resources\Registries\RegistryService;
use SDK\Core\Resources\BatchRequests;
use SDK\Enums\RouteType;
use SDK\Services\Parameters\Groups\AssetParametersGroup;
use SDK\Services\PluginService as PluginServiceSDK;
use FWK\Services\Traits\ServiceTrait;
use SDK\Core\Dtos\ElementCollection;
use SDK\Core\Dtos\PluginProperties;
use SDK\Enums\PluginConnectorType;
use FWK\Core\Dtos\ElementCollection as DtosElementCollection;
use FWK\Dtos\Common\Plugin as FWKPlugin;
use SDK\Dtos\Common\Plugin;
use SDK\Services\Parameters\Groups\PluginConnectorTypeParametersGroup;
use SDK\Services\Parameters\Groups\PluginModuleParametersGroup;

/**
 * This is the PluginService class.
 * Remember that a service is an extension of a SDK model that allows to add additional
 * actions to a model request or create new methods to simplify some common requests.
 * In this case, the PluginService extends the SDK\Services\PluginService.
 *
 * @see LegalTextService
 *
 * @package FWK\Services
 */
class PluginService extends PluginServiceSDK {

    use ServiceTrait;

    private const REGISTRY_KEY = RegistryService::PLUGIN_SERVICE;

    private const ADD_FILTER_INTERVAL_PARAMETERS = [];

    private const ADD_FILTER_ID_VALUE_PARAMETERS = [];

    private function getParametersByRouteType(string $routeType): AssetParametersGroup {
        $trackerParametersGroup = new AssetParametersGroup();
        $trackerParametersGroup->setRouteType($routeType);
        return $trackerParametersGroup;
    }

    /**
     * This method adds the batch request to get those trackers whose routeType matches the given one.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     * @param string $routeType
     * 
     * @return void
     */
    public function addGetAssetsByRouteType(BatchRequests $batchRequests, string $batchName, string $routeType): void {
        $this->addGetAssets($batchRequests, $batchName, $this->getParametersByRouteType($routeType));
    }

    /**
     * This method returns if the routeType given is valid the given routeType for gets its assets.
     * 
     * @param string $routeType
     *
     * @return bool
     *
     */
    public static function isValidRouteTypeForAssets(string $routeType): bool {
        return RouteType::isValid($routeType);
    }

    /**
     * This method returns the configuration of maps plugin
     *
     * @return ?PluginProperties
     *
     */
    public function getMapsPluginProperties(): ?PluginProperties {
        $params = $this->getPluginConnectorTypeParametersGroup(PluginConnectorType::MAPS);
        $plugins = $this->getPlugins($params);
        if (!empty($plugins->getItems())) {
            return $this->getPluginPropertiesByModule($plugins->getItems()[0]->getModule());
        } else {
            return null;
        }
    }

    /**
     * This method returns the configuration of captcha plugin
     *
     * @return ?PluginProperties
     *
     */
    public function getCaptchaPluginProperties(): ?PluginProperties {
        $params = $this->getPluginConnectorTypeParametersGroup(PluginConnectorType::CAPTCHA);
        $plugins = $this->getPlugins($params);
        if (!empty($plugins->getItems())) {
            return $this->getPluginPropertiesByModule($plugins->getItems()[0]->getModule());
        } else {
            return null;
        }
    }

    /**
     * Returns the Express checkout active plugins
     *
     * @return ElementCollection|NULL
     */
    public function getExpressCheckoutPlugins(): ?ElementCollection {
        $params = $this->getPluginConnectorTypeParametersGroup(PluginConnectorType::EXPRESS_CHECKOUT);
        $plugins = $this->getPlugins($params);
        $expressCheckoutPlugins = DtosElementCollection::fillFromParentCollection($plugins, FWKPlugin::class);

        foreach ($expressCheckoutPlugins->getItems() as $expressCheckoutPluginKey => $expressCheckoutPlugin) {
            $pluginProperties = $this->getPluginProperties($expressCheckoutPlugin->getId());
            $expressCheckoutActive = false;
            foreach ($pluginProperties->getProperties() as $pluginPropertieKey => $pluginPropertie) {
                if ($pluginPropertie->getName() === 'expressCheckout' && $pluginPropertie->getValue() === 'true') {
                    $expressCheckoutActive = true;
                }
                $expressCheckoutPlugin->setProperties($pluginProperties);
            }
            if (!$expressCheckoutActive) {
                $expressCheckoutPlugins->remove($expressCheckoutPluginKey, false);
            }
        }
        $expressCheckoutPlugins->resetIndex();
        return $expressCheckoutPlugins;
    }

    /**
     * Returns the Oauth plugins
     *
     * @return ElementCollection|NULL
     */
    public function getOauthPlugins(): ?ElementCollection {
        $params = $this->getPluginConnectorTypeParametersGroup(PluginConnectorType::OAUTH);
        $plugins = $this->getPlugins($params);
        $oauthPlugins = DtosElementCollection::fillFromParentCollection($plugins, FWKPlugin::class);
        foreach ($oauthPlugins->getItems() as $oauthPlugin) {
            $pluginProperties = $this->getPluginProperties($oauthPlugin->getId());
            $oauthPlugin->setProperties($pluginProperties);
        }
        $oauthPlugins->resetIndex();
        return $oauthPlugins;
    }

    /**
     * Returns the configuration of the route plugin
     *
     * @return ElementCollection|NULL
     */
    public function getRoutePluginProperties(string $module): ?PluginProperties {
        return $this->getPluginPropertiesByModule($module);
    }

    /**
     * Returns the configuration of the route plugin
     *
     * @param string $module
     *
     * @return ?PluginProperties
     *
     */
    public function getRoutePluginByModule(string $module): ?Plugin {
        $params = $this->getPluginConnectorTypeParametersGroup(PluginConnectorType::ROUTE);
        $plugins = $this->getPlugins($params);
        foreach ($plugins->getItems() as $plugin) {
            /** @var Plugin $plugin */
            if ($plugin->getModule() === $module) {
                return $plugin;
            }
        }
        return null;
    }

    /**
     * Returns the true if the plugin by module is enabled
     *
     * @param string $module
     *
     * @return bool
     *
     */
    public function isPluginEnabled(string $module): bool {
        $params = new PluginModuleParametersGroup();
        $params->setModule($module);
        $params->setNavigationHash($this->getNavigationHash());
        $plugins = $this->getPluginsByModule($params);
        if (count($plugins) == 0) {
            return false;
        }
        foreach ($plugins as $plugin) {
            /** @var Plugin $plugin */
            if ($plugin->isActive()) {
                return true;
            }
        }
        return false;
    }

    private function getPluginConnectorTypeParametersGroup(String $connectorType): ?PluginConnectorTypeParametersGroup {
        $params = new PluginConnectorTypeParametersGroup();
        $params->setType($connectorType);
        $params->setNavigationHash($this->getNavigationHash());
        return $params;
    }
}