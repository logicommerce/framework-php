<?php

namespace FWK\Controllers;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\Resources\Loader;
use FWK\Enums\Services;
use SDK\Core\Resources\BatchRequests;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Services\BrandService;
use SDK\Dtos\Common\Route;
use SDK\Services\Parameters\Groups\BrandParametersGroup;

/**
 * This is the base brands controller
 * This class extends BaseHtmlController, see this class.
 *
 * @controllerData: self::CONTROLLER_ITEM: \SDK\Core\Dtos\ElementCollection of \SDK\Dtos\Catalog\Brand
 * 
 * @twigContent: \themes\{{themeName}}\{{version}}\Content\Brands\default.html.twig
 * 
 * @RouteType: \SDK\Enums\RouteType::BRANDS
 * 
 * @getAllBrands: protected bool $getAllBrands. Default value true, sets false for get paginated brands
 * 
 * @filterParams: \FWK\Core\FilterInput\FilterInputFactory::getBrandsParameters()
 * 
 * SDK\Core\Dtos\ElementCollection
 * 
 * @see BaseHtmlController
 *
 * @package FWK\Controllers
 */
class BrandsController extends BaseHtmlController {

    protected BrandService $brandService;

    protected BrandParametersGroup $brandParametersGroup;

    protected array $brandsFilter = [];

    protected bool $getAllBrands = true;

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->brandService = Loader::service(Services::BRAND);
        $this->brandParametersGroup = new BrandParametersGroup();
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
        return FilterInputFactory::getBrandsParameters();
    }

    /**
     * This method initialize applied parameters, runs previously to run preSendControllerBaseBatchData
     *
     */
    protected function initializeAppliedParameters(): void {
        $this->brandsFilter = $this->brandService->generateParametersGroupFromArray($this->brandParametersGroup, $this->getRequestParams());
    }

    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
        if (!$this->getAllBrands) {
            $this->brandService->addGetBrands($requests, self::CONTROLLER_ITEM, $this->brandParametersGroup);
        }
    }

    /**
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     */
    protected function setControllerBaseData(): void {
        if ($this->getAllBrands) {
            $this->setDataValue(self::CONTROLLER_ITEM, $this->brandService->getAllBrands($this->brandParametersGroup));
        }
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
