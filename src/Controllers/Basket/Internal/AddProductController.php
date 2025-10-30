<?php

namespace FWK\Controllers\Basket\Internal;

use FWK\Core\Resources\Loader;
use SDK\Services\Parameters\Groups\Basket\AddProductParametersGroup;
use SDK\Dtos\Basket\Basket;
use SDK\Core\Dtos\Element;
use SDK\Core\Resources\BatchRequests;
use FWK\Enums\Parameters;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\FilterInput\FilterInputFactory;
use SDK\Dtos\Common\Route;
use FWK\Enums\LanguageLabels;
use FWK\Enums\Services;
use FWK\Core\Controllers\BaseJsonController;
use FWK\Core\Controllers\Traits\AddItemToBasketTrait;
use FWK\Core\Resources\DateTimeFormatter;
use FWK\Services\BasketService;
use SDK\Core\Resources\Date;
use SDK\Enums\ProductType;
use SDK\Services\Parameters\Groups\Basket\AddVoucherPurchaseParametersGroup;
use SDK\Services\Parameters\Groups\Basket\LockedStocksAggregateDataParametersGroup;

/**
 * This is the AddProductController class.
 * This class extends BaseJsonController, see this class.<br>
 *
 * @controllerData: 
 *  <p>self::RESPONSE[self::DATA] => \SDK\Dtos\Basket\Basket</p>
 * 
 * @RouteType: \FWK\Enums\RouteTypes\InternalBasket::ADD_PRODUCT
 * 
 * @responseMessageSuccess = \FWK\Enums\LanguageLabels\LanguageLabels::ADDED_TO_CART;
 * @responseMessageError = \FWK\Enums\LanguageLabels\LanguageLabels::ADD_TO_CART_ERROR;
 *
 * @uses AddItemToBasketTrait
 * 
 * @package FWK\Controllers\Basket\Internal
 */
class AddProductController extends BaseJsonController {
    use AddItemToBasketTrait;

    private ?BasketService $basketService = null;

    protected ?AddProductParametersGroup $addProductParameters = null;
    
    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->basketService = Loader::service(Services::BASKET);
        $productType =  $this->getRequestParam(Parameters::PRODUCT_TYPE, false, ProductType::PRODUCT);
        if ($productType === ProductType::VOUCHER_PURCHASE) {
            $this->addProductParameters = new AddVoucherPurchaseParametersGroup();
        } else {
            $this->addProductParameters = new AddProductParametersGroup();
        }
        $this->appliedParameters = [];        
        $this->responseMessage = $this->language->getLabelValue(LanguageLabels::ADDED_TO_CART, $this->responseMessage);
        $this->responseMessageError = $this->language->getLabelValue(LanguageLabels::ADD_TO_CART_ERROR, $this->responseMessageError);        
    }

    /**
     * This method initialize applied parameters, runs previously to run preSendControllerBaseBatchData
     *
     */
    protected function initializeAppliedParameters(): void {        
        $options = $this->getRequestParam(Parameters::OPTIONS, false, []);
        if (isset($options) && count($options)) {
            $productOptionsParameters = [];
            $appliedOptions = [];
            $this->parseOptions($options, $productOptionsParameters, $appliedOptions);
        } else {
            $productOptionsParameters = null;
            $appliedOptions = null;
        }        
        $this->appliedParameters = $this->basketService->generateParametersGroupFromArray($this->addProductParameters, array_merge($this->getRequestParams(), [Parameters::OPTIONS => $productOptionsParameters]));
        $this->appliedParameters[Parameters::OPTIONS] = $appliedOptions;
    }


    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FilterInputFactory::getAddProductParameters();
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
        return FilterInputHandler::PARAMS_FROM_POST_DATA_OBJECT;
    }

    /**
     * 
     * This method adds the producto to the basket and returns it.
     * 
     * @return \SDK\Dtos\Basket\Basket
     * 
     */
    protected function addProduct(): Basket {        
        return $this->basketService->addProduct($this->addProductParameters);
    }


    /**
     * This method launches the adequate actions against the SDK (through the FWK services) and returns the response data. 
     *
     * @return Element
     */
    protected function getResponseData(): ?Element {
        $settingsService = Loader::service(Services::SETTINGS);
        if ($settingsService->getBasketStockLockingSettings()->getActive()) {
            $lockedStocksAggregateDataParametersGroup = new LockedStocksAggregateDataParametersGroup();
            $lockedStocksAggregateDataParametersGroup->setProductCombinationIdList($this->getRequestParam(Parameters::COMBINATION_ID, true));
            $lockedStocksAggregateData = $this->basketService->getLockedStocksAggregateData($lockedStocksAggregateDataParametersGroup);
            if (!empty($lockedStocksAggregateData->getItems()) && $lockedStocksAggregateData->getItems()[0]->getOthersLockedStockUnits() > 0 && ($this->getRequestParam(Parameters::QUANTITY, true) + $lockedStocksAggregateData->getItems()[0]->getMyLockedStockUnits()) > ($this->getRequestParam(Parameters::STOCK, true) - $lockedStocksAggregateData->getItems()[0]->getOthersLockedStockUnits())) {
                $response = '';
                if ($lockedStocksAggregateData->getItems()[0]->getMyLockedStockUnits() > 0) {
                    $response = str_replace('{{myLockedStockUnits}}', $lockedStocksAggregateData->getItems()[0]->getMyLockedStockUnits(), $this->language->getLabelValue(LanguageLabels::LOCKED_STOCK_ADD_PRODUCT_RESPONSE_MY_STOCK)) . '. ';
                }
                $response .= str_replace('{{othersLockedStockUnits}}', $lockedStocksAggregateData->getItems()[0]->getOthersLockedStockUnits(), $this->language->getLabelValue(LanguageLabels::LOCKED_STOCK_ADD_PRODUCT_RESPONSE_OTHER_STOCK));
                $response = str_replace('{{othersNearestLockedStockToExpireUnits}}', $lockedStocksAggregateData->getItems()[0]->getOthersNearestLockedStockToExpireUnits(), $response);
                return new class($response, $lockedStocksAggregateData->getItems()[0]->getOthersNearestLockedStockToExpireTime()) extends Element {
                    private ?string $response = null;
                    private ?Date $expireTime = null;
                    public function __construct(?string $response, ?Date $expireTime) {
                        $this->response = $response;
                        $this->expireTime = $expireTime;
                    }
                    public function jsonSerialize(): mixed {
                        return [
                            'code' => 'lockedStock',
                            'response' => $this->response,
                            'expireTime' => (new DateTimeFormatter())->getFormattedDateTime($this->expireTime,  "Y-MM-dd HH:mm:ss")
                        ];
                    }
                };
            }
        }
        return $this->expressCheckoutRedirect($this->addProduct());
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
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
    }
}
