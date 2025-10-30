<?php

namespace FWK\Core\Resources;

use SDK\Core\Resources\Environment;
use SDK\Application;
use FWK\Core\Theme\Theme;

/**
 * This is the Assets class.
 * Remember that assets refer to all the static content (css,img,js,...) that makes up the project.
 * <br>The purpose of this class is to facilitate the assets paths.
 *
 * @see Assets::getInstance()
 * @see Assets::getAssetsPaths()
 * @see Assets::getAssetsPath()
 *
 *
 * @package FWK\Core\Resources
 */
class Assets {

    public const ENVIRONMENT_THEMES = 'themes';

    public const ENVIRONMENT_THEMES_VERSIONS = 'versions';

    public const ENVIRONMENT_COMMERCE = 'commerce';

    public const ENVIRONMENT_CUSTOMIZED = 'customized';

    public const ENVIRONMENT_CORE = 'core';

    public const ENVIRONMENT_PLUGINS = 'plugins';

    public const ENVIRONMENT_IMAGES = 'images';

    public const TYPE_CSS = 'css';

    public const TYPE_JS = 'js';

    public const TYPE_IMG = 'img';

    public const TYPE_FONT = 'fonts';

    public const ASSETS_FILE = '/assets.json';

    private array $commerceAssetsMap = [];

    private array $mapAssetsCore = [];

    private string $cdnImages = '';

    private string $cdnAssetsCommerce = '';

    private string $cdnAssetsCore = '';

    private string $cdnAssetsPlugins = '';

    private static $instance = null;

    private bool $isDevel = false;

    private ?Theme $theme = null;

    final private function __construct() {
        $this->isDevel = Environment::get('COMMERCE_MODE') === 'DEVEL';
        $this->cdnImages = Application::getInstance()->getEcommerceSettings()->getGeneralSettings()->getCdnImages();
        $fwkBuildVersion = '';
        if (defined('FWK_BUILD_VERSION')) {
            $fwkBuildVersion = '/' . FWK_BUILD_VERSION;
        } else {
            $fwkBuildVersion = '';
        }

        if ($this->isDevel) {
            $this->commerceAssetsMap = json_decode(file_get_contents(SITE_PATH . '/assets' . self::ASSETS_FILE), true);
            $this->cdnAssetsCommerce = Environment::get('CDN_ASSETS_COMMERCE');
            if (Environment::get('FWK_JS_VERSION') === 'SITE') {
                $this->cdnAssetsPlugins = $this->cdnAssetsCommerce . '/plugins';
                $this->cdnAssetsCore = $this->cdnAssetsCommerce . '/core';
                $this->mapAssetsCore = json_decode(file_get_contents(SITE_PATH . '/assets/core/' . self::ASSETS_FILE), true);
            } else {
                $this->cdnAssetsCore = Environment::get('CDN_ASSETS_CORE') . $fwkBuildVersion;
                $this->cdnAssetsPlugins = is_null(Environment::get('CDN_ASSETS_PLUGINS')) ? '' : Environment::get('CDN_ASSETS_PLUGINS');
            }
        } else {
            $this->commerceAssetsMap = [];
            $this->cdnAssetsCommerce = Environment::get('CDN_ASSETS_COMMERCE') . '/' . BUILD_VERSION . '/assets';
            $this->cdnAssetsCore = Environment::get('CDN_ASSETS_CORE') . $fwkBuildVersion;
            $this->cdnAssetsPlugins = is_null(Environment::get('CDN_ASSETS_PLUGINS')) ? '' : Environment::get('CDN_ASSETS_PLUGINS');
        }
        $this->theme = Theme::getInstance();
    }

    /**
     * This method returns the Assets instance (singleton)
     *
     * @return Assets
     */
    final public static function getInstance(): Assets {
        if (self::$instance === null) {
            self::$instance = new Assets();
        }
        return self::$instance;
    }

    /**
     * This method returns the assets paths for the given environment, type and group.
     *
     * @param string $environment
     *            Possible values: one of these Assets constants: ENVIRONMENT_CORE, ENVIRONMENT_COMMERCE, ENVIRONMENT_THEMES, ENVIRONMENT_THEMES_VERSIONS, ENVIRONMENT_CUSTOMIZED.
     * @param string $type
     *            Possible values: one of these Assets constants: TYPE_CSS, TYPE_JS, TYPE_IMG, TYPE_FONT.
     * @param string $group
     *
     * @return array
     */
    public function getAssetsPaths(string $environment, string $type, string $group): array {
        $responsePaths = [];
        if ($this->isDevel && (($environment === self::ENVIRONMENT_CORE && count($this->mapAssetsCore)) || $environment != self::ENVIRONMENT_CORE)) {
            $responsePaths = $this->getEnvironmentFilesFromDevel($environment, $type, $group);
        } else {
            $responsePaths = $this->getEnvironmentFilesFromProduction($environment, $type, $group);
        }
        return $responsePaths;
    }

    /**
     * This method returns the assets path for the given environment.
     *
     * @param string $environment
     *            Possible values: one of these Assets constants: ENVIRONMENT_CORE, ENVIRONMENT_COMMERCE, ENVIRONMENT_THEMES, ENVIRONMENT_THEMES_VERSIONS, ENVIRONMENT_CUSTOMIZED.
     *            
     * @return string
     */
    public function getAssetsPath(string $environment): string {
        $path = '';
        if (in_array($environment, [
            self::ENVIRONMENT_COMMERCE,
            self::ENVIRONMENT_THEMES,
            self::ENVIRONMENT_THEMES_VERSIONS,
            self::ENVIRONMENT_CUSTOMIZED
        ])) {
            $path = $this->cdnAssetsCommerce;
        } elseif ($environment === self::ENVIRONMENT_CORE) {
            $path = $this->cdnAssetsCore;
        } elseif ($environment === self::ENVIRONMENT_PLUGINS) {
            return $this->cdnAssetsPlugins;
        }

        if ($environment === self::ENVIRONMENT_THEMES && strlen($this->theme->getName())) {
            $path .= '/' . $environment . '/' . $this->theme->getName();
        } elseif ($environment === self::ENVIRONMENT_THEMES_VERSIONS && strlen($this->theme->getVersion())) {
            $path .= '/' . self::ENVIRONMENT_THEMES . '/' . $this->theme->getName() . '/' . $environment . '/' . $this->theme->getVersion();
        } elseif ($environment === self::ENVIRONMENT_CUSTOMIZED || $environment === self::ENVIRONMENT_CORE) {
            $path .= '';
        } else {
            $path .= '/' . $environment;
        }
        return $path;
    }

    /**
     * This method returns the assets images path for the given environment.
     *
     * @param string $environment
     *            Possible values: one of these Assets constants: ENVIRONMENT_CORE, ENVIRONMENT_COMMERCE, ENVIRONMENT_THEMES, ENVIRONMENT_THEMES_VERSIONS, ENVIRONMENT_CUSTOMIZED, ENVIRONMENT_IMAGES
     *            
     * @return string
     */
    public function getAssetsImagesPath(string $environment): string {
        if ($environment === self::ENVIRONMENT_IMAGES) {
            return $this->cdnImages;
        }
        return $this->getAssetsPath($environment) . '/' . Assets::TYPE_IMG;
    }

    /**
     * This method returns the assets fonts path for the given environment.
     *
     * @param string $environment
     *            Possible values: one of these Assets constants: 
     *                  ENVIRONMENT_CORE,
     *                  ENVIRONMENT_COMMERCE,
     *                  ENVIRONMENT_THEMES, 
     *                  ENVIRONMENT_THEMES_VERSIONS,
     *                  ENVIRONMENT_CUSTOMIZED.
     *            
     * @return string
     */
    public function getAssetsFontsPath(string $environment): string {
        return $this->getAssetsPath($environment) . '/' . Assets::TYPE_FONT;
    }

    private function getEnvironmentFilesFromProduction(string $environment, string $type, string $group): array {
        $responsePaths = [];

        if ($environment === self::ENVIRONMENT_CORE) {
            $folderPath = $type . '/dist';
            $responsePaths[] = $this->cdnAssetsCore . '/' . $folderPath . '/' . $group . '.' . $type;
        } elseif ($environment === self::ENVIRONMENT_COMMERCE) {
            $folderPath = self::ENVIRONMENT_COMMERCE . '/' . $type . '/dist';
            $responsePaths[] = $this->cdnAssetsCommerce . '/' . $folderPath . '/' . self::ENVIRONMENT_COMMERCE . ".$group" . '.' . $type;
        } elseif ($environment === self::ENVIRONMENT_THEMES && strlen($this->theme->getName())) {
            $folderPath = self::ENVIRONMENT_THEMES . '/' . $this->theme->getName() . '/' . $type . '/dist';
            $responsePaths[] = $this->cdnAssetsCommerce . '/' . $folderPath . '/' . self::ENVIRONMENT_THEMES . "." . $this->theme->getName() . ".$group" . ".$type";
        } elseif ($environment === self::ENVIRONMENT_THEMES_VERSIONS && strlen($this->theme->getName()) && strlen($this->theme->getVersion())) {
            $folderPath = self::ENVIRONMENT_THEMES . '/' . $this->theme->getName() . '/' . self::ENVIRONMENT_THEMES_VERSIONS . '/' . $type . '/dist';
            $responsePaths[] = $this->cdnAssetsCommerce . '/' . $folderPath . '/' . self::ENVIRONMENT_THEMES_VERSIONS . "." . $this->theme->getName() . "." . $this->theme->getVersion() . ".$group" . ".$type";
        } elseif ($environment === self::ENVIRONMENT_CUSTOMIZED) {
            $folderPath = $environment . '/' . $type . '/dist';
            $responsePaths[] = $this->cdnAssetsCommerce . '/' . $folderPath . '/' . $environment . ".$group" . '.' . $type;
        }
        return $responsePaths;
    }

    private function getEnvironmentFilesFromDevel(string $environment, string $type, string $group): array {
        $responsePaths = [];
        if ($environment === self::ENVIRONMENT_CORE && isset($this->mapAssetsCore[$type][$group]['files'])) {
            $responsePaths = $this->getFiles($this->mapAssetsCore[$type][$group]['files'], $this->getAssetsPath($environment) . '/' . $type, $type);
        } elseif ($environment === self::ENVIRONMENT_COMMERCE && isset($this->commerceAssetsMap[$environment][$type][$group]['files'])) {
            $responsePaths = $this->getFiles($this->commerceAssetsMap[$environment][$type][$group]['files'], $this->getAssetsPath($environment) . '/' . $type, $type);
        } elseif ($environment === self::ENVIRONMENT_THEMES && strlen($this->theme->getName()) && isset($this->commerceAssetsMap[$environment][$this->theme->getName()][$type][$group]['files'])) {
            $responsePaths = $this->getFiles($this->commerceAssetsMap[$environment][$this->theme->getName()][$type][$group]['files'], $this->getAssetsPath($environment) . '/' . $type, $type);
        } elseif ($environment === self::ENVIRONMENT_THEMES_VERSIONS && strlen($this->theme->getName()) && strlen($this->theme->getVersion()) && isset($this->commerceAssetsMap[self::ENVIRONMENT_THEMES][$this->theme->getName()][self::ENVIRONMENT_THEMES_VERSIONS][$this->theme->getVersion()][$type][$group]['files'])) {
            $responsePaths = $this->getFiles($this->commerceAssetsMap[self::ENVIRONMENT_THEMES][$this->theme->getName()][self::ENVIRONMENT_THEMES_VERSIONS][$this->theme->getVersion()][$type][$group]['files'], $this->getAssetsPath($environment) . '/' . $group . '/' . $type, $type);
        } elseif ($environment === self::ENVIRONMENT_CUSTOMIZED && isset($this->commerceAssetsMap[self::ENVIRONMENT_CUSTOMIZED][$type][$group]['files'])) {
            $responsePaths = $this->getFiles($this->commerceAssetsMap[self::ENVIRONMENT_CUSTOMIZED][$type][$group]['files'], $this->getAssetsPath($environment), $type);
        }

        return $responsePaths;
    }

    private function getFiles(array $path, string $prefix, string $availableExtension): array {
        $files = [];
        foreach ($path as $item) {
            if (is_string($item) && substr($item, strrpos($item, '.') + 1) === $availableExtension) {
                $files[] = $prefix . '/' . $item;
            }
        }
        return $files;
    }
}
