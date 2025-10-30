<?php

namespace FWK\Controllers\Basket\Internal;

use FWK\Core\Resources\Loader;
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
use FWK\Services\BasketService;
use SDK\Services\Parameters\Groups\Basket\AddBundleParametersGroup;
use SDK\Services\Parameters\Groups\Basket\BundleItemParametersGroup;

/**
 * This is the AddBundleController class.
 * 
 * @controllerData: 
 *  <p>self::RESPONSE[self::DATA] => \SDK\Dtos\Basket\Basket</p>
 * @RouteType: \FWK\Enums\RouteTypes\InternalBasket::ADD_BUNDLE
 * 
 * @responseMessageSuccess = \FWK\Enums\LanguageLabels\LanguageLabels::ADDED_TO_CART;
 * @responseMessageError = \FWK\Enums\LanguageLabels\LanguageLabels::ADD_TO_CART_ERROR;
 *  
 * @uses AddItemToBasketTrait
 *
 * @package FWK\Controllers\Basket\Internal
 */
class AddBundleController extends BaseJsonController {
    use AddItemToBasketTrait;

    protected ?BasketService $basketService = null;

    protected ?AddBundleParametersGroup $addBundleParametersGroup = null;

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->basketService = Loader::service(Services::BASKET);
        $this->appliedParameters = [];
        $this->addBundleParametersGroup = new AddBundleParametersGroup();
        $this->responseMessage = $this->language->getLabelValue(LanguageLabels::ADDED_TO_CART, $this->responseMessage);
        $this->responseMessageError = $this->language->getLabelValue(LanguageLabels::ADD_TO_CART_ERROR, $this->responseMessageError);
    }


    /**
     * This method initialize applied parameters, runs previously to run preSendControllerBaseBatchData
     *
     */
    protected function initializeAppliedParameters(): void {
        $items = [];
        foreach ($this->getRequestParam(Parameters::ITEMS, true) as $item) {
            $bundleItemParametersGroup = new BundleItemParametersGroup();
            if (!empty($item[Parameters::OPTIONS])) {
                $itemOptionsParameters = [];
                $itemAppliedOptions = [];
                $this->parseOptions($item[Parameters::OPTIONS], $itemOptionsParameters, $itemAppliedOptions);
            } else {
                $itemOptionsParameters = null;
                $itemAppliedOptions = null;
            }
            $appliedItems[] = array_merge(
                $this->basketService->generateParametersGroupFromArray(
                    $bundleItemParametersGroup,
                    array_merge($item, [Parameters::OPTIONS => $itemOptionsParameters])
                ),
                [Parameters::OPTIONS => $itemAppliedOptions]
            );

            $items[] = $bundleItemParametersGroup;
        }
        $this->appliedParameters = array_merge(
            $this->basketService->generateParametersGroupFromArray($this->addBundleParametersGroup, array_merge($this->getRequestParams(), [Parameters::ITEMS => $items])),
            [Parameters::ITEMS => $appliedItems]
        );
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FilterInputFactory::getAddBundleParameters();
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
     * This method launches the adequate actions against the SDK (through the FWK services) and returns the response data. 
     *
     * @return Element
     */
    protected function getResponseData(): ?Element {
        return $this->expressCheckoutRedirect($this->basketService->addBundle($this->addBundleParametersGroup));
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
