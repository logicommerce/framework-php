<?php

namespace FWK\Core\Theme;

use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Resources\Loader;
use SDK\Core\Resources\BatchRequests;
use FWK\Core\Theme\Dtos\Configuration;


/**
 * This is the ThemeConfiguration class.
 * This is a functional class responsible to agglutinate various usefull methods related to theme configurations.
 * <br><br>This class generally bridges the 'Theme' and its corresponding 'theme configuration': 
 * in most methods, given a 'Theme' its corresponding 'theme configuration' is searched 
 * and instantiated and the necessary actions are performed based on it.
 *
 * @see ThemeConfiguration::addBatchRequests()
 * @see ThemeConfiguration::getCalculatedData()
 * @see ThemeConfiguration::getConfigurationData()
 * @see ThemeConfiguration::getConfiguration()
 * @see ThemeConfiguration::runForbiddenResponse()
 * 
 * @see Theme
 * @see TcInterface
 * @see Tc
 *
 * @package FWK\Core\Theme
 */
class ThemeConfiguration {

    private static array $tcView = [];

    private static function themeConfiguration(string $viewKey = ''): ?TcInterface {
        if (!isset(self::$tcView[$viewKey])) {
            $locations = Loader::LOCATIONS;
            $className = 'Tc' . $viewKey;
            foreach ($locations as $location) {
                $class = $location . 'ThemesConfigurations\\' . $className;
                if (class_exists($class)) {
                    self::$tcView[$viewKey] = new $class();
                    return self::$tcView[$viewKey];
                }
            }
            throw new CommerceException('ERROR: Undefined TC for ' . $viewKey . ' as ' . $class . ' class.', CommerceException::THEME_CONFIGURATION_UNDEFINED);
        }
        return self::$tcView[$viewKey];
    }

    private static function addTcBatchRequests(BatchRequests $requests, TcInterface $tc, string $routeType) {
        if ($tc->routeTypeBatchRequestsFilter($routeType)) {
            $tc->addBatchRequests($requests, $routeType);
        }
    }

    /**
     * This method adds the given batch request to the configuration of the given theme, and it also
     * adds the root batch requests if the routeType of the given theme requires the RootBatchRequest.
     * 
     * @param BatchRequests $requests
     * @param Theme $theme
     */
    public static function addBatchRequests(BatchRequests $requests, Theme $theme) {
        $tc = self::themeConfiguration($theme->getViewKey());
        self::addTcBatchRequests($requests, $tc, $theme->getRouteType());
    }

    /**
     * This method invokes the getCalculatedData method of the configuration of the theme given by parameter. 
     * 
     * @see TcInterface::getCalculatedData()
     * 
     * @param array $batchResult
     * @param Theme $theme
     * 
     * @return array
     */
    public static function getCalculatedData(array $batchResult, Theme $theme): array {
        $tc = self::themeConfiguration($theme->getViewKey());
        return $tc->getCalculatedData($batchResult);
    }

    /**
     * This method returns the 'configuration data' of the given Theme.
     * 
     * @internal It invokes the getConfigurationData method of the configuration of the theme given by parameter.
     * 
     * @see TcInterface::getConfigurationData()
     * 
     * @param Theme $theme
     * 
     * @return array
     */
    public static function getConfigurationData(Theme $theme): array {
        $tc = self::themeConfiguration($theme->getViewKey());
        return $tc->getConfigurationData();
    }

    /**
     * This method returns the 'theme configuration' data (encapsulated in a Configuration object) of the given theme.
     * 
     * @see Tc::getConfiguration()
     * 
     * @param Theme $theme
     * 
     * @return Configuration
     */
    public static function getConfiguration(Theme $theme): Configuration {
        $tc = self::themeConfiguration($theme->getViewKey());
        return $tc->getConfiguration();
    }

    /**
     * This method invokes the runForbiddenResponse method of the configuration of the theme given by parameter.
     * 
     * @see TcInterface::runForbiddenResponse()
     * 
     * @param Theme $theme
     */
    public static function runForbiddenResponse(Theme $theme): void {
        $tc = self::themeConfiguration($theme->getViewKey());
        $tc->runForbiddenResponse($theme->getRouteType());
    }
}
