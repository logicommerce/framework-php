<?php

namespace FWK\Controllers\Product\Internal;

use FWK\Core\Controllers\BaseJsonController;
use FWK\Enums\LanguageLabels;
use FWK\Enums\Parameters;
use FWK\Enums\Services;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\Resources\Loader;
use SDK\Dtos\Common\Route;
use FWK\Core\Form\FormFactory;
use FWK\Services\ProductService;
use SDK\Core\Dtos\Element;
use SDK\Core\Resources\BatchRequests;
use SDK\Services\Parameters\Groups\Product\QueryParametersGroup;
use FWK\Core\Controllers\Traits\CheckCaptcha;

/**
 * This is the SetContactController class.
 * This class extends BaseJsonController (FWK\Core\Controllers\BaseJsonController), see this class.
 *
 * @see BaseJsonController
 * 
 * @uses CheckCaptcha
 *
 * @package FWK\Controllers\Product\Internal
 */
class SetContactController extends BaseJsonController {
    use CheckCaptcha;

    private ?ProductService $productService = null;

    private ?QueryParametersGroup $queryParameters = null;

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->productService = Loader::service(Services::PRODUCT);
        $this->queryParameters = new QueryParametersGroup();
        $this->responseMessage = $this->language->getLabelValue(LanguageLabels::CONTACT_RESPONSE_OK);
        $this->responseMessageError = $this->language->getLabelValue(LanguageLabels::CONTACT_RESPONSE_KO);
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FormFactory::getProductContact()->getInputFilterParameters();
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
        $this->checkCaptcha();
        $parameters = $this->getRequestParams();
        $parameters[Parameters::NAME] = $this->getRequestParam(Parameters::NAME, false) . ' ' . $this->getRequestParam(Parameters::FIRST_NAME, false) . ' ' . $this->getRequestParam(Parameters::LAST_NAME, false);
        $this->appliedParameters += $this->productService->generateParametersGroupFromArray($this->queryParameters, $parameters);
        return $this->productService->query($this->getRequestParam(Parameters::ID, true), $this->queryParameters, self::getTheme()->getConfiguration()->getDataValidators()->getProductContact());
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
