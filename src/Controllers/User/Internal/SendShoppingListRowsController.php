<?php

namespace FWK\Controllers\User\Internal;

use FWK\Core\Controllers\BaseJsonController;
use FWK\Core\Controllers\Traits\BuidlProductOptionParametersGroupTrait;
use SDK\Core\Dtos\Element;
use SDK\Core\Resources\BatchRequests;
use FWK\Core\Form\FormFactory;
use FWK\Core\FilterInput\FilterInputHandler;
use SDK\Dtos\Common\Route;
use FWK\Core\Resources\Loader;
use FWK\Enums\Services;
use FWK\Enums\LanguageLabels;
use FWK\Services\ProductService;
use FWK\Core\Controllers\Traits\CheckCaptcha;
use FWK\Enums\Parameters;
use SDK\Enums\RecommendItemType;
use SDK\Services\Parameters\Groups\Basket\BundleItemParametersGroup;
use SDK\Services\Parameters\Groups\Product\RecommendItemParametersGroup;
use SDK\Services\Parameters\Groups\Product\RecommendParametersGroup;

/**
 * This is the SendShoppingListRowsController controller class.
 * This class extends FWK\Core\Controllers\BaseJsonController, see this class.
 *
 * @see BaseJsonController
 * 
 * @uses CheckCaptcha
 *
 * @package FWK\Controllers\User\Internal
 */
class SendShoppingListRowsController extends BaseJsonController {
    use CheckCaptcha, BuidlProductOptionParametersGroupTrait;

    protected bool $loggedInRequired = true;

    protected ?ProductService $productService = null;

    protected ?RecommendParametersGroup $recommendParametersGroup = null;

    /**
     * Constructor method.
     * 
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->productService = Loader::service(Services::PRODUCT);
        $this->recommendParametersGroup = new RecommendParametersGroup();
        $this->responseMessageError = $this->language->getLabelValue(LanguageLabels::SEND_WISHLIST_RESPONSE_KO, $this->responseMessage);
        $this->responseMessage = $this->language->getLabelValue(LanguageLabels::SEND_WISHLIST_RESPONSE_OK, $this->responseMessage);
    }


    /**
     * This method initialize applied parameters, runs previously to run preSendControllerBaseBatchData
     *
     */
    protected function initializeAppliedParameters(): void {
        $this->checkCaptcha();
        $requestParams = $this->getRequestParams();
        $items = json_decode($this->getRequestParams()[Parameters::ITEMS], true);
        $requestParams[Parameters::ITEMS] = [];
        $appliedItems[Parameters::ITEMS] = [];
        foreach ($items as $item) {
            $appliedItem = [];
            $recommendItemParametersGroup =  new RecommendItemParametersGroup();
            $recommendItemParametersGroup->setId($item[Parameters::ID]);
            $recommendItemParametersGroup->setType($item[Parameters::TYPE]);
            $appliedItem = $recommendItemParametersGroup->toArray();
            $itemAppliedOptions = [];
            if ($item[Parameters::TYPE] === RecommendItemType::PRODUCT) {
                $productOptionsParametersGroups = [];
                $this->parseOptions($item[Parameters::OPTIONS], $productOptionsParametersGroups, $itemAppliedOptions);
                $recommendItemParametersGroup->setProductOptions($productOptionsParametersGroups);
                $appliedItem[Parameters::PRODUCT_OPTIONS] = $itemAppliedOptions;
            } else if ($item[Parameters::TYPE] === RecommendItemType::BUNDLE) {
                $bundleOptionsParametersGroups = [];
                foreach ($item[Parameters::OPTIONS] as $itemOptions) {
                    $bundleOptionsParametersGroup = new BundleItemParametersGroup();
                    $bundleOptionsParametersGroup->setId($itemOptions[Parameters::ITEM_ID]);
                    $productOptionsParametersGroups = [];
                    $itemAppliedOptionsAux = [];
                    $this->parseOptions($itemOptions[Parameters::OPTIONS], $productOptionsParametersGroups, $itemAppliedOptionsAux);
                    $itemAppliedOptions[$itemOptions[Parameters::ITEM_ID]] = $itemAppliedOptionsAux;
                    $bundleOptionsParametersGroup->setOptions($productOptionsParametersGroups);
                    $bundleOptionsParametersGroups[] = $bundleOptionsParametersGroup;
                }
                $recommendItemParametersGroup->setBundleOptions($bundleOptionsParametersGroups);
                $appliedItem[Parameters::BUNDLE_OPTIONS] = $itemAppliedOptions;
            }
            $requestParams[Parameters::ITEMS][] = $recommendItemParametersGroup;
            $appliedItems[Parameters::ITEMS][] = $appliedItem;
        }
        $this->appliedParameters = $this->productService->generateParametersGroupFromArray($this->recommendParametersGroup, $requestParams);
        $this->appliedParameters[Parameters::ITEMS] = $appliedItems[Parameters::ITEMS];
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
     * This method launches the adequate actions against the SDK (through the FWK services) and returns the response data. 
     *
     * @return Element
     */
    protected function getResponseData(): ?Element {
        return $this->productService->recommend($this->recommendParametersGroup);
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FormFactory::getSendShoppingListRows()->getInputFilterParameters();
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
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
    }
}
