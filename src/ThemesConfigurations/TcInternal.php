<?php

namespace FWK\ThemesConfigurations;

use FWK\Core\Resources\Session;
use FWK\Core\Theme\Dtos\Configuration;
use SDK\Core\Resources\BatchRequests;

/**
 * This is the TcInternal (internal  theme configuration) class.
 * The purpose of this class is to represent an internal  theme configuration.
 * <br>This class extends FWK\ThemesConfigurations\TcDefault, see this class.
 *
 * @see TcInternal::routeTypeBatchRequestsFilter()
 * @see TcInternal::addBatchRequests()
 * @see TcInternal::getCalculatedData()
 * 
 * @see TcDefault
 *
 * @package FWK\ThemesConfigurations
 */
class TcInternal extends TcDefault {

    /**
     * @see \FWK\Core\Theme\TcInterface::routeTypeBatchRequestsFilter()
     */
    public function routeTypeBatchRequestsFilter(string $routeType): bool {
        $availableRouteTypes = [];
        return in_array($routeType, $availableRouteTypes);
    }

    /**
     * @see \FWK\Core\Theme\TcInterface::addBatchRequests()
     */
    public function addBatchRequests(BatchRequests $requests, string $routeType) {
    }

    /**
     * @see \FWK\Core\Theme\TcInterface::getCalculatedData()
     */
    public function getCalculatedData(array $batchResult): array {
        return [];
    }

    /**
     * This method returns the 'theme configuration' data, encapsulated in a Configuration object. In this theme configuration, sets as configuration the default theme configutationData
     *
     * @return Configuration
     *
     * @see Configuration
     */
    public function getConfiguration(): Configuration {
        if (is_null(self::$configuration)) {
            self::$configuration = new Configuration(Session::getInstance()->getDefaultTheme()->getConfigurationData());
        }
        return self::$configuration;
    }
}
