<?php

namespace FWK\Core\Controllers;

use FWK\Core\Exceptions\CommerceException;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\FilterInput\FilterInput;
use FWK\Enums\Parameters;
use SDK\Services\Parameters\Groups\Product\ProductsParametersGroup;
use SDK\Core\Resources\BatchRequests;
use FWK\Core\Resources\Loader;
use FWK\Core\Resources\SeoItems;
use FWK\Enums\Services;
use FWK\Core\Theme\Dtos\ItemList;
use FWK\Services\ProductService;
use SDK\Core\Dtos\ElementCollection;

/**
 * This is the filtrable product list trait.
 *
 * @internal This trait has been created to share common code between some controllers (like ProductsFeaturedController, ProductsOffersController,...)
 *          
 * @package FWK\Core\Controllers
 */
trait FiltrableProductListTrait {

    protected array $productsFilter = [];

    protected ?ItemList $itemListConfiguration = null;

    protected array $additionalRequestParameters = [];

    protected ?ProductService $productService = null;

    protected ?ProductsParametersGroup $productsProductParametersGroup = null;

    protected string $itemListDataKey = '';

    /**
     * This method initializes:
     * <ul>
     * <li>The ProductsParametersGroup for the SDK communication (into $this->productsProductParametersGroup)</li>
     * <li>The given configuration theme configuration data (into $this->itemListConfiguration)</li>
     * <li>The given additional request parameters (into $this->additionalRequestParameters)</li>
     * </ul>
     *
     * @param string $itemListDataKey
     * @param ItemList $itemListConfiguration
     * @param array $additionalRequestParameters
     *
     * @return void
     */
    protected function initFiltrableProductList(string $itemListDataKey, ItemList $itemListConfiguration, array $additionalRequestParameters = []): void {
        $this->itemListDataKey = $itemListDataKey;
        $this->productsProductParametersGroup = new ProductsParametersGroup();
        $this->itemListConfiguration = $itemListConfiguration;
        $this->additionalRequestParameters = $additionalRequestParameters;
        $this->productService = Loader::service(Services::PRODUCT);
    }

    /**
     * This method adds the given additionalRequestParameters.
     *
     * @param array $additionalRequestParameters
     *
     * @return void
     */
    protected function addAdditionalRequestParameters(array $additionalRequestParameters = []): void {
        $this->additionalRequestParameters = $additionalRequestParameters;
    }

    /**
     * This method returns the origin of the params (see FilterInputHandler::PARAMS_FROM_GET, FilterInputHandler::PARAMS_FROM_QUERY_STRING or FilterInputHandler::PARAMS_FROM_POST,...).
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     *
     * @see FilterInputHandler
     */
    protected function getOriginParams() {
        return FilterInputHandler::PARAMS_FROM_QUERY_STRING;
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        $result = FilterInputFactory::getProductsListParameters();
        $result = [
            Parameters::PER_PAGE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_AVAILABLE_VALUES => $this->itemListConfiguration->getViewOptions()->getPerPage()->getAvailablePaginations()
            ]),
            Parameters::TEMPLATE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_AVAILABLE_VALUES => $this->itemListConfiguration->getViewOptions()->getTemplate()->getAvailableTemplates()
            ]),
            Parameters::Q => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_REGEX_VALIDATE => '/^.{' . $this->itemListConfiguration->getApplicableFilters()->getQ()->getQMinCharacters() . ',}$/'
            ])
        ] + $result;

        return $result;
    }

    private function prepareProductParametersGroup(): void {
        $productRequest = array_merge(
            $this->itemListConfiguration->getDefaultParametersValues(),
            array_filter($this->getRequestParams()),
            $this->itemListConfiguration->getRequestParameters(),
            $this->additionalRequestParameters
        );
        if (isset($productRequest[Parameters::PRICE_RANGE]) && $productRequest[Parameters::PRICE_RANGE] !== null) {
            $listValues = explode(FilterInput::REGEX_VALIDATE_RANGE_SEPARATOR, $productRequest[Parameters::PRICE_RANGE]);
            if ($listValues[0] <= $listValues[1]) {
                $productRequest[Parameters::FROM_PRICE] = $listValues[0];
                $productRequest[Parameters::TO_PRICE] = $listValues[1];
            } else {
                $productRequest[Parameters::TO_PRICE] = $listValues[0];
                $productRequest[Parameters::FROM_PRICE] = $listValues[1];
            }
        }
        $this->productsFilter = $this->productService->generateParametersGroupFromArray($this->productsProductParametersGroup, $productRequest);
        $this->setDataValue('productsFilter', $this->productsFilter);
    }

    /**
     * This method adds the get products request (with the corresponding requestParams) to the given batch request.
     *
     * @param BatchRequests $requests
     *
     * @return void
     */
    protected function addGetProductsToBatchRequest(BatchRequests $requests): void {
        $this->prepareProductParametersGroup();
        $this->productService->addGetProducts($requests, $this->itemListDataKey, $this->productsProductParametersGroup);
    }

    /**
     * This method gets the products and sets it to the controller data.
     *
     */
    protected function setProducts(): void {
        $this->prepareProductParametersGroup();
        $products = $this->productService->getProducts($this->productsProductParametersGroup);
        $this->checkCriticalServiceLoaded($products);
        $this->setDataValue($this->itemListDataKey, $products);
    }

    /**
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     */
    protected function setControllerBaseData(): void {
        parent::setControllerBaseData();
    }

    /**
     * This method is the one in charge of defining all the data batch requests that
     * are needed for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     * @return void
     */
    protected function setBatchData(BatchRequests $request): void {
    }

    /**
     * 
     *
     * @see \FWK\Core\Controllers\Controller::getSeoItems()
     */
    protected function getSeoItems(): ?SeoItems {
        $seoItems = parent::getSeoItems();
        $products = $this->getControllerData($this->itemListDataKey);
        if ($products instanceof ElementCollection && !is_null($products->getPagination())) {
            $seoItems->setPaginationValues($products->getPagination());
        }
        return $seoItems;
    }
}
