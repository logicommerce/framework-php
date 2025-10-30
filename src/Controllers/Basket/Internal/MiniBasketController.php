<?php

namespace FWK\Controllers\Basket\Internal;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\Controllers\Traits\DeleteRowsTrait;
use FWK\Core\Controllers\Traits\DeleteRowTrait;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\Resources\Loader;
use FWK\Core\Theme\Dtos\CommerceLockedStock;
use FWK\Enums\Parameters;
use FWK\Enums\Services;
use FWK\Services\BasketService;
use FWK\Services\SettingsService;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Common\Route;
use SDK\Enums\BasketRowType;
use SDK\Services\Parameters\Groups\Basket\EditBundleParametersGroup;
use SDK\Services\Parameters\Groups\Basket\EditLinkedParametersGroup;
use SDK\Services\Parameters\Groups\Basket\EditProductParametersGroup;
use SDK\Services\Parameters\Groups\Basket\EditVoucherPurchaseParametersGroup;

/**
 * This is the MiniBasketController controller class.
 * This class extends FWK\Core\Controllers\BaseHtmlController, see this class.
 *
 * @controllerData: self::CONTROLLER_ITEM: \SDK\Dtos\Basket\Basket
 * 
 * @twigContent: \themes\{{themeName}}\{{version}}\Content\Basket\Internal\MiniBasket\default.html.twig
 * 
 * @RouteType: \FWK\Enums\RouteTypes\InternalBasket::MINI_BASKET
 *
 * @package FWK\Controllers\Basket\Internal
 */
class MiniBasketController extends BaseHtmlController {
    use DeleteRowTrait, DeleteRowsTrait;

    private ?BasketService $basketService = null;

    private ?SettingsService $settingsService = null;


    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {


        parent::__construct($route);
        $this->settingsService = Loader::service(Services::SETTINGS);
        $this->basketService = Loader::service(Services::BASKET);
    }

    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
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
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return
            FilterInputFactory::getDeleteRowsParameters() +
            FilterInputFactory::getDeleteRowParameters() +
            FilterInputFactory::getEditQuantityParameters();
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
        return FilterInputHandler::PARAMS_FROM_GET;
    }

    /**
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     */
    final protected function setControllerBaseData(): void {
        $this->basketService->extendLockedStockTimer(CommerceLockedStock::EXTEND_BY_ROUTE_VISITED, self::getTheme()->getConfiguration()->getCommerce()->getLockedStock());
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
        $this->basketService = Loader::service(Services::BASKET);
        if (!empty($this->getRequestParam(Parameters::HASH, false, ''))) {
            if ($this->getRequestParam(Parameters::QUANTITY, false, 0) == 0) {
                $this->getDeleteRowResponseData();
            } else {
                $type = $this->getRequestParam(Parameters::TYPE, true);
                $hash = $this->getRequestParam(Parameters::HASH, true);
                $quantity = $this->getRequestParam(Parameters::QUANTITY, true);
                switch ($type) {
                    case BasketRowType::PRODUCT:
                        $editProduct = new EditProductParametersGroup();
                        $editProduct->setQuantity($quantity);
                        $this->basketService->editProduct($hash, $editProduct);
                        break;
                    case BasketRowType::VOUCHER_PURCHASE:
                        $editVoucherPurchase = new EditVoucherPurchaseParametersGroup();
                        $editVoucherPurchase->setQuantity($quantity);
                        $this->basketService->editProduct($hash, $editVoucherPurchase);
                        break;
                    case BasketRowType::LINKED:
                        $editLinked = new EditLinkedParametersGroup();
                        $editLinked->setQuantity($quantity);
                        $this->basketService->editLinked($hash, $editLinked);
                        break;
                    case BasketRowType::BUNDLE:
                        $editBundle = new EditBundleParametersGroup();
                        $editBundle->setQuantity($quantity);
                        $this->basketService->editBundle($hash, $editBundle);
                        break;
                }
            }
        } else if (!empty($this->getRequestParam(Parameters::HASHES, false, []))) {
            $this->getDeleteRowsResponseData(explode(',', $this->getRequestParam(Parameters::HASHES, false, [])[0]));
        }
    }
}
