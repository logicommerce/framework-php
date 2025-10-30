<?php

namespace FWK\Controllers;

use FWK\Core\Controllers\BaseHtmlController;
use SDK\Core\Resources\BatchRequests;
use FWK\Core\Controllers\FiltrableProductListTrait;
use SDK\Dtos\Common\Route;
use FWK\Enums\Parameters;
use FWK\Core\Theme\Dtos\Search;
use SDK\Core\Dtos\ElementCollection;

/**
 * This is the base search controller
 * This class extends BaseHtmlController, see this class.
 *
 * @controllerData:
 *      <p>self::CONTROLLER_ITEM: array</p>
 *      <p>self::CONTROLLER_ITEM[self::PRODUCTS] => SDK\Core\Dtos\ElementCollection of SDK\Dtos\Catalog\Product\Product</p>
 * 
 * @twigContent: \themes\{{themeName}}\{{version}}\Content\Order\default.html.twig
 * @RouteType: \SDK\Enums\RouteType::SEARCH
 * 
 * @see BaseHtmlController
 *
 * @package FWK\Controllers
 */
class SearchController extends BaseHtmlController {
    use FiltrableProductListTrait {
        getOriginParams as protected FPLTraitGetOriginParams;
        getFilterParams as protected FPLTraitGetFilterParams;
        setControllerBaseData as protected FPLTraitSetControllerBaseData;
        setBatchData as protected FPLTraitSetBatchData;
    }

    private const CATEGORIES = 'categories';

    private const NEWS = 'news';

    private const PAGES = 'pages';

    private const PRODUCTS = 'products';

    private const PRODUCTS_FILTER = 'productsFilter';

    protected ?Search $searchConfiguration = null;

    protected bool $enableQuery = false;

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        $this->searchConfiguration = self::getTheme()->getConfiguration()->getSearch();
        $this->initFiltrableProductList(self::PRODUCTS, $this->searchConfiguration->getProducts()->getList());
        parent::__construct($route);
        $this->enableQuery = (strlen($this->getRequestParam(Parameters::Q, false, '')) > 0) || (strlen($this->getRequestParam(Parameters::ID_LIST, false, '')) > 0);
    }

    /**
     * This method is the one in charge of defining all the data batch requests that
     * are needed for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     * @return void
     */
    protected function getOriginParams() {
        return $this->FPLTraitGetOriginParams();
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return $this->FPLTraitGetFilterParams();
    }

    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
        if ($this->enableQuery) {
            if ($this->searchConfiguration->getCategories()->isActived()) {
                // $searchCriteria = $this->getRequestParam(Parameters::Q);
                // Todo:: Loader::service(Services::CATEGORY)->addGetCategoriesBySearchCriteria($requests, self::CATEGORIES, $searchCriteria);
            }
            if ($this->searchConfiguration->getProducts()->isActived()) {
                $this->addGetProductsToBatchRequest($requests);
            }
            if ($this->searchConfiguration->getNews()->isActived()) {
                // $searchCriteria = $this->getRequestParam(Parameters::Q);
                // Todo:: Loader::service(Services::NEWS)->addGetNewsBySearchCriteria($requests, self::NEWS, $searchCriteria);
            }
            if ($this->searchConfiguration->getPages()->isActived()) {
                // $searchCriteria = $this->getRequestParam(Parameters::Q);
                // Todo:: Loader::service(Services::PAGE)->addGetPagesBySearchCriteria($requests, self::PAGES, $searchCriteria);
            }
        }
    }

    final protected function setControllerBaseData(): void {
        $resultSearch = [];
        if ($this->enableQuery) {
            if ($this->searchConfiguration->getCategories()->isActived()) {
                // $resultSearch[self::CATEGORIES] = $this->getControllerData(self::CATEGORIES);
            }
            if ($this->searchConfiguration->getProducts()->isActived()) {
                $resultSearch[self::PRODUCTS] = $this->getControllerData(self::PRODUCTS);
                $this->deleteControllerData(self::PRODUCTS);
                $this->FPLTraitSetControllerBaseData();
            }
            if ($this->searchConfiguration->getNews()->isActived()) {
                // $resultSearch[self::NEWS] = $this->getControllerData(self::NEWS);
            }
            if ($this->searchConfiguration->getPages()->isActived()) {
                // $resultSearch[self::PAGES] = $this->getControllerData(self::PAGES);
            }
        } else {
            $resultSearch[self::PRODUCTS] = new ElementCollection([]);
            $this->setDataValue(self::PRODUCTS_FILTER, []);
        }
        $this->setDataValue(self::CONTROLLER_ITEM, $resultSearch);
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
     * This method runs after the batch requests (defined in the setBatchData methods) are resolved,
     * so here you can work with the response of the batch requests and calculate and set more needed data.
     *
     * @param array $additionalData
     *              Set additiona data to the controller data
     * 
     * @return void
     */
    protected function setData(array $additionalData = []): void {
    }
}
