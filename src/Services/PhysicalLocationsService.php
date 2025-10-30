<?php

namespace FWK\Services;

use FWK\Core\Resources\Registries\RegistryService;
use SDK\Core\Dtos\ElementCollection;
use SDK\Core\Resources\BatchRequests;
use SDK\Services\Parameters\Groups\PhysicalLocationParametersGroup;
use SDK\Services\PhysicalLocationsService as PhysicalLocationsServiceSDK;
use FWK\Services\Traits\ServiceTrait;
use SDK\Dtos\Catalog\PhysicalLocation;

/**
 * This is the PhysicalLocationsService class.
 * Remember that a service is an extension of a SDK model that allows to add additional
 * actions to a model request or create new methods to simplify some common requests.
 * In this case, the PhysicalLocationsService extends the SDK\Services\PhysicalLocationsService.
 *
 * @see PhysicalLocationsService::getPhysicalLocationsByCountryId()
 * @see PhysicalLocationsService::getPhysicalLocationsByLatitudeAndLongitude()
 * @see PhysicalLocationsService::addGetPhysicalLocationsByCountryId()
 * @see PhysicalLocationsService::addGetPhysicalLocationsByLatitudeAndLongitude()
 * @see PhysicalLocationsService::getAllPhysicalLocations
 *
 * @see PhysicalLocationsService
 *
 * @package FWK\Services
 */
class PhysicalLocationsService extends PhysicalLocationsServiceSDK {
    
    use ServiceTrait;
    
    private const REGISTRY_KEY = RegistryService::PHYSICAL_LOCATIONS_SERVICE;

    private const ADD_FILTER_INTERVAL_PARAMETERS = [];
    
    private const ADD_FILTER_ID_VALUE_PARAMETERS = [];
    
    /**
     * This method returns the Dtos of the 'physical locations' whose countryId matches the given one.
     * 
     * @param int $countryId
     * 
     * @return ElementCollection|NULL
     */
    public function getPhysicalLocationsByCountryId(int $countryId): ?ElementCollection {
        return $this->getPhysicalLocations($this->getParametersByCountryId($countryId));
    }

    /**
     * This method returns the Dtos of the 'physical locations' whose latitude and longitude matches the given ones.
     * 
     * @param float $latitude
     * @param float $longitude
     * 
     * @return ElementCollection|NULL
     */
    public function getPhysicalLocationsByLatitudeAndLongitude(float $latitude, float $longitude): ?ElementCollection {
        return $this->getPhysicalLocations($this->getParametersByLatitudeAndLongitude($latitude, $longitude));
    }

    /**
     * This method adds the batch request to get the 'physical locations' whose countryId matches the given one.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     * @param int $countryId
     */
    public function addGetPhysicalLocationsByCountryId(BatchRequests $batchRequests, string $batchName, int $countryId): void {
        $this->addGetPhysicalLocations($batchRequests, $batchName, $this->getParametersByCountryId($countryId));
    }

    /**
     * This method adds the batch request to get the 'physical locations' whose latitude and longitude matches the given ones.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     * @param float $latitude
     * @param float $longitude
     */
    public function addGetPhysicalLocationsByLatitudeAndLongitude(BatchRequests $batchRequests, string $batchName, float $latitude, float $longitude): void {
        $this->addGetPhysicalLocations($batchRequests, $batchName, $this->getParametersByLatitudeAndLongitude($latitude, $longitude));
    }

    private function getParametersByCountryId(int $countryId): PhysicalLocationParametersGroup {
        $physicalLocationParametersGroup = new PhysicalLocationParametersGroup();
        $physicalLocationParametersGroup->setCountryId($countryId);
        return $physicalLocationParametersGroup;
    }

    private function getParametersByLatitudeAndLongitude(float $latitude, float $longitude): PhysicalLocationParametersGroup {
        $physicalLocationParametersGroup = new PhysicalLocationParametersGroup();
        $physicalLocationParametersGroup->setLatitude($latitude);
        $physicalLocationParametersGroup->setLongitude($longitude);
        return $physicalLocationParametersGroup;
    }

    /**
     * Returns all available physical locations filtered with the given parameters
     *
     * @param PhysicalLocationParametersGroup $params
     *            object with the needed filters to send to the API physical locations resource
     *
     * @return ElementCollection|NULL
     */
    public function getAllPhysicalLocations(PhysicalLocationParametersGroup $physicalLocationParametersGroup = null): ?ElementCollection {
        if(is_null($physicalLocationParametersGroup)){
            $physicalLocationParametersGroup = new PhysicalLocationParametersGroup();
        }
        return $this->getAllElementCollectionItems(PhysicalLocation::class, 'PhysicalLocations', $physicalLocationParametersGroup);
    }

}
