<?php

namespace FWK\Controllers\Blog\Internal;

use SDK\Core\Dtos\Element;
use SDK\Core\Resources\BatchRequests;
use SDK\Enums\BlogPostCommentMode;
use SDK\Services\Parameters\Groups\Blog\AddBlogPostCommentParametersGroup;
use FWK\Core\Controllers\BaseJsonController;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\Form\FormFactory;
use FWK\Core\Resources\Loader;
use SDK\Dtos\Common\Route;
use FWK\Core\Resources\Utils;
use FWK\Enums\LanguageLabels;
use FWK\Enums\Parameters;
use FWK\Enums\Services;
use FWK\Services\BlogService;
use FWK\Core\Controllers\Traits\CheckCaptcha;

/**
 * This is the AddCommentController class.
 * This class extends BaseJsonController (FWK\Core\Controllers\BaseJsonController), see this class.
 *
 * @see BaseJsonController
 * 
 * @uses CheckCaptcha
 *
 * @package FWK\Controllers\Blog\Internal
 */
class AddCommentController extends BaseJsonController {
    use CheckCaptcha;

    private ?BlogService $blogService = null;

    /**
     * This attribute is an AddBlogPostCommentParametersGroup instance needed to communicate with the SDK.
     */
    private ?AddBlogPostCommentParametersGroup $addCommentParameters = null;

    /**
     * Constructor method.
     * 
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $blogSettings = Loader::service(Services::SETTINGS)->getBlogSettings();
        $canComment = false;
        if ($blogSettings->getCommentsMode() === BlogPostCommentMode::ANONYMOUS_AND_REGISTERED_USERS) {
            $canComment = true;
        } elseif ($blogSettings->getCommentsMode() === BlogPostCommentMode::ONLY_REGISTERED_USERS && $this->getSession() !== null && $this->getSession()->getUser()->getId() !== 0) {
            $canComment = true;
        }
        if (!$canComment) {
            throw new CommerceException("Login is required", CommerceException::CONTROLLER_LOGIN_REQUIRED);
        }
        $this->blogService = Loader::service(Services::BLOG);
        $this->addCommentParameters = new AddBlogPostCommentParametersGroup();
        $this->responseMessage = $this->language->getLabelValue(LanguageLabels::BLOG_ADD_COMMENT_RESPONSE_OK, $this->responseMessage);
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
        $this->checkCaptcha();
        $response = null;
        $this->appliedParameters += [
            $this->getRequestParam(Parameters::ID),
            $this->blogService->generateParametersGroupFromArray($this->addCommentParameters, $this->getRequestParams())
        ];
        $response = $this->blogService->postAddComment($this->getRequestParam(Parameters::ID), $this->addCommentParameters);
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
