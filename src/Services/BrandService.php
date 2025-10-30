<?php

namespace FWK\Services;

use FWK\Core\Dtos\ElementCollection as DtosElementCollection;
use FWK\Core\Resources\Loader;
use FWK\Core\Resources\Registries\RegistryService;
use FWK\Dtos\Catalog\Brand as FWKDtosBrand;
use FWK\Enums\Services;
use FWK\Services\Traits\ServiceTrait;
use SDK\Core\Dtos\ElementCollection;
use SDK\Core\Resources\BatchRequests;
use SDK\Core\Services\BatchService;
use SDK\Dtos\Catalog\Brand;
use SDK\Services\BrandService as BrandServiceSDK;
use SDK\Services\Parameters\Groups\BrandParametersGroup;

/**
 * This is the BrandService class.
 * Remember that a service is an extension of a SDK model that allows to add additional
 * actions to a model request or create new methods to simplify some common requests.
 * In this case, the BrandService extends the SDK\Services\BrandService.
 *
 * @see BrandService::getBrandById()
 * @see BrandService::getBrandsByIdList()
 * @see BrandService::getBrandByPId()
 * @see BrandService::addGetBrandById()
 * @see BrandService::addGetBrandsByIdList()
 * @see BrandService::addGetBrandByPId()
 * @see BrandService::getAllBrands()
 * @see BrandService::setProducts()
 *
 * @see BrandService
 *
 * @package FWK\Services
 */
class BrandService extends BrandServiceSDK {
    use ServiceTrait;

    private const REGISTRY_KEY = RegistryService::BRAND_SERVICE;

    private const ADD_FILTER_INTERVAL_PARAMETERS = [];

    private const ADD_FILTER_ID_VALUE_PARAMETERS = [];

    /**
     * This method returns the Dto of the brand whose id matches the given one.
     * 
     * @param int $id
     * 
     * @return Brand|NULL
     */
    public function getBrandById(int $id): ?Brand {
        return $this->getBrand($id);
    }

    /**
     * This method returns the Dtos of those brands whose id matches any of the given list.
     * 
     * @param string $idList
     * 
     * @return ElementCollection|NULL
     */
    public function getBrandsByIdList(string $idList): ?ElementCollection {
        return $this->getBrands($this->getParametersByIdList($idList));
    }

    /**
     * This method returns the Dto of the brand whose pId matches the given one.
     * 
     * @param string $pId
     * 
     * @return Brand|NULL
     */
    public function getBrandByPId(string $pId): ?Brand {
        $result = $this->getBrands($this->getParametersByPId($pId));

        if (!empty($result)) {
            return $result->getItems()[0];
        }

        return new Brand();
    }

    /**
     * This method adds the batch request to get the brand whose id matches the given one.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     * @param int $id
     */
    public function addGetBrandById(BatchRequests $batchRequests, string $batchName, int $id): void {
        $this->addGetBrand($batchRequests, $batchName, $id);
    }

    /**
     * This method adds the batch request to get those brands whose id matches any of the given idList.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     * @param string $idList
     */
    public function addGetBrandsByIdList(BatchRequests $batchRequests, string $batchName, string $idList): void {
        $this->addGetBrands($batchRequests, $batchName, $this->getParametersByIdList($idList));
    }

    /**
     * This method adds the batch request to get the brand whose pId matches the given one.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     * @param string $pId
     */
    public function addGetBrandByPId(BatchRequests $batchRequests, string $batchName, string $pId): void {
        $this->addGetBrands($batchRequests, $batchName, $this->getParametersByPId($pId));
    }

    private function getParametersByIdList(string $idList): BrandParametersGroup {
        $brandParametersGroup = new BrandParametersGroup();
        $brandParametersGroup->setIdList($idList);
        return $brandParametersGroup;
    }

    private function getParametersByPId(string $pId): BrandParametersGroup {
        $brandParametersGroup = new BrandParametersGroup();
        $brandParametersGroup->setPId($pId);
        return $brandParametersGroup;
    }

    /**
     * Returns all brands
     *
     * @param BrandParametersGroup $params
     *            object with the needed filters to send to the API brands resource
     *
     * @return ElementCollection|NULL
     */
    public function getAllBrands(BrandParametersGroup $params = null): ?ElementCollection {
        if (is_null($params)) {
            $params = new BrandParametersGroup();
        }
        return $this->getAllElementCollectionItems(Brand::class, 'Brands', $params);
    }


    /**
     * This method sets the products of each of the given brands.
     * 
     * @internal 
     * It launches a batch request to get the products of each brand in a single request to the API.
     * $brand->products = Loader::service(Services::PRODUCT)->addGetProductsByBrandId($category->getId());
     * 
     * @param ElementCollection $brands
     * 
     * @return void
     */
    public function setProducts(ElementCollection &$brands): void {
        $batchRequest = new BatchRequests();
        $productService = Loader::service(Services::PRODUCT);
        foreach ($brands as $key => $brand) {
            $productService->addGetProductsByBrandId($batchRequest, $key, $brand->getId());
        }
        $batchResult = BatchService::getInstance()->send($batchRequest);
        $brands = DtosElementCollection::fillFromParentCollection($brands, FWKDtosBrand::class);
        foreach ($brands as $key => $brand) {
            $brand->setProducts($batchResult[$key]);
        }
    }
}
