<?php

namespace FWK\Services;

use FWK\Core\Resources\Registries\RegistryService;
use SDK\Core\Dtos\ElementCollection;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Catalog\News;
use SDK\Services\NewsService as NewsServiceSDK;
use SDK\Services\Parameters\Groups\NewsParametersGroup;
use FWK\Services\Traits\ServiceTrait;

/**
 * This is the NewsService class.
 * Remember that a service is an extension of a SDK model that allows to add additional
 * actions to a model request or create new methods to simplify some common requests.
 * In this case, the NewsService extends the SDK\Services\NewsService.
 *
 * @see NewsService::getNewsById()
 * @see NewsService::getNewsByIdList()
 * @see NewsService::getNewsByPId()
 * @see NewsService::getNewsBySearchCriteria()
 * @see NewsService::addGetNewsById()
 * @see NewsService::addGetNewsByIdList()
 * @see NewsService::addGetNewsByPId()
 * @see NewsService::addGetNewsBySearchCriteria()
 *
 * @see NewsService
 *
 * @package FWK\Services
 */
class NewsService extends NewsServiceSDK {
    use ServiceTrait;

    private const REGISTRY_KEY = RegistryService::NEWS_SERVICE;

    private const ADD_FILTER_INTERVAL_PARAMETERS = [];

    private const ADD_FILTER_ID_VALUE_PARAMETERS = [];

    /**
     * This method returns the Dto of the 'new' whose id matches the given one.
     * 
     * @param int $id
     * 
     * @return News|NULL
     */
    public function getNewsById(int $id): ?News {
        return $this->getPieceOfNews($id);
    }

    /**
     * This method returns the Dtos of those 'news' whose id matches any of the given list.
     * 
     * @param string $idList
     * 
     * @return ElementCollection|NULL
     */
    public function getNewsByIdList(string $idList): ?ElementCollection {
        return $this->getNews($this->getParametersByIdList($idList));
    }

    /**
     * This method returns the Dto of the 'new' whose pId matches the given one.
     * 
     * @param string $pId
     * 
     * @return News|NULL
     */
    public function getNewsByPId(string $pId): ?News {
        $result = $this->getNews($this->getParametersByPId($pId));

        if (!empty($result)) {
            return $result->getItems()[0];
        }

        return new News();
    }

    /**
     * This method returns the Dtos of those 'news' whose search criteria matches the given one.
     * 
     * @param string $searchCriteria
     * 
     * @return ElementCollection|NULL
     */
    public function getNewsBySearchCriteria(string $searchCriteria): ?ElementCollection {
        return $this->getNews($this->getParametersBySearchCriteria($searchCriteria));
    }

    /**
     * This method adds the batch request to get the 'new' whose id matches the given one.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     * @param int $id
     */
    public function addGetNewsById(BatchRequests $batchRequests, string $batchName, int $id): void {
        $this->addGetPieceOfNews($batchRequests, $batchName, $id);
    }

    /**
     * This method adds the batch request to get those 'news' whose id matches any of the given idList.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     * @param string $idList
     */
    public function addGetNewsByIdList(BatchRequests $batchRequests, string $batchName, string $idList): void {
        $this->addGetNews($batchRequests, $batchName, $this->getParametersByIdList($idList));
    }

    /**
     * This method adds the batch request to get the 'new' whose pId matches the given one.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     * @param string $pId
     */
    public function addGetNewsByPId(BatchRequests $batchRequests, string $batchName, string $pId): void {
        $this->addGetNews($batchRequests, $batchName, $this->getParametersByPId($pId));
    }

    /**
     * This method adds the batch request to get those 'news' that match the given search criteria.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     * @param string $searchCriteria
     */
    public function addGetNewsBySearchCriteria(BatchRequests $batchRequests, string $batchName, string $searchCriteria): void {
        $this->addGetNews($batchRequests, $batchName, $this->getParametersBySearchCriteria($searchCriteria));
    }

    private function getParametersByIdList(string $idList): NewsParametersGroup {
        $newsParametersGroup = new NewsParametersGroup();
        $newsParametersGroup->setIdList($idList);
        return $newsParametersGroup;
    }

    private function getParametersByPId(string $pId): NewsParametersGroup {
        $newsParametersGroup = new NewsParametersGroup();
        $newsParametersGroup->setPId($pId);
        return $newsParametersGroup;
    }

    private function getParametersBySearchCriteria(string $searchCriteria): NewsParametersGroup {
        $newsParametersGroup = new NewsParametersGroup();
        $newsParametersGroup->setQ($searchCriteria);
        return $newsParametersGroup;
    }
}
