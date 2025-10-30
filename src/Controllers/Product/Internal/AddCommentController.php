<?php

namespace FWK\Controllers\Product\Internal;

use FWK\Core\Controllers\BaseJsonController;
use FWK\Enums\Services;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\Resources\Loader;
use SDK\Dtos\Common\Route;
use SDK\Services\Parameters\Groups\Product\AddCommentParametersGroup;
use SDK\Core\Dtos\Element;
use SDK\Core\Resources\BatchRequests;
use FWK\Core\Form\FormFactory;
use FWK\Enums\Parameters;
use FWK\Enums\LanguageLabels;
use FWK\Core\Resources\Utils;
use FWK\Core\Exceptions\CommerceException;
use FWK\Services\ProductService;
use FWK\Core\Controllers\Traits\CheckCaptcha;

/**
 * This is the AddCommentController class.
 * This class extends BaseJsonController (FWK\Core\Controllers\BaseJsonController), see this class.
 *
 * @see BaseJsonController
 * 
 * @uses CheckCaptcha
 *
 * @package FWK\Controllers\Product\Internal
 */
class AddCommentController extends BaseJsonController {
    use CheckCaptcha;

    private ?ProductService $productService = null;

    /**
     * This attribute is an AddCommentParametersGroup instance needed to communicate with the SDK.
     */
    private ?AddCommentParametersGroup $addCommentParameters = null;

    /**
     * Constructor method.
     * 
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);

        $canComment = false;
        if (self::getTheme()->getConfiguration()->getForms()->getComments()->getAnonymousRatingEnabled()) {
            $canComment = true;
        } elseif ($this->getSession() !== null && $this->getSession()->getUser()->getId() !== 0) {
            $canComment = true;
        }
        if (!$canComment) {
            throw new CommerceException("Login is required", CommerceException::CONTROLLER_LOGIN_REQUIRED);
        }
        $this->productService = Loader::service(Services::PRODUCT);
        $this->addCommentParameters = new AddCommentParametersGroup();
        $this->responseMessage = $this->language->getLabelValue(LanguageLabels::PRODUCT_ADD_COMMENT_RESPONSE_OK, $this->responseMessage);
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FormFactory::getComment()->getInputFilterParameters();
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
        $response = null;
        $this->checkCaptcha();
        $this->appliedParameters += [
            $this->getRequestParam(Parameters::ID),
            $this->productService->generateParametersGroupFromArray($this->addCommentParameters, $this->getRequestParams())
        ];
        $response = $this->productService->addComment($this->getRequestParam(Parameters::ID), $this->addCommentParameters);
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
