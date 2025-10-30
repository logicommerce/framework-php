<?php

namespace FWK\Services;

use FWK\Core\Resources\Registries\RegistryService;
use FWK\Core\Resources\Session;
use FWK\Core\Theme\Dtos\Configuration;
use FWK\Core\Theme\Dtos\ItemList;
use FWK\Core\Theme\Theme;
use SDK\Core\Dtos\ElementCollection;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Catalog\Product\Product;
use SDK\Services\ProductService as ProductServiceSDK;
use FWK\Enums\Parameters;
use FWK\Services\Traits\ServiceTrait;
use FWK\Services\Traits\SetRelatedItemsTrait;
use SDK\Core\Dtos\Status;
use SDK\Core\Enums\MethodType;
use SDK\Services\Parameters\Groups\Product\AddComparisonProductParametersGroup;
use SDK\Services\Parameters\Groups\Product\ProductParametersGroup;
use SDK\Services\Parameters\Groups\Product\ProductsParametersGroup;

/**
 * This is the ProductService class.
 * Remember that a service is an extension of a SDK model that allows to add additional
 * actions to a model request or create new methods to simplify some common requests.
 * In this case, the ProductService extends the SDK\Services\ProductService.
 *
 * @see ProductService::getProductById()
 * @see ProductService::getProductsByIdList()
 * @see ProductService::getProductsByPId()
 * @see ProductService::getProductsByCategoryId()
 * @see ProductService::getProductsByBrandId()
 * @see ProductService::getOffers()
 * @see ProductService::getFeatured()
 * @see ProductService::getProductsBySearchCriteria()
 * @see ProductService::addGetProductById()
 * @see ProductService::addGetProductsByIdList()
 * @see ProductService::addGetProductsByPId()
 * @see ProductService::addGetProductsByCategoryId()
 * @see ProductService::addGetProductsByBrandId()
 * @see ProductService::addGetOffers()
 * @see ProductService::addGetFeatured()
 * @see ProductService::addGetProductsBySearchCriteria()
 *
 * @see ProductService
 * @see ServiceTrait
 * @see SetRelatedItemsTrait
 *
 * @package FWK\Services
 */
class ProductService extends ProductServiceSDK {
    use ServiceTrait, SetRelatedItemsTrait;

    private const REGISTRY_KEY = RegistryService::PRODUCT_SERVICE;

    public const PRICE = 'price';

    public const PRICE_RETAIL = 'priceRetail';

    public const PRICE_BASE = 'priceBase';

    private const RELATED_ITEM_CLASS = [Product::class];

    private const ADD_FILTER_INTERVAL_PARAMETERS = [
        Parameters::FILTER_CUSTOMTAG_INTERVAL
    ];

    private const ADD_FILTER_ID_VALUE_PARAMETERS = [
        Parameters::FILTER_CUSTOMTAG_INTERVAL,
        Parameters::FILTER_CUSTOMTAG,
        Parameters::FILTER_OPTION
    ];

    private static ?Configuration $themeConfiguration = null;

    protected static function getConfiguration(): Configuration {
        if (is_null(self::$themeConfiguration)) {
            self::$themeConfiguration = Theme::getInstance()->getConfiguration();
        }
        return self::$themeConfiguration;
    }

    /**
     * This method returns the Dto of the product whose id matches the given one.
     * 
     * @param int $id
     * @param ProductParametersGroup $params
     * 
     * @return Product|NULL
     */
    public function getProductById(int $id, ProductParametersGroup $params = null): ?Product {
        return $this->getProduct($id, $params);
    }

    /**
     * Returns the products filtered with the given parameters
     *
     * @param ProductsParametersGroup $params
     *            object with the needed filters to send to the API products resource
     *
     * @return ElementCollection|NULL
     */
    public function getProducts(ProductsParametersGroup $params = null): ?ElementCollection {
        return parent::getProducts($this->getProductsParameters($params));
    }

    /**
     * This method returns the Dtos of those products whose id matches any of the given list.
     * 
     * @param string $idList
     * 
     * @return ElementCollection|NULL
     */
    public function getProductsByIdList(string $idList): ?ElementCollection {
        return $this->getProducts($this->getParametersByIdList($idList));
    }

    /**
     * This method returns the Dto of the products whose pId matches the given one.
     * 
     * @param string $pId
     * 
     * @return Product|NULL
     */
    public function getProductByPId(string $pId): ?Product {
        $result = $this->getProducts($this->getParametersByPId($pId));

        if (!empty($result)) {
            return $result->getItems()[0];
        }

        return new Product();
    }

    /**
     * This method returns the Dtos of those products whose categoryId matches the given one.
     * 
     * @param int $categoryId
     * 
     * @return ElementCollection|NULL
     */
    public function getProductsByCategoryId(int $categoryId): ?ElementCollection {
        return $this->getProducts($this->getParametersByCategoryId($categoryId));
    }

    /**
     * This method returns the Dtos of those products whose brandId matches the given one.
     * 
     * @param int $brandId
     * 
     * @return ElementCollection|NULL
     */
    public function getProductsByBrandId(int $brandId): ?ElementCollection {
        return $this->getProducts($this->getParametersByBrandId($brandId));
    }

    /**
     * This method returns the Dtos of all the products on offer.
     * 
     * @return ElementCollection|NULL
     */
    public function getOffers(): ?ElementCollection {
        return $this->getProducts($this->getParametersOnlyOffers());
    }

    /**
     * This method returns the Dtos of all the featured products.
     * 
     * @return ElementCollection|NULL
     */
    public function getFeatured(): ?ElementCollection {
        return $this->getProducts($this->getParametersOnlyFeatured());
    }

    /**
     * This method returns the Dtos of those products that match the given searchCriteria.
     * 
     * @param string $searchCriteria
     * 
     * @return ElementCollection|NULL
     */
    public function getProductsBySearchCriteria(string $searchCriteria): ?ElementCollection {
        return $this->getProducts($this->getParametersBySearchCriteria($searchCriteria));
    }

    /**
     * This method adds the batch request to get the product whose id matches the given one.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     * @param int $id
     * @param ProductParametersGroup $params
     */
    public function addGetProductById(BatchRequests $batchRequests, string $batchName, int $id, ProductParametersGroup $params = null): void {
        $this->addGetProduct($batchRequests, $batchName, $id, $params);
    }

    /**
     * Add the request to get the products filtered with the given parameters
     *
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *            the name that will identify the request on the batch return.
     * @param ProductsParametersGroup $params
     *            object with the needed filters to send to the API products resource
     *
     * @return void
     */
    public function addGetProducts(BatchRequests $batchRequests, string $batchName, ProductsParametersGroup $params = null): void {
        parent::addGetProducts($batchRequests, $batchName, $this->getProductsParameters($params));
    }

    /**
     * This method adds the batch request to get those products whose id matches any of the given idList.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     * @param string $idList
     */
    public function addGetProductsByIdList(BatchRequests $batchRequests, string $batchName, string $idList): void {
        $this->addGetProducts($batchRequests, $batchName, $this->getParametersByIdList($idList));
    }

    /**
     * This method adds the batch request to get the products whose pId matches the given one.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     * @param string $pId
     */
    public function addGetProductsByPId(BatchRequests $batchRequests, string $batchName, string $pId): void {
        $this->addGetProducts($batchRequests, $batchName, $this->getParametersByPId($pId));
    }

    /**
     * This method adds the batch request to get those products whose categoryId matches the given one.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     * @param int $categoryId
     */
    public function addGetProductsByCategoryId(BatchRequests $batchRequests, string $batchName, int $categoryId): void {
        $this->addGetProducts($batchRequests, $batchName, $this->getParametersByCategoryId($categoryId));
    }

    /**
     * This method adds the batch request to get those products whose brandId matches the given one.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     * @param int $brandId
     */
    public function addGetProductsByBrandId(BatchRequests $batchRequests, string $batchName, int $brandId): void {
        $this->addGetProducts($batchRequests, $batchName, $this->getParametersByBrandId($brandId));
    }

    /**
     * This method adds the batch request to get all the products on offer.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     */
    public function addGetOffers(BatchRequests $batchRequests, string $batchName): void {
        $this->addGetProducts($batchRequests, $batchName, $this->getParametersOnlyOffers());
    }

    /**
     * This method adds the batch request to get all the featured products.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     */
    public function addGetFeatured(BatchRequests $batchRequests, string $batchName): void {
        $this->addGetProducts($batchRequests, $batchName, $this->getParametersOnlyFeatured());
    }

    /**
     * This method adds the batch request to get those products that match the given search criteria.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     * @param string $searchCriteria
     */
    public function addGetProductsBySearchCriteria(BatchRequests $batchRequests, string $batchName, string $searchCriteria): void {
        $this->addGetProducts($batchRequests, $batchName, $this->getParametersBySearchCriteria($searchCriteria));
    }

    private function getProductsParameters(ProductsParametersGroup $productParametersGroup = null): ProductsParametersGroup {
        if (is_null($productParametersGroup)) {
            $productParametersGroup = new ProductsParametersGroup();
        }
        if (!isset($productParametersGroup->toArray()[Parameters::TAX_INCLUDED])) {
            $productParametersGroup->setTaxIncluded(self::getConfiguration()->getCommerce()->showTaxesIncluded());
        }
        return $productParametersGroup;
    }

    private function getParametersByPId(string $pId): ProductsParametersGroup {
        $productParametersGroup = new ProductsParametersGroup();
        $productParametersGroup->setPId($pId);
        return $productParametersGroup;
    }

    private function getThemeConfigurationParameters(ItemList $itemListConfiguration): ProductsParametersGroup {
        $productParametersGroup = new ProductsParametersGroup();
        $productRequest = array_merge($itemListConfiguration->getDefaultParametersValues(), $itemListConfiguration->getRequestParameters());
        $this->generateParametersGroupFromArray($productParametersGroup, $productRequest);
        return $productParametersGroup;
    }

    public function getParametersByIdList(string $idList): ProductsParametersGroup {
        $productParametersGroup = $this->getThemeConfigurationParameters(self::getConfiguration()->getCategory()->getProductList());
        $productParametersGroup->setIdList($idList);
        return $productParametersGroup;
    }

    public function getParametersByCategoryId(int $categoryId): ProductsParametersGroup {
        $productParametersGroup = $this->getThemeConfigurationParameters(self::getConfiguration()->getCategory()->getProductList());
        $productParametersGroup->setCategoryId($categoryId);
        return $productParametersGroup;
    }

    public function getParametersByBrandId(int $brandId): ProductsParametersGroup {
        $productParametersGroup = $this->getThemeConfigurationParameters(self::getConfiguration()->getBrand()->getProductList());
        $productParametersGroup->setBrandId($brandId);
        return $productParametersGroup;
    }

    public function getParametersOnlyOffers(): ProductsParametersGroup {
        $productParametersGroup = $this->getThemeConfigurationParameters(self::getConfiguration()->getOffers()->getProductList());
        $productParametersGroup->setOnlyOffers(true);
        return $productParametersGroup;
    }

    public function getParametersOnlyFeatured(): ProductsParametersGroup {
        $productParametersGroup = $this->getThemeConfigurationParameters(self::getConfiguration()->getFeatured()->getProductList());
        $productParametersGroup->setOnlyFeatured(true);
        return $productParametersGroup;
    }

    public function getParametersBySearchCriteria(string $searchCriteria): ProductsParametersGroup {
        $productParametersGroup = $this->getThemeConfigurationParameters(self::getConfiguration()->getSearch()->getProducts()->getList());
        $productParametersGroup->setQ($searchCriteria);
        return $productParametersGroup;
    }

    /**
     *
     * @see SDK\Dtos\Product\Product::addProductComparison()
     */
    public function addProductComparison(AddComparisonProductParametersGroup $addComparisonProduct = null): ?Status {
        $response = parent::addProductComparison($addComparisonProduct);
        if (is_null($response->getError())) {
            Session::getInstance()->setAggregateDataProductComparison($addComparisonProduct->toArray()[Parameters::ID], MethodType::POST);
        }
        return $response;
    }

    /**
     *
     * @see SDK\Dtos\Product\Product::deleteProductComparison()
     */
    public function deleteProductComparison(int $id): ?Status {
        $response = parent::deleteProductComparison($id);
        if (is_null($response->getError()) || $response->getError()->getCode() == 'A01000-PRODUCT_NOT_FOUND') {
            Session::getInstance()->setAggregateDataProductComparison($id, MethodType::DELETE);
        }
        return $response;
    }
}
