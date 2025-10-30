<?php

namespace FWK\Controllers\ProductComparison\Internal;

use FWK\Core\Controllers\BaseJsonController;
use SDK\Core\Resources\BatchRequests;
use FWK\Core\Resources\Loader;
use FWK\Enums\Services;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\Resources\Utils;
use FWK\Enums\LanguageLabels;
use FWK\Enums\Parameters;
use SDK\Core\Dtos\Element;
use FWK\Services\ProductService;
use SDK\Application;
use SDK\Dtos\Common\Route;
use SDK\Services\Parameters\Groups\Product\AddComparisonProductParametersGroup;

/**
 * This is the AddComparisonProductController controller class.
 *
 * @see BaseJsonController
 *
 * @package FWK\Controllers\Comparison\Internal
 */
class AddComparisonProductController extends BaseJsonController {

    protected ?ProductService $productService = null;

    protected ?AddComparisonProductParametersGroup $addComparisonProductParametersGroup = null;

    /**
     * Constructor method.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->productService = Loader::service(Services::PRODUCT);
        $this->addComparisonProductParametersGroup = new AddComparisonProductParametersGroup();
        $this->responseMessage = $this->language->getLabelValue(LanguageLabels::COMPARISON_PRODUCT_STATUS_OK, $this->responseMessage);
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FilterInputFactory::getIdParameter();
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
     * This method initialize applied parameters, runs previously to run preSendControllerBaseBatchData
     *
     */
    protected function initializeAppliedParameters(): void {
        $this->appliedParameters[Parameters::ID] = $this->getRequestParam(Parameters::ID, true);
        $this->addComparisonProductParametersGroup->setId($this->appliedParameters[Parameters::ID]);
    }

    /**
     * This method launches the adequate actions against the SDK (through the FWK services) and returns the response data. 
     *
     * @return Element
     */
    protected function getResponseData(): ?Element {
        $response = $this->productService->addProductComparison($this->addComparisonProductParametersGroup);
        if (is_null($response->getError())) {
            return $this->getSession()->getAggregateData()->getProductComparison();
        } elseif ($response->getError()->getCode() === 'A01000-PRODUCT_COMPARISON_ADD_PRODUCT_EXISTS') {
            return new class($response->getError()->getCode()) extends Element {
                protected ?string $code = '';
                public function __construct(string $code) {
                    $this->code = $code;
                }
                public function jsonSerialize(): mixed {
                    return [
                        'errorCode' => $this->code
                    ];
                }
            };
        } else {
            $this->responseMessageError = str_replace('{{productComparisonMax}}', Application::getInstance()->getEcommerceSettings()->getCatalogSettings()->getProductComparisonMax(), Utils::getErrorLabelValue($response));
        }
        return $response;
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
