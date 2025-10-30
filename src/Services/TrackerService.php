<?php

namespace FWK\Services;

use FWK\Core\Resources\Registries\RegistryService;
use SDK\Core\Dtos\ElementCollection;
use SDK\Core\Resources\BatchRequests;
use SDK\Enums\RouteType;
use SDK\Services\Parameters\Groups\TrackerParametersGroup;
use SDK\Services\TrackerService as TrackerServiceSDK;
use FWK\Services\Traits\ServiceTrait;

/**
 * This is the TrackerService class.
 * Remember that a service is an extension of a SDK model that allows to add additional
 * actions to a model request or create new methods to simplify some common requests.
 * In this case, the TrackerService extends the SDK\Services\TrackerService.
 *
 * @see TrackerService::getTrackersByRouteType()
 * @see TrackerService::getTrackersByPageType()
 * @see TrackerService::getTrackersByRouteTypeAndPageType()
 * @see TrackerService::addGetTrackersByRouteType()
 * @see TrackerService::addGetTrackersByPageType()
 * @see TrackerService::addGetTrackersByRouteTypeAndPageType()
 * @see TrackerService::isTrackableRouteType()
 *
 * @see TrackerService
 *
 * @package FWK\Services
 */
class TrackerService extends TrackerServiceSDK {
    use ServiceTrait;

    private const REGISTRY_KEY = RegistryService::TRACKER_SERVICE;

    private const ADD_FILTER_INTERVAL_PARAMETERS = [];

    private const ADD_FILTER_ID_VALUE_PARAMETERS = [];

    /**
     * This method returns the Dtos of those trackers whose routeType matches the given one.
     * 
     * @param string $routeType
     * 
     * @return ElementCollection|NULL
     */
    public function getTrackersByRouteType(string $routeType): ?ElementCollection {
        return $this->getTrackers($this->getParametersByRouteType($routeType));
    }

    /**
     * This method returns the Dtos of those trackers whose pageType matches the given one.
     * 
     * @param string $pageType
     * 
     * @return ElementCollection|NULL
     */
    public function getTrackersByPageType(string $pageType): ?ElementCollection {
        return $this->getTrackers($this->getParametersByPageType($pageType));
    }

    /**
     * This method returns the Dtos of those trackers whose routeType and pageType match the given one.
     * 
     * @param string $pageType
     * @param string $routeType
     * 
     * @return ElementCollection|NULL
     */
    public function getTrackersByRouteTypeAndPageType(string $pageType, string $routeType): ?ElementCollection {
        return $this->getTrackers($this->getParametersByRouteTypeAndPageType($routeType, $pageType));
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
    public function addGetTrackersByRouteType(BatchRequests $batchRequests, string $batchName, string $routeType): void {
        $this->addGetTrackers($batchRequests, $batchName, $this->getParametersByRouteType($routeType));
    }

    /**
     * This method adds the batch request to get those trackers whose pageType matches the given one.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     * @param string $pageType
     * 
     * @return void
     */
    public function addGetTrackersByPageType(BatchRequests $batchRequests, string $batchName, string $pageType): void {
        $this->addGetTrackers($batchRequests, $batchName, $this->getParametersByPageType($pageType));
    }

    /**
     * This method adds the batch request to get those trackers whose routeType and pageType match the given one.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     * @param string $pageType
     * @param string $routeType
     * 
     * @return void
     */
    public function addGetTrackersByRouteTypeAndPageType(BatchRequests $batchRequests, string $batchName, string $pageType, string $routeType): void {
        $this->addGetTrackers($batchRequests, $batchName, $this->getParametersByRouteTypeAndPageType($routeType, $pageType));
    }

    private function getParametersByRouteType(string $routeType): TrackerParametersGroup {
        $trackerParametersGroup = new TrackerParametersGroup();
        $trackerParametersGroup->setRouteType(self::parseRouteType($routeType));
        return $trackerParametersGroup;
    }

    private function getParametersByPageType(string $pageType): TrackerParametersGroup {
        $trackerParametersGroup = new TrackerParametersGroup();
        $trackerParametersGroup->setPageType($pageType);
        return $trackerParametersGroup;
    }

    private function getParametersByRouteTypeAndPageType(string $routeType, string $pageType): TrackerParametersGroup {
        $trackerParametersGroup = new TrackerParametersGroup();
        $trackerParametersGroup->setRouteType(self::parseRouteType($routeType));
        $trackerParametersGroup->setPageType($pageType);
        return $trackerParametersGroup;
    }

    /**
     * This method returns if the routeType given is trackeable.
     * 
     * @param string $routeType
     *
     * @return bool
     *
     */
    public static function isTrackableRouteType(string $routeType): bool {
        return RouteType::isValid(self::parseRouteType($routeType));
    }

    private static function parseRouteType(string $routeType): string {
        if (strpos($routeType, RouteType::PAGE . "_") !== false) {
            $routeType = RouteType::PAGE;
        }
        return $routeType;
    }
}
