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
use Plugins\ComLogicommerceMagicfront\Dtos\Common\PluginProperties as MagicFrontPluginProperties;
use FWK\Core\Dtos\ElementCollection as DtosElementCollection;
use FWK\Core\Resources\Loader;
use FWK\Dtos\Common\Plugin as FWKPlugin;
use FWK\Dtos\Basket\PaymentSystem;
use FWK\Dtos\Common\PluginExpressCheckout;
use FWK\Enums\RouteTypes\InternalResources;
use FWK\Enums\RouteTypes\InternalUser;
use FWK\Enums\Services;
use SDK\Dtos\Common\Plugin;
use SDK\Application;
use SDK\Core\Enums\Resource;
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

    private ?BasketService $basketService = null;

    private const OVERRIDE_PLUGIN_PIDS = [
        'com.logicommerce.magicfront',
    ];

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
        $expressCheckoutPlugins = DtosElementCollection::fillFromParentCollection($plugins, PluginExpressCheckout::class);
        $this->basketService = Loader::service(Services::BASKET);
        $paymentSystems = $this->basketService->getPaymentSystems();
        foreach ($expressCheckoutPlugins->getItems() as $expressCheckoutPlugin) {
            /** @var PluginExpressCheckout $expressCheckoutPlugin */
            $pluginProperties = $this->getPluginProperties($expressCheckoutPlugin->getId());
            $pluginPaymentSystems = $this->getPluginAccountPaymentSystems(
                $paymentSystems->getItems(),
                $expressCheckoutPlugin->getId(),
                $pluginProperties->getConnectors()
            );
            $expressCheckoutPlugin->setPaymentSystems($pluginPaymentSystems);
            $expressCheckoutPlugin->setProperties($pluginProperties);
        }
        $expressCheckoutPlugins->resetIndex();
        return $expressCheckoutPlugins;
    }

    private function getPluginAccountPaymentSystems(array $paymentSystems, int $pluginAccountId, array $connectors): ?array {
        $pluginPaymentSystems = [];
        foreach ($paymentSystems as $paymentSystem) {
            $paymentSystemConnector = $this->getPluginAccountConnector($connectors, PluginConnectorType::PAYMENT_SYSTEM);
            if ($paymentSystemConnector !== null && $paymentSystem->getPluginId() === $pluginAccountId) {
                $connectorItem = $this->getPluginAccountConnectorItem($paymentSystemConnector, $paymentSystem->getId());
                if ($connectorItem !== null) {
                    $paymentSystem->setPluginPropertiesConnectorItemProperties($connectorItem->getProperties());
                    $pluginPaymentSystems[] = $paymentSystem;
                }
            }
        }
        return $pluginPaymentSystems;
    }

    private function getPluginAccountConnector(array $connectors, string $type): mixed {
        foreach ($connectors as $connector) {
            if ($connector->getType() === $type) {
                return $connector;
            }
        }
        return null;
    }

    private function getPluginAccountConnectorItem(mixed $connector, int $itemId): mixed {
        foreach ($connector->getItems() as $item) {
            if ($item->getItemId() === $itemId) {
                return $item;
            }
        }
        return null;
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

    private function getPluginConnectorTypeParametersGroup(string $connectorType): ?PluginConnectorTypeParametersGroup {
        $params = new PluginConnectorTypeParametersGroup();
        $params->setConnectorType($connectorType);
        $params->setNavigationHash($this->getNavigationHash());
        return $params;
    }

    /**
     * Returns all active override plugins (those whose pId is listed in OVERRIDE_PLUGIN_PIDS).
     * To add a plugin to this mechanism, add its pId to OVERRIDE_PLUGIN_PIDS.
     *
     * @return Plugin[]
     *
     */
    public function getOverridePlugins(): array {
        $plugins = Application::getInstance()->getEcommercePlugins();
        if (is_null($plugins)) {
            $plugins = $this->getElements(Plugin::class, Resource::PLUGINS);
        }
        $indexed = [];
        foreach ($plugins as $plugin) {
            $indexed[$plugin->getPId()] = $plugin;
        }
        $result = [];
        foreach (self::OVERRIDE_PLUGIN_PIDS as $pId) {
            if (isset($indexed[$pId]) && $indexed[$pId]->isActive()) {
                $result[] = $indexed[$pId];
            }
        }
        return $result;
    }

    /**
     * Returns if the MagicFront plugin is enabled for the given route type
     *
     * @param string $routeType
     *
     * @return bool
     *
     */
    public function isPluginMagicFrontEnabled(string $routeType): bool {
        $plugins = Application::getInstance()->getEcommercePlugins();
        if (is_null($plugins)) {
            $plugins = $this->getElements(Plugin::class, Resource::PLUGINS);
        }
        foreach ($plugins as $plugin) {
            if ($plugin->getPId() == 'com.logicommerce.magicfront') {
                return $plugin->isActive();
            }
        }
        return false;
        /** @var MagicFrontPluginProperties|null $properties */
        /*
        $properties = $this->getPluginPropertiesByConnectorType(PluginConnectorType::MAGIC_FRONT);
        return $properties !== null && in_array($routeType, $properties->getAvailablePages());*/
    }
}
