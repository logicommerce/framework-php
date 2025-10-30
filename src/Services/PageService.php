<?php

namespace FWK\Services;

use FWK\Core\Resources\Registries\RegistryService;
use SDK\Core\Dtos\ElementCollection;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Catalog\Page\Page;
use SDK\Services\PageService as PageServiceSDK;
use SDK\Services\Parameters\Groups\PageParametersGroup;
use FWK\Services\Traits\ServiceTrait;
use FWK\Services\Traits\SetRelatedItemsTrait;

/**
 * This is the PageService class.
 * Remember that a service is an extension of a SDK model that allows to add additional
 * actions to a model request or create new methods to simplify some common requests.
 * In this case, the PageService extends the SDK\Services\PageService.
 *
 * @see PageService::getPageById()
 * @see PageService::getPagesByIdList()
 * @see PageService::getPageByPId()
 * @see PageService::getPagesByPosition()
 * @see PageService::getPagesByPositionList()
 * @see PageService::getPagesByPagesGroupId()
 * @see PageService::getPagesByPagesGroupIdList()
 * @see PageService::getPagesByPagesGroupPId()
 * @see PageService::getPagesBySearchCriteria()
 * @see PageService::addGetPageById()
 * @see PageService::addGetPagesByIdList()
 * @see PageService::addGetPageByPId()
 * @see PageService::addGetPagesByPosition()
 * @see PageService::addGetPagesByPositionList()
 * @see PageService::addGetPagesByPagesGroupId()
 * @see PageService::addGetPagesByPagesGroupIdList()
 * @see PageService::addGetPagesByPagesGroupPId()
 * @see PageService::addGetPagesBySearchCriteria()
 *
 * @see PageService
 * @see ServiceTrait
 * @see SetRelatedItemsTrait
 *
 * @package FWK\Services
 */
class PageService extends PageServiceSDK {
    use ServiceTrait, SetRelatedItemsTrait;

    private const REGISTRY_KEY = RegistryService::PAGES_SERVICE;

    private const ADD_FILTER_INTERVAL_PARAMETERS = [];

    private const ADD_FILTER_ID_VALUE_PARAMETERS = [];

    private const PARAMETERS_GROUP_CLASS = PageParametersGroup::class;

    private const RELATED_ITEM_CLASS = [Page::class];

    /**
     * This method returns the Dto of the page whose id matches the given one.
     * 
     * @param int $id
     * @param int $level, default 1
     * 
     * @return Page|NULL
     */
    public function getPageById(int $id, int $level = 1): ?Page {
        return $this->getPage($id, $level);
    }

    /**
     * This method returns the Dtos of those pages whose id matches any of the given list.
     * 
     * @param string $idList
     * 
     * @return ElementCollection|NULL
     */
    public function getPagesByIdList(string $idList): ?ElementCollection {
        return $this->getPages($this->getParametersByIdList($idList));
    }

    /**
     * This method returns the Dto of the page whose pId matches the given one.
     * 
     * @param string $pId
     * 
     * @return Page|NULL
     */
    public function getPageByPId(string $pId): ?Page {
        return $this->getPages($this->getParametersByPId($pId));
    }

    /**
     * This method returns the Dtos of the pages whose parent id matches the given one.
     *
     * @param int $id
     *
     * @return Page ElementCollection|NULL
     */
    public function getPagesByParentId(int $id): ?ElementCollection {
        return $this->getPages($this->getParametersByParentId($id));
    }
    
    /**
     * This method returns the Dtos of those pages whose position matches the given one.
     * 
     * @param int $position
     * 
     * @return ElementCollection|NULL
     */
    public function getPagesByPosition(int $position): ?ElementCollection {
        return $this->getPages($this->getParametersByPosition($position));
    }

    /**
     * This method returns the Dtos of those pages whose position matches any of the given list.
     * 
     * @param string $positionList
     * 
     * @return ElementCollection|NULL
     */
    public function getPagesByPositionList(string $positionList): ?ElementCollection {
        return $this->getPages($this->getParametersByPositionList($positionList));
    }

    /**
     * This method returns the Dto of the pages whose pagesGroupId matches the given one.
     * 
     * @param int $pagesGroupId
     * 
     * @return ElementCollection|NULL
     */
    public function getPagesByPagesGroupId(int $pagesGroupId): ?ElementCollection {
        return $this->getPages($this->getParametersByPagesGroupId($pagesGroupId));
    }

    /**
     * This method returns the Dtos of those pages whose pagesGroupId matches any of the given list.
     * 
     * @param string $pagesGroupIdList
     * 
     * @return ElementCollection|NULL
     */
    public function getPagesByPagesGroupIdList(string $pagesGroupIdList): ?ElementCollection {
        return $this->getPages($this->getParametersByPagesGroupIdList($pagesGroupIdList));
    }

    /**
     * This method returns the Dto of the pages whose pagesGroupPId matches the given one.
     * 
     * @param string $pagesGroupPId
     * 
     * @return ElementCollection|NULL
     */
    public function getPagesByPagesGroupPId(string $pagesGroupPId): ?ElementCollection {
        return $this->getPages($this->getParametersByPagesGroupPId($pagesGroupPId));
    }

    /**
     * This method returns the Dtos of the pages that match the given search criteria.
     * 
     * @param string $searchCriteria
     * 
     * @return ElementCollection|NULL
     */
    public function getPagesBySearchCriteria(string $searchCriteria): ?ElementCollection {
        return $this->getPages($this->getParametersBySearchCriteria($searchCriteria));
    }

    /**
     * This method adds the batch request to get the page whose id matches the given one.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     * @param int $id
     * @param int $level, default 1
     */
    public function addGetPageById(BatchRequests $batchRequests, string $batchName, int $id, int $level = 1): void {
        $this->addGetPage($batchRequests, $batchName, $id, $level);
    }

    /**
     * This method adds the batch request to get those pages whose id matches any of the given idList.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     * @param string $idList
     */
    public function addGetPagesByIdList(BatchRequests $batchRequests, string $batchName, string $idList): void {
        $this->addGetPages($batchRequests, $batchName, $this->getParametersByIdList($idList));
    }

    /**
     * This method adds the batch request to get the page whose pId matches the given one.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     * @param string $pId
     */
    public function addGetPageByPId(BatchRequests $batchRequests, string $batchName, string $pId): void {
        $this->addGetPages($batchRequests, $batchName, $this->getParametersByPId($pId));
    }

    /**
     * This method adds the batch request to get the pages whose position matches the given one.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     * @param int $position
     */
    public function addGetPagesByPosition(BatchRequests $batchRequests, string $batchName, int $position): void {
        $this->addGetPages($batchRequests, $batchName, $this->getParametersByPosition($position));
    }

    /**
     * This method adds the batch request to get the pages by parent id.
     *
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     * @param int $id
     */
    public function addGetPagesByParentId(BatchRequests $batchRequests, string $batchName, int $id): void {
        $this->addGetPages($batchRequests, $batchName, $this->getParametersByParentId($id));
    }
    
    /**
     * This method adds the batch request to get those pages whose position matches any of the given idList.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     * @param string $positionList
     */
    public function addGetPagesByPositionList(BatchRequests $batchRequests, string $batchName, string $positionList): void {
        $this->addGetPages($batchRequests, $batchName, $this->getParametersByPositionList($positionList));
    }

    /**
     * This method adds the batch request to get the pages whose pagesGroupId matches the given one.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     * @param int $pagesGroupId
     */
    public function addGetPagesByPagesGroupId(BatchRequests $batchRequests, string $batchName, int $pagesGroupId): void {
        $this->addGetPages($batchRequests, $batchName, $this->getParametersByPagesGroupId($pagesGroupId));
    }

    /**
     * This method adds the batch request to get those pages whose pagesGroupId matches any of the given idList.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     * @param string $pagesGroupIdList
     */
    public function addGetPagesByPagesGroupIdList(BatchRequests $batchRequests, string $batchName, string $pagesGroupIdList): void {
        $this->addGetPages($batchRequests, $batchName, $this->getParametersByPagesGroupIdList($pagesGroupIdList));
    }

    /**
     * This method adds the batch request to get the pages whose pagesGroupPId matches the given one.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     * @param string $pagesGroupPId
     */
    public function addGetPagesByPagesGroupPId(BatchRequests $batchRequests, string $batchName, string $pagesGroupPId): void {
        $this->addGetPages($batchRequests, $batchName, $this->getParametersByPagesGroupPId($pagesGroupPId));
    }

    /**
     * This method adds the batch request to get the pages that match the given search criteria.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     * @param string $searchCriteria
     */
    public function addGetPagesBySearchCriteria(BatchRequests $batchRequests, string $batchName, string $searchCriteria): void {
        $this->addGetPages($batchRequests, $batchName, $this->getParametersBySearchCriteria($searchCriteria));
    }

    private function getParametersByIdList(string $idList): PageParametersGroup {
        $pageParametersGroup = new PageParametersGroup();
        $pageParametersGroup->setIdList($idList);
        return $pageParametersGroup;
    }
    
    private function getParametersByPId(string $pId): PageParametersGroup {
        $pageParametersGroup = new PageParametersGroup();
        $pageParametersGroup->setPId($pId);
        return $pageParametersGroup;
    }
    
    private function getParametersByParentId(int $id): PageParametersGroup {
        $pageParametersGroup = new PageParametersGroup();
        $pageParametersGroup->setParentId($id);
        return $pageParametersGroup;
    }
    
    private function getParametersBySearchCriteria(string $searchCriteria): PageParametersGroup {
        $pageParametersGroup = new PageParametersGroup();
        $pageParametersGroup->setQ($searchCriteria);
        return $pageParametersGroup;
    }

    private function getParametersByPosition(int $position): PageParametersGroup {
        $pageParametersGroup = new PageParametersGroup();
        $pageParametersGroup->setPosition($position);
        return $pageParametersGroup;
    }

    private function getParametersByPositionList(string $positionList): PageParametersGroup {
        $pageParametersGroup = new PageParametersGroup();
        $pageParametersGroup->setPositionList($positionList);
        return $pageParametersGroup;
    }

    private function getParametersByPagesGroupId(int $pagesGroupId): PageParametersGroup {
        $pageParametersGroup = new PageParametersGroup();
        $pageParametersGroup->setPagesGroupId($pagesGroupId);
        return $pageParametersGroup;
    }

    private function getParametersByPagesGroupIdList(string $pagesGroupIdList): PageParametersGroup {
        $pageParametersGroup = new PageParametersGroup();
        $pageParametersGroup->setPagesGroupIdList($pagesGroupIdList);
        return $pageParametersGroup;
    }

    private function getParametersByPagesGroupPId(string $pagesGroupPId): PageParametersGroup {
        $pageParametersGroup = new PageParametersGroup();
        $pageParametersGroup->setPagesGroupPId($pagesGroupPId);
        return $pageParametersGroup;
    }
}
