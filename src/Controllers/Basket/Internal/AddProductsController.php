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
use FWK\Core\Resources\Session;
use FWK\Services\BasketService;
use SDK\Dtos\Basket\ResponseAddFillProductCollection;
use SDK\Services\Parameters\Groups\Basket\AddProductParametersGroup;
use SDK\Services\Parameters\Groups\Basket\AddProductsParametersGroup;
use SDK\Services\Parameters\Groups\Basket\DeleteRowsParametersGroup;

/**
 * This is the AddProductsController class.
 * This class extends BaseJsonController, see this class.<br>
 *
 * @controllerData: 
 *  <p>self::RESPONSE[self::DATA] => \SDK\Dtos\Basket\Basket</p>
 * 
 * @RouteType: \FWK\Enums\RouteTypes\InternalBasket::ADD_PRODUCTS
 *
 * @responseMessageSuccess = \FWK\Enums\LanguageLabels\LanguageLabels::ADDED_TO_CART;
 * @responseMessageError = \FWK\Enums\LanguageLabels\LanguageLabels::ADD_TO_CART_ERROR;
 * 
 * @uses AddItemToBasketTrait
 * 
 * @package FWK\Controllers\Basket\Internal
 */
class AddProductsController extends BaseJsonController {
    use AddItemToBasketTrait;

    protected ?BasketService $basketService = null;

    protected ?AddProductsParametersGroup $addProductsParameters = null;

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->basketService = Loader::service(Services::BASKET);
        $this->appliedParameters = [];
        $this->addProductsParameters = new AddProductsParametersGroup();
        $this->responseMessage = $this->language->getLabelValue(LanguageLabels::ADDED_TO_CART, $this->responseMessage);
        $this->responseMessageError = $this->language->getLabelValue(LanguageLabels::ADD_TO_CART_ERROR, $this->responseMessageError);
    }

    /**
     * This method initialize applied parameters, runs previously to run preSendControllerBaseBatchData
     *
     */
    protected function initializeAppliedParameters(): void {
        $products = [];
        foreach ($this->getRequestParam(Parameters::PRODUCTS, true) as $product) {
            $addProductParametersGroup = new AddProductParametersGroup();
            if (!empty($product[Parameters::OPTIONS])) {
                $productOptionsParameters = [];
                $productAppliedOptions = [];
                $this->parseOptions($product[Parameters::OPTIONS], $productOptionsParameters, $productAppliedOptions);
            } else {
                $productOptionsParameters = null;
                $productAppliedOptions = null;
            }
            $appliedProducts[] = array_merge(
                $this->basketService->generateParametersGroupFromArray(
                    $addProductParametersGroup,
                    array_merge($product, [Parameters::OPTIONS => $productOptionsParameters])
                ),
                [Parameters::OPTIONS => $productAppliedOptions]
            );
            $products[] = $addProductParametersGroup;
        }
        $this->appliedParameters = array_merge(
            $this->basketService->generateParametersGroupFromArray($this->addProductsParameters, array_merge($this->getRequestParams(), [Parameters::PRODUCTS => $products])),
            [Parameters::PRODUCTS => $appliedProducts]
        );
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FilterInputFactory::getAddProductsParameters();
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
     * @return \SDK\Dtos\Basket\ResponseAddFillProductCollection
     * 
     */
    protected function addProducts(): ResponseAddFillProductCollection {
        $id = $this->getRequestParam(Parameters::ID, false, null);
        $type = $this->getRequestParam(Parameters::TYPE, false, null);
        if (!is_null($id) && !is_null($type) && $type == 'GRID') {
            $hashes = [];
            if (isset(Session::getInstance()->getBasketGridProducts()[$id])) {
                foreach (Session::getInstance()->getBasketGridProducts()[$id]->getCombinations() as $combination) {
                    if (!empty($combination->hash)) {
                        $hashes[] = $combination->hash;
                    }
                }
            }
            if (!empty($hashes)) {
                $deleteRowsParametersGroup = new DeleteRowsParametersGroup();
                $deleteRowsParametersGroup->setHashes($hashes);
                $response = $this->basketService->deleteRows($deleteRowsParametersGroup);
                if (!is_null($response->getError())) {
                    return $response;
                }
            }
        }

        return $this->basketService->addProducts($this->addProductsParameters);
    }

    /**
     * This method launches the adequate actions against the SDK (through the FWK services) and returns the response data. 
     *
     * @return Element
     */
    protected function getResponseData(): ?Element {
        return $this->expressCheckoutRedirect($this->addProducts());
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
