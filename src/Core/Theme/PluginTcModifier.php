<?php

declare(strict_types=1);

namespace FWK\Core\Theme;

/**
 * Lets an override plugin adjust the theme configuration data (the array Tc::getConfigurationData()
 * builds) on the routes it takes over, before FWK turns it into the Configuration DTOs.
 *
 * Discovered by class-name convention:
 *
 *     Plugins\<CamelModule>\Core\Theme\TcModifier
 *
 * Only applied once the route is resolved, and only for active plugins whose properties list the route
 * in `getTcOverridePages()` (see PluginService::getActiveTcModifiers); every other route keeps the
 * commerce's own Tc untouched.
 *
 * @package FWK\Core\Theme
 */
interface PluginTcModifier {

    public function modify(array $configurationData, string $routeType): array;
}
