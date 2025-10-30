<?php

namespace FWK\Controllers\Blog\Internal;

use SDK\Core\Resources\BatchRequests;
use FWK\Core\Resources\Loader;
use FWK\Enums\Parameters;
use FWK\Core\Controllers\BaseJsonController;
use FWK\Core\FilterInput\FilterInputHandler;
use SDK\Core\Dtos\Element;
use FWK\Enums\Services;
use FWK\Core\Form\FormFactory;
use FWK\Core\Resources\Utils;
use SDK\Dtos\Common\Route;
use FWK\Enums\LanguageLabels;

/**
 * This is the Blog Category Subscribe controller.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @see BaseHtmlController
 *
 * @package FWK\Controllers\Blog
 */
class CategorySubscribeController extends BaseJsonController {

    /**
     * Constructor method.
     * 
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->responseMessage = $this->language->getLabelValue(LanguageLabels::BLOG_CATEGORY_SUBSCRIPTION_FORM_OK, $this->responseMessage);
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        $subscribeForm = FormFactory::getBlogSubscribe(FormFactory::BLOG_CATEGORY_SUBSCRIBE);
        if (!is_null($subscribeForm)) {
            return $subscribeForm->getInputFilterParameters();
        } else {
            return [];
        }
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
        $this->appliedParameters[Parameters::EMAIL] = $this->getRequestParam(Parameters::EMAIL, true);
        $this->appliedParameters[Parameters::ID] = $this->getRequestParam(Parameters::ID, true);
        $response = Loader::service(Services::BLOG)->categorySubscribe($this->appliedParameters[Parameters::ID], $this->appliedParameters[Parameters::EMAIL]);
        $this->responseMessageError = Utils::getErrorLabelValue($response);
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
