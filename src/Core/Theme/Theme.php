<?php

namespace FWK\Core\Theme;

use FWK\Enums\RouteType;
use SDK\Core\Resources\BatchRequests;
use SDK\Core\Dtos\Theme as SDKTheme;
use SDK\Dtos\Common\Route;
use FWK\Core\Resources\Loader;
use FWK\Core\Theme\Dtos\Configuration;
use FWK\Enums\RouteItems;
use FWK\Core\Resources\Route as FWKRoute;
use FWK\Core\Resources\Utils;
use SDK\Core\Dtos\Traits\ElementTrait;

/**
 * This is the Theme class.
 * The purpose of this class is to encapsulate the informaton of a Theme and facilitates methods to access its corresponding ThemeConfiguration data.
 * This class extends SDK\Core\Dtos\Theme, see this class.
 *
 * @internal The data of the corresponding ThemeConfiguration is accessed via the functional class 'ThemeConfiguration'.
 *          
 * @see Theme::getInstance()
 * @see Theme::resetInstance()
 * @see Theme::reloadInstance()
 * @see Theme::getRouteType()
 * @see Theme::setRouteType()
 * @see Theme::getMode()
 * @see Theme::setMode()
 * @see Theme::getVersion()
 * @see Theme::setVersion()
 * @see Theme::setName()
 * @see Theme::getName()
 * @see Theme::getViewKey()
 * @see Theme::addBatchRequests()
 * @see Theme::getCalculatedData()
 * @see Theme::getConfigurationData()
 * @see Theme::getConfiguration()
 * @see Theme::runForbiddenResponse()
 *
 * @see SDKTheme
 * @see ThemeConfiguration
 *
 * @package FWK\Core\Theme
 */
class Theme extends SDKTheme {
    use ElementTrait;

    private static ?Theme $instance = null;

    private static ?Configuration $configuration = null;

    public const ROUTE_TYPE = 'routeType';

    protected string $name = '';

    protected string $mode = THEME_DEFAULT_MODE;

    protected ?string $version = THEME_DEFAULT_VERSION;

    protected string $routeType = RouteType::NOT_FOUND;

    final private function __construct(array $data) {
        $defaultRoute =  DEFAULT_ROUTE;
        $this->name = $defaultRoute[RouteItems::THEME][RouteItems::NAME];
        parent::__construct($data);
        $this->setVersion();
        $this->setMode();
    }

    /**
     * This method returns the Theme instance (singleton).
     *
     * @param array $data
     *
     * @return Theme
     */
    final public static function getInstance(array $data = []): Theme {
        if (self::$instance === null) {
            self::setInstance($data);
        }
        return self::$instance;
    }

    private static function setInstance(array $data = []): void {
        foreach (Loader::LOCATIONS as $location) {
            $class = Loader::getClassFQN('Theme', $location . 'Core\\Theme\\', '');
            if (class_exists($class)) {
                self::$instance = new $class($data);
                return;
            }
        }
    }

    /**
     * This method unsets the Theme instance (singleton).
     *
     * @return void
     */
    final public static function resetInstance(): void {
        self::$instance = null;
        self::$configuration = null;
    }

    /**
     * This method reloads the Theme instance.
     *
     * @param Route $route
     *
     * @return void
     */
    final public static function reloadInstance(Route $route = null): void {
        self::resetInstance();
        $theme = $route->getTheme();
        $auxThemeData = ($theme !== null) ? $theme->toArray() : [];
        $auxThemeData[Theme::ROUTE_TYPE] = $route->getType();
        self::setInstance($auxThemeData);
        Tc::resetInstance();
    }

    /**
     * This method returns the route type of the Theme.
     *
     * @return string
     */
    public function getRouteType(): string {
        return $this->routeType;
    }

    /**
     * This method sets the route type of the Theme.
     *
     * @param string $routeType
     *
     * @return void
     */
    protected function setRouteType(string $routeType): void {
        $this->routeType = $routeType;
    }

    /**
     * This method sets the Theme's mode.
     *
     * @param string $mode
     *
     * @return void
     */
    protected function setMode(?string $mode = null): void {
        if (is_null($mode)) {
            $this->mode = THEME_DEFAULT_MODE;
        } else {
            $this->mode = $mode;
        }
    }

    /**
     * This method returns the Theme's mode.
     *
     * @return string
     */
    public function getMode(): string {
        return $this->mode;
    }

    /**
     * This method sets the Theme's version.
     *
     * @param ?string $version
     *
     * @return void
     */
    public function setVersion(?string $version = null): void {
        $this->version = $version;
    }

    /**
     * This method returns the Theme's version.
     *
     * @return string
     */
    final public function getVersion(): string {
        if (is_null($this->version)) {
            return $this->getCalculatedVersion();
        } else {
            return $this->version;
        }
    }

    protected function getCalculatedVersion(): string {
        if (is_null($this->version)) {
            return Utils::getCamelFromSnake(FWKRoute::getDevice());
        }
        return $this->version;
    }

    /**
     * This method sets the Theme's name.
     *
     * @param string $name
     *
     * @return void
     */
    public function setName(string $name): void {
        $this->name = $name;
    }

    /**
     *
     * @see \SDK\Core\Dtos\Theme::getName()
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * This method returns the Theme's view key.
     *
     * @return string
     */
    public function getViewKey(): string {
        return ucwords(strtolower($this->getName())) . ucwords(strtolower($this->getVersion()));
    }

    /**
     * This method adds to batch request the requests defined by ThemeConfiguration.
     *
     * @see /FWK/Resources/Theme/ThemeConfiguration::addBatchRequests()
     *
     * @param BatchRequests $requests
     */
    final public function addBatchRequests(BatchRequests $requests): void {
        ThemeConfiguration::addBatchRequests($requests, $this);
    }

    /**
     * This method returns the calculated data defined by ThemeConfiguration.
     *
     * @see /FWK/Resources/Theme/ThemeConfiguration::getCalculatedData()
     *
     * @param array $batchResult
     *
     * @return array
     */
    final public function getCalculatedData(array $batchResult): array {
        return ThemeConfiguration::getCalculatedData($batchResult, $this);
    }

    /**
     * This method returns the configuration data defined by ThemeConfiguration.
     *
     * @see /FWK/Resources/Theme/ThemeConfiguration::getConfigurationData()
     *
     * @return array
     */
    final public function getConfigurationData(): array {
        return ThemeConfiguration::getConfigurationData($this);
    }

    /**
     * This method returns the configuration defined by ThemeConfiguration.
     *
     * @see /FWK/Resources/Theme/ThemeConfiguration::getConfigurationData()
     *
     * @return Configuration
     */
    final public function getConfiguration(): Configuration {
        if (self::$configuration == null) {
            self::$configuration = ThemeConfiguration::getConfiguration($this);
        }
        return self::$configuration;
    }

    /**
     * This method runs the Forbbiden response, defined by ThemeConfiguration.
     *
     * @see /FWK/Resources/Theme/ThemeConfiguration::runForbiddenResponse()
     *
     * @return void
     */
    final public function runForbiddenResponse(): void {
        ThemeConfiguration::runForbiddenResponse($this);
    }
}
