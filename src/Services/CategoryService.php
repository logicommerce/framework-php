<?php

namespace FWK\Services;

use FWK\Core\Dtos\ElementCollection as DtosElementCollection;
use FWK\Dtos\Catalog\Category as FWKDtosCategory;
use FWK\Dtos\Catalog\Brand as FWKDtosBrand;
use SDK\Core\Dtos\ElementCollection;
use SDK\Core\Services\BatchService;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Catalog\Category;
use SDK\Services\CategoryService as CategoryServiceSDK;
use SDK\Services\Parameters\Groups\CategoryParametersGroup;
use FWK\Core\Resources\Loader;
use FWK\Core\Resources\Registries\RegistryService;
use FWK\Enums\Services;
use FWK\Services\Traits\ServiceTrait;
use FWK\Services\Traits\SetRelatedItemsTrait;
use SDK\Dtos\Catalog\CategoryTree;

/**
 * This is the CategoryService class.
 * Remember that a service is an extension of a SDK model that allows to add additional
 * actions to a model request or create new methods to simplify some common requests.
 * In this case, the CategoryService extends the SDK\Services\CategoryService.
 *
 * @see CategoryService::getCategoryById()
 * @see CategoryService::getCategoriesByIdList()
 * @see CategoryService::getCategoryByPId()
 * @see CategoryService::getCategoriesByAreaPosition()
 * @see CategoryService::getCategoriesByParentId()
 * @see CategoryService::getCategoriesByParentIdList()
 * @see CategoryService::getCategoriesByParentPId()
 * @see CategoryService::getCategoriesBySearchCriteria()
 * @see CategoryService::addGetCategoryById()
 * @see CategoryService::addGetCategoriesByIdList()
 * @see CategoryService::addGetCategoryByPId()
 * @see CategoryService::addGetCategoriesByAreaPosition()
 * @see CategoryService::addGetCategoriesByParentId()
 * @see CategoryService::addGetCategoriesByParentIdList()
 * @see CategoryService::addGetCategoriesByParentPId()
 * @see CategoryService::addGetCategoriesBySearchCriteria()
 * @see CategoryService::setProducts()
 *
 * @see CategoryService
 * @see ServiceTrait
 * @see SetRelatedItemsTrait
 * 
 *
 * @package FWK\Services
 */
class CategoryService extends CategoryServiceSDK {
    use ServiceTrait, SetRelatedItemsTrait;

    private const REGISTRY_KEY = RegistryService::CATEGORY_SERVICE;

    private const ADD_FILTER_INTERVAL_PARAMETERS = [];

    private const ADD_FILTER_ID_VALUE_PARAMETERS = [];

    private const RELATED_ITEM_CLASS = [Category::class, CategoryTree::class];

    /**
     * This method returns the Dto of the category whose id matches the given one.
     * 
     * @param int $id
     * 
     * @return Category|NULL
     */
    public function getCategoryById(int $id): ?Category {
        return $this->getCategory($id);
    }

    /**
     * This method returns the Dtos of those categories whose id matches any of the given list.
     * 
     * @param string $idList
     * 
     * @return ElementCollection|NULL
     */
    public function getCategoriesByIdList(string $idList): ?ElementCollection {
        return $this->getCategories($this->getParametersByIdList($idList));
    }

    /**
     * This method returns the Dto of the category whose pId matches the given one.
     * 
     * @param string $pId
     * 
     * @return Category|NULL
     */
    public function getCategoryByPId(string $pId): ?Category {
        $result = $this->getCategories($this->getParametersByPId($pId));

        if (!empty($result)) {
            return $result->getItems()[0];
        }

        return new Category();
    }

    /**
     * This method returns the Dtos of those categories whose area position matches the given one.
     * 
     * @param int $areaPosition
     * 
     * @return ElementCollection|NULL
     */
    public function getCategoriesByAreaPosition(int $areaPosition): ?ElementCollection {
        return $this->getCategories($this->getParametersByAreaPosition($areaPosition));
    }

    /**
     * This method returns the Dtos of those categories whose parentId matches the given one.
     * 
     * @param int $parentId
     * 
     * @return ElementCollection|NULL
     */
    public function getCategoriesByParentId(int $parentId): ?ElementCollection {
        return $this->getCategories($this->getParametersByParentId($parentId));
    }

    /**
     * This method returns the Dtos of those categories whose parentId matches any of the given list.
     * 
     * @param string $parentIdList
     * 
     * @return ElementCollection|NULL
     */
    public function getCategoriesByParentIdList(string $parentIdList): ?ElementCollection {
        return $this->getCategories($this->getParametersByParentIdList($parentIdList));
    }

    /**
     * This method returns the Dtos of those categories whose parentPId matches the given one.
     * 
     * @param string $parentPId
     * 
     * @return ElementCollection|NULL
     */
    public function getCategoriesByParentPId(string $parentPId): ?ElementCollection {
        return $this->getCategories($this->getParametersByParentPId($parentPId));
    }

    /**
     * This method returns the Dtos of those categories whose search criteria matches the given one.
     * 
     * @param string $searchCriteria
     * 
     * @return ElementCollection|NULL
     */
    public function getCategoriesBySearchCriteria(string $searchCriteria): ?ElementCollection {
        return $this->getCategories($this->getParametersBySearchCriteria($searchCriteria));
    }

    /**
     * This method adds the batch request to get the category whose id matches the given one.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     * @param int $id
     */
    public function addGetCategoryById(BatchRequests $batchRequests, string $batchName, int $id): void {
        $this->addGetCategory($batchRequests, $batchName, $id);
    }

    /**
     * This method adds the batch request to get those categories whose id matches any of the given idList.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     * @param string $idList
     */
    public function addGetCategoriesByIdList(BatchRequests $batchRequests, string $batchName, string $idList): void {
        $this->addGetCategories($batchRequests, $batchName, $this->getParametersByIdList($idList));
    }

    /**
     * This method adds the batch request to get the category whose pId matches the given one.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     * @param string $pId
     */
    public function addGetCategoryByPId(BatchRequests $batchRequests, string $batchName, string $pId): void {
        $this->addGetCategories($batchRequests, $batchName, $this->getParametersByPId($pId));
    }

    /**
     * This method adds the batch request to get those categories whose areaPosition matches the given one.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     * @param int $areaPosition
     */
    public function addGetCategoriesByAreaPosition(BatchRequests $batchRequests, string $batchName, int $areaPosition): void {
        $this->addGetCategories($batchRequests, $batchName, $this->getParametersByAreaPosition($areaPosition));
    }

    /**
     * This method adds the batch request to get those categories whose parentId matches the given one.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     * @param int $parentId
     */
    public function addGetCategoriesByParentId(BatchRequests $batchRequests, string $batchName, int $parentId): void {
        $this->addGetCategories($batchRequests, $batchName, $this->getParametersByParentId($parentId));
    }

    /**
     * This method adds the batch request to get those categories whose parentId matches any of the given idList.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     * @param string $parentIdList
     */
    public function addGetCategoriesByParentIdList(BatchRequests $batchRequests, string $batchName, string $parentIdList): void {
        $this->addGetCategories($batchRequests, $batchName, $this->getParametersByParentIdList($parentIdList));
    }

    /**
     * This method adds the batch request to get those categories whose parentPId matches the given one.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     * @param string $parentPId
     */
    public function addGetCategoriesByParentPId(BatchRequests $batchRequests, string $batchName, string $parentPId): void {
        $this->addGetCategories($batchRequests, $batchName, $this->getParametersByParentPId($parentPId));
    }

    /**
     * This method adds the batch request to get those categories that match the given search criteria.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     * @param string $searchCriteria
     */
    public function addGetCategoriesBySearchCriteria(BatchRequests $batchRequests, string $batchName, string $searchCriteria): void {
        $this->addGetCategories($batchRequests, $batchName, $this->getParametersBySearchCriteria($searchCriteria));
    }

    private function getParametersByIdList(string $idList): CategoryParametersGroup {
        $categoryParametersGroup = new CategoryParametersGroup();
        $categoryParametersGroup->setIdList($idList);
        return $categoryParametersGroup;
    }

    private function getParametersByPId(string $pId): CategoryParametersGroup {
        $categoryParametersGroup = new CategoryParametersGroup();
        $categoryParametersGroup->setPId($pId);
        return $categoryParametersGroup;
    }

    private function getParametersByAreaPosition(string $areaPosition): CategoryParametersGroup {
        $categoryParametersGroup = new CategoryParametersGroup();
        $categoryParametersGroup->setAreaPosition($areaPosition);
        return $categoryParametersGroup;
    }

    private function getParametersByParentId(int $parentId): CategoryParametersGroup {
        $categoryParametersGroup = new CategoryParametersGroup();
        $categoryParametersGroup->setParentId($parentId);
        return $categoryParametersGroup;
    }

    private function getParametersByParentIdList(string $parentIdList): CategoryParametersGroup {
        $categoryParametersGroup = new CategoryParametersGroup();
        $categoryParametersGroup->setParentIdList($parentIdList);
        return $categoryParametersGroup;
    }

    private function getParametersByParentPId(string $parentPId): CategoryParametersGroup {
        $categoryParametersGroup = new CategoryParametersGroup();
        $categoryParametersGroup->setParentPId($parentPId);
        return $categoryParametersGroup;
    }

    private function getParametersBySearchCriteria(string $searchCriteria): CategoryParametersGroup {
        $categoryParametersGroup = new CategoryParametersGroup();
        $categoryParametersGroup->setQ($searchCriteria);
        return $categoryParametersGroup;
    }

    /**
     * This method sets the products of each of the given categories.
     * 
     * @internal 
     * It launches a batch request to get the products of each category in a single request to the API.
     * $category->products = Loader::service(Services::PRODUCT)->getProductsByCategoryId($category->getId());
     * 
     * @param ElementCollection $categories
     * 
     * @return void
     */
    public function setProducts(ElementCollection &$categories): void {
        $batchRequest = new BatchRequests();
        $productService = Loader::service(Services::PRODUCT);
        foreach ($categories as $key => $category) {
            $productService->addGetProductsByCategoryId($batchRequest, $key, $category->getId());
        }
        $batchResult = BatchService::getInstance()->send($batchRequest);
        $categories = DtosElementCollection::fillFromParentCollection($categories, FWKDtosCategory::class);
        foreach ($categories as $key => $category) {
            $category->setProducts($batchResult[$key]);
        }
    }

    /**
     * This method sets the subcategories of each of the given categories.
     * 
     * @internal 
     * It launches a batch request to get the subcategories of each category in a single request to the API.
     * $category->subcategories = $this->addGetCategoriesByParentId($category->getId());
     * 
     * @param ElementCollection $categories
     * 
     * @return void
     */
    public function setSubcategories(ElementCollection &$categories): void {
        $batchRequest = new BatchRequests();
        foreach ($categories as $key => $category) {
            $this->addGetCategoriesByParentId($batchRequest, $key, $category->getId());
        }
        $batchResult = BatchService::getInstance()->send($batchRequest);
        $categories = DtosElementCollection::fillFromParentCollection($categories, FWKDtosCategory::class);
        foreach ($categories as $key => $category) {
            $category->setSubcategories($batchResult[$key]);
        }
    }
}
