<?php

namespace FWK\Services;

use FWK\Core\Resources\Registries\RegistryService;
use FWK\Services\Traits\ServiceTrait;
use SDK\Core\Dtos\ElementCollection;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Catalog\Area;
use SDK\Services\AreaService as AreaServiceSDK;
use SDK\Services\Parameters\Groups\AreaParametersGroup;

/**
 * This is the AreaService class.
 * Remember that a service is an extension of a SDK model that allows to add additional 
 * actions to a model request or create new methods to simplify some common requests.
 * In this case, the AreaService extends the SDK\Services\AreaService.
 *
 * @see AreaService::getAreaById()
 * @see AreaService::getAreasByIdList()
 * @see AreaService::getAreaByPId()
 * @see AreaService::getAreasByPosition()
 * @see AreaService::addGetAreaById()
 * @see AreaService::addGetAreasByIdList()
 * @see AreaService::addGetAreaByPId()
 * @see AreaService::addGetAreasByPosition()
 * 
 * @see AreaService
 * 
 * @package FWK\Services
 */
class AreaService extends AreaServiceSDK {
    use ServiceTrait;

    private const REGISTRY_KEY = RegistryService::AREA_SERVICE;

    private const ADD_FILTER_INTERVAL_PARAMETERS = [];

    private const ADD_FILTER_ID_VALUE_PARAMETERS = [];

    /**
     * This method returns the Dto of the area whose id matches the given one.
     * 
     * @param int $id
     * 
     * @return Area|NULL
     */
    public function getAreaById(int $id): ?Area {
        return $this->getArea($id);
    }

    /**
     * This method returns the Dtos of those areas whose id matches any of the given list.
     * 
     * @param string $idList
     * 
     * @return ElementCollection|NULL
     */
    public function getAreasByIdList(string $idList): ?ElementCollection {
        return $this->getAreas($this->getParametersByIdList($idList));
    }

    /**
     * This method returns the Dto of the area whose pId matches the given one.
     * 
     * @param string $pId
     * 
     * @return Area|NULL
     */
    public function getAreaByPId(string $pId): ?Area {
        $result = $this->getAreas($this->getParametersByPId($pId));

        if (!empty($result)) {
            return $result->getItems()[0];
        }

        return null;
    }

    /**
     * This method returns the Dtos of those areas whose position matches the given one.
     * 
     * @param int $position
     * 
     * @return ElementCollection|NULL
     */
    public function getAreasByPosition(int $position): ?ElementCollection {
        return $this->getAreas($this->getParametersByPosition($position));
    }

    /**
     * This method adds the batch request to get the area whose id matches the given one.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     * @param int $id
     */
    public function addGetAreaById(BatchRequests $batchRequests, string $batchName, int $id): void {
        $this->addGetArea($batchRequests, $batchName, $id);
    }

    /**
     * This method adds the batch request to get those areas whose id matches any of the given idList.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     * @param string $idList
     */
    public function addGetAreasByIdList(BatchRequests $batchRequests, string $batchName, string $idList): void {
        $this->addGetAreas($batchRequests, $batchName, $this->getParametersByIdList($idList));
    }

    /**
     * This method adds the batch request to get the area whose pId matches the given one.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     * @param string $pId
     */
    public function addGetAreaByPId(BatchRequests $batchRequests, string $batchName, string $pId): void {
        $this->addGetAreas($batchRequests, $batchName, $this->getParametersByPId($pId));
    }

    /**
     * This method adds the batch request to get those areas whose position matches the given one.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     * @param int $position
     */
    public function addGetAreasByPosition(BatchRequests $batchRequests, string $batchName, int $position): void {
        $this->addGetAreas($batchRequests, $batchName, $this->getParametersByPosition($position));
    }

    private function getParametersByIdList(string $idList): AreaParametersGroup {
        $areaParametersGroup = new AreaParametersGroup();
        $areaParametersGroup->setIdList($idList);
        return $areaParametersGroup;
    }

    private function getParametersByPId(string $pId): AreaParametersGroup {
        $areaParametersGroup = new AreaParametersGroup();
        $areaParametersGroup->setPId($pId);
        return $areaParametersGroup;
    }

    private function getParametersByPosition(int $position): AreaParametersGroup {
        $areaParametersGroup = new AreaParametersGroup();
        $areaParametersGroup->setPosition($position);
        return $areaParametersGroup;
    }
}
