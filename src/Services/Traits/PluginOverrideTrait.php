<?php

namespace FWK\Services\Traits;

use FWK\Core\Resources\Loader;
use FWK\Core\Resources\Route as FwkRoute;
use FWK\Core\Resources\Utils;
use FWK\Enums\Services;
use SDK\Dtos\Catalog\Page\Page;
use SDK\Dtos\Common\Plugin;
use SDK\Dtos\Common\Route;
use SDK\Enums\PluginConnectorType;
use SDK\Enums\RouteType;

/**
 * SITE_BUILDER plugin override resolution — the subsystem that decides which plugins
 * take over the layout/chrome (Twig initializers) and the controller for a route.
 *
 * Mixed into {@see \FWK\Services\PluginService}; relies on the host for the shared
 * helpers getPlugins(), getPluginConnectorTypeParametersGroup() and
 * getPluginPropertiesByModule().
 *
 * @package FWK\Services\Traits
 */
trait PluginOverrideTrait {

    /** Per-request memo of getControllerOverridePlugins() (null = not computed yet; [] is a valid computed result). */
    private ?array $controllerOverridePluginsCache = null;

    /**
     * Active SITE_BUILDER plugins, filtered by `getAvailablePages()` when a
     * route type is given. Used by TwigLoader for layout/chrome activation.
     */
    final public function getOverridePlugins(?string $routeType = null): array {
        $params = $this->getPluginConnectorTypeParametersGroup(PluginConnectorType::SITE_BUILDER);
        $plugins = $this->getPlugins($params);
        $result = [];
        foreach ($plugins->getItems() as $plugin) {
            /** @var Plugin $plugin */
            if (!$plugin->isActive()) {
                continue;
            }
            if ($routeType !== null && !$this->isOverrideEnabledForRoute($plugin, $routeType)) {
                continue;
            }
            $result[] = $plugin;
        }
        return $result;
    }

    /** Plugin without `getAvailablePages()` opts out of per-route filtering (overrides everywhere). */
    private function isOverrideEnabledForRoute(Plugin $plugin, string $routeType): bool {
        $properties = $this->getPluginPropertiesByModule($plugin->getModule());
        if ($properties === null) {
            return false;
        }
        if (!method_exists($properties, 'getAvailablePages')) {
            return true;
        }
        return in_array($routeType, $properties->getAvailablePages(), true);
    }

    /** Discovers `Plugins\<Module>\Core\Twig\TwigInitializer` by class-name convention; missing class = opt out. */
    final public function getActiveTwigInitializers(?string $routeType = null): array {
        $result = [];
        foreach ($this->getOverridePlugins($routeType) as $plugin) {
            $module = Utils::getCamelFromSnake($plugin->getModule(), '.');
            $class = 'Plugins\\' . $module . '\\Core\\Twig\\TwigInitializer';
            if (class_exists($class) && is_subclass_of($class, \FWK\Twig\PluginTwigInitializer::class)) {
                $result[] = new $class();
            }
        }
        return $result;
    }

    /**
     * Strict variant — filtered by `getControllerOverridePages()` so layout-only
     * plugins don't hijack the controller. Falls back to `getAvailablePages`.
     *
     * Result is memoized: computed once (with the route → native-page gate) and reused
     * by later route-less callers (e.g. TwigLoader) within the request.
     */
    final public function getControllerOverridePlugins(?string $routeType = null, ?Route $route = null): array {
        if ($routeType === null) {
            return $this->getOverridePlugins($routeType);
        }
        if ($this->controllerOverridePluginsCache !== null) {
            return $this->controllerOverridePluginsCache;
        }
        $plugins = $this->getOverridePlugins($routeType);
        if ($plugins === [] || $this->isNativePageRoute($route)) {
            return $this->controllerOverridePluginsCache = [];
        }
        $result = [];
        foreach ($plugins as $plugin) {
            $properties = $this->getPluginPropertiesByModule($plugin->getModule());
            if ($properties === null) {
                continue;
            }
            if (method_exists($properties, 'getControllerOverridePages')) {
                if (in_array($routeType, $properties->getControllerOverridePages(), true)) {
                    $result[] = $plugin;
                }
                continue;
            }
            $result[] = $plugin;
        }
        return $this->controllerOverridePluginsCache = $result;
    }

    /**
     * A PAGE route whose page has no pluginAccountId is a native site page — no plugin
     * may take over its controller. Self-resolves when the caller passed no route (e.g.
     * TwigLoader): falls back to the current route and, for a PAGE route without a loaded
     * page, fetches it by id (mirrors Router::enroute).
     */
    private function isNativePageRoute(?Route $route): bool {
        $route ??= Loader::service(Services::ROUTE)->getRoute();
        if (!$route instanceof Route || $route->getType() !== RouteType::PAGE) {
            return false;
        }
        $page = $route instanceof FwkRoute
            ? $route->getPage()
            : Loader::service(Services::PAGE)->getPageById((int) $route->getId());
        return $page instanceof Page && $page->getPluginAccountId() === null;
    }
}
