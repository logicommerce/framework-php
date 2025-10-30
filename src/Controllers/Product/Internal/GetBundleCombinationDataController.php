<?php

namespace FWK\Controllers\Product\Internal;

use FWK\Core\Controllers\BaseJsonController;
use FWK\Core\Controllers\Traits\BuidlProductOptionParametersGroupTrait;
use FWK\Enums\Services;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\Resources\Loader;
use SDK\Dtos\Common\Route;
use SDK\Core\Dtos\Element;
use SDK\Core\Resources\BatchRequests;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Enums\Parameters;
use SDK\Services\Parameters\Groups\Basket\BundleItemParametersGroup;
use SDK\Services\Parameters\Groups\Product\GetBundleDefinitionsGroupingsCombinationDataParametersGroup;
use FWK\Services\ProductService;

/**
 * This is the GetBundleCombinationDataController class.
 * This class extends BaseJsonController (FWK\Core\Controllers\BaseJsonController), see this class.
 *
 * @see BaseJsonController
 *
 * @package FWK\Controllers\Product\Internal
 */
class GetBundleCombinationDataController extends BaseJsonController {
    use BuidlProductOptionParametersGroupTrait;

    protected ?ProductService $productService = null;

    protected ?GetBundleDefinitionsGroupingsCombinationDataParametersGroup $getBundleDefinitionsGroupingsCombinationDataParametersGroup = null;

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->productService = Loader::service(Services::PRODUCT);
        $this->appliedParameters = [];
        $this->getBundleDefinitionsGroupingsCombinationDataParametersGroup = new GetBundleDefinitionsGroupingsCombinationDataParametersGroup();
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
                $this->productService->generateParametersGroupFromArray(
                    $bundleItemParametersGroup,
                    array_merge($item, [Parameters::OPTIONS => $itemOptionsParameters])
                ),
                [Parameters::OPTIONS => $itemAppliedOptions]
            );

            $items[] = $bundleItemParametersGroup;
        }
        $this->appliedParameters = array_merge(
            $this->productService->generateParametersGroupFromArray($this->getBundleDefinitionsGroupingsCombinationDataParametersGroup, array_merge($this->getRequestParams(), [Parameters::ITEMS => $items])),
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
        return FilterInputFactory::getGetBundleCalculateDataParameters();
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
        return $this->productService->getBundleDefinitionsGroupingsCombinationData($this->getBundleDefinitionsGroupingsCombinationDataParametersGroup);
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
