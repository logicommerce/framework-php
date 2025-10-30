<?php

namespace FWK\Controllers\Page\Internal;

use FWK\Core\Controllers\BaseJsonController;
use FWK\Enums\LanguageLabels;
use FWK\Core\FilterInput\FilterInputHandler;
use SDK\Dtos\Common\Route;
use FWK\Core\Form\FormFactory;
use SDK\Core\Dtos\Element;
use SDK\Core\Resources\BatchRequests;
use FWK\Core\Resources\Loader;
use FWK\Enums\Services;

/**
 * This is the SponsorShipController class.
 * This class extends BaseJsonController (FWK\Core\Controllers\BaseJsonController), see this class.
 *
 * @see BaseJsonController
 *
 * @package FWK\Controllers\Page\Internal
 */
class SponsorShipController extends BaseJsonController {

    private $productService = null;

    // TODO LCFWKPHP-710

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        //$this->productService = Loader::service(Services::PRODUCT);
        //$this->queryParameters = new QueryParametersGroup();
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
        return FormFactory::getSponsorShip()->getInputFilterParameters();
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
        //$this->getRequestParams(Parameters::NAME) = $this->getRequestParams(Parameters::FIRST_NAME) . ' ' . $this->getRequestParams(Parameters::LAST_NAME);
        //$this->appliedParameters = $this->productService->generateParametersGroupFromArray($this->queryParameters, $this->getRequestParams());

        // $paramsContactParameterGroup = new ContactParametersGroup(); 

        return Loader::service(Services::CONTACT)->send();
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
