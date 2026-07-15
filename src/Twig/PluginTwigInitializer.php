<?php

declare(strict_types=1);

namespace FWK\Twig;

use Twig\Environment;

/**
 * Lets an active override plugin inject Twig functions / globals / a layout
 * override into EVERY controller render — not just routes the plugin owns.
 *
 * Discovered by class-name convention:
 *
 *     Plugins\<CamelModule>\Core\Twig\TwigInitializer
 *
 * Return value: optional layout template path (relative to themeVersion dir)
 * to use in place of the theme default; null to leave the layout untouched.
 * The `$core` env is already locked at call time — register on it via
 * `registerUndefinedFunctionCallback`, not `addFunction`/`addGlobal`.
 */
interface PluginTwigInitializer {

    public function apply(
        Environment $main,
        Environment $core,
        string $routeType,
        array $controllerData
    ): ?string;
}
