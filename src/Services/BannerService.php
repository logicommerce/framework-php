<?php

namespace FWK\Services;

use FWK\Core\Resources\Registries\RegistryService;
use SDK\Core\Dtos\ElementCollection;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Catalog\Banner;
use SDK\Services\BannerService as BannerServiceSDK;
use SDK\Services\Parameters\Groups\BannerParametersGroup;
use FWK\Services\Traits\ServiceTrait;

/**
 * This is the BannerService class.
 * Remember that a service is an extension of a SDK model that allows to add additional
 * actions to a model request or create new methods to simplify some common requests.
 * In this case, the BannerService extends the SDK\Services\BannerService.
 *
 * @see BannerService::getBannerById()
 * @see BannerService::getBannersByIdList()
 * @see BannerService::getBannersByPositionList()
 * @see BannerService::getBannerByPId()
 * @see BannerService::getBannersByPosition()
 * @see BannerService::addGetBannerById()
 * @see BannerService::addGetBannersByIdList()
 * @see BannerService::addGetBannerByPId()
 * @see BannerService::addGetBannersByPosition()
 * @see BannerService::addGetBannersByPositionList()
 *
 * @see BannerService
 *
 * @package FWK\Services
 */
class BannerService extends BannerServiceSDK {
    use ServiceTrait;

    private const REGISTRY_KEY = RegistryService::BANNER_SERVICE;

    private const ADD_FILTER_INTERVAL_PARAMETERS = [];

    private const ADD_FILTER_ID_VALUE_PARAMETERS = [];

    /**
     * This method returns the Dto of the banner whose id matches the given one.
     * 
     * @param int $id
     * 
     * @return Banner|NULL
     */
    public function getBannerById(int $id): ?Banner {
        return $this->getBanner($id);
    }

    /**
     * This method returns the Dtos of those banners whose id matches any of the given list.
     * 
     * @param string $idList
     * 
     * @return ElementCollection|NULL
     */
    public function getBannersByIdList(string $idList): ?ElementCollection {
        return $this->getBanners($this->getParametersByIdList($idList));
    }

    /**
     * This method returns the Dto of the banner whose pId matches the given one.
     * 
     * @param string $pId
     * 
     * @return Banner|NULL
     */
    public function getBannerByPId(string $pId): ?Banner {
        $result = $this->getBanners($this->getParametersByPId($pId));

        if (!empty($result)) {
            return $result->getItems()[0];
        }

        return new Banner();
    }

    /**
     * This method returns the Dtos of those banners whose position matches the given one.
     * 
     * @param int $position
     * 
     * @return ElementCollection|NULL
     */
    public function getBannersByPosition(int $position): ?ElementCollection {
        return $this->getBanners($this->getParametersByPosition($position));
    }

    /**
     * This method returns the Dtos of those banners whose position matches any of the given list.
     * 
     * @param string $positionList
     * 
     * @return ElementCollection|NULL
     */
    public function getBannersByPositionList(string $positionList): ?ElementCollection {
        return $this->getBanners($this->getParametersByPositionList($positionList));
    }

    /**
     * This method adds the batch request to get the banner whose id matches the given one.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     * @param int $id
     */
    public function addGetBannerById(BatchRequests $batchRequests, string $batchName, int $id): void {
        $this->addGetBanner($batchRequests, $batchName, $id);
    }

    /**
     * This method adds the batch request to get those banners whose id matches any of the given idList.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     * @param string $idList
     */
    public function addGetBannersByIdList(BatchRequests $batchRequests, string $batchName, string $idList): void {
        $this->addGetBanners($batchRequests, $batchName, $this->getParametersByIdList($idList));
    }

    /**
     * This method adds the batch request to get the banner whose pId matches the given one.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     * @param string $pId
     */
    public function addGetBannerByPId(BatchRequests $batchRequests, string $batchName, string $pId): void {
        $this->addGetBanners($batchRequests, $batchName, $this->getParametersByPId($pId));
    }

    /**
     * This method adds the batch request to get those banners whose position matches the given one.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     * @param int $position
     */
    public function addGetBannersByPosition(BatchRequests $batchRequests, string $batchName, int $position): void {
        $this->addGetBanners($batchRequests, $batchName, $this->getParametersByPosition($position));
    }

    /**
     * This method adds the batch request to get those banners whose position matches any of the given idList.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     * @param string $positionList
     */
    public function addGetBannersByPositionList(BatchRequests $batchRequests, string $batchName, string $positionList): void {
        $this->addGetBanners($batchRequests, $batchName, $this->getParametersByPositionList($positionList));
    }

    private function getParametersByIdList(string $idList): BannerParametersGroup {
        $bannerParametersGroup = new BannerParametersGroup();
        $bannerParametersGroup->setIdList($idList);
        return $bannerParametersGroup;
    }

    private function getParametersByPId(string $pId): BannerParametersGroup {
        $bannerParametersGroup = new BannerParametersGroup();
        $bannerParametersGroup->setPId($pId);
        return $bannerParametersGroup;
    }

    private function getParametersByPosition(int $position): BannerParametersGroup {
        $bannerParametersGroup = new BannerParametersGroup();
        $bannerParametersGroup->setPosition($position);
        return $bannerParametersGroup;
    }

    private function getParametersByPositionList(string $positionList): BannerParametersGroup {
        $bannerParametersGroup = new BannerParametersGroup();
        $bannerParametersGroup->setPositionList($positionList);
        return $bannerParametersGroup;
    }
}
