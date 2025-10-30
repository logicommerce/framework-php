<?php

namespace FWK\Controllers\User\Internal;

use FWK\Enums\LanguageLabels;
use FWK\Enums\Services;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\Resources\Loader;
use FWK\Core\Form\FormFactory;
use SDK\Core\Resources\BatchRequests;
use SDK\Core\Dtos\Element;
use SDK\Dtos\Common\Route;
use FWK\Enums\Parameters;
use FWK\Core\Controllers\BaseJsonController;
use FWK\Core\Resources\Utils;
use FWK\Enums\NewsletterSubscriptionActions;
use FWK\Services\UserService;
use SDK\Core\Services\Parameters\Groups\NewsletterSubscriptionParametersGroup;
use FWK\Core\Controllers\Traits\CheckCaptcha;

/**
 * This is the NewsletterController controller class.
 * This class extends FWK\Core\Controllers\BaseJsonController, see this class.
 *
 * @see BaseJsonController
 * 
 * @uses CheckCaptcha
 *
 * @package FWK\Controllers\User\Internal
 */
class NewsletterController extends BaseJsonController {
    use CheckCaptcha;

    protected ?NewsletterSubscriptionParametersGroup $newsletterSubscriptionParametersGroup = null;

    private ?UserService $userService = null;

    /**
     * Constructor method.
     * 
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->userService = Loader::service(Services::USER);
        $this->newsletterSubscriptionParametersGroup = new NewsletterSubscriptionParametersGroup();
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        if (!is_null(FormFactory::getNewsletter())) {
            return FormFactory::getNewsletter()->getInputFilterParameters();
        }
        return [];
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
        $this->appliedParameters = [
            Parameters::EMAIL => $this->getRequestParam(Parameters::EMAIL, true),
            Parameters::TYPE => $this->getRequestParam(Parameters::TYPE, true)
        ];
        $this->newsletterSubscriptionParametersGroup->setEmail($this->appliedParameters[Parameters::EMAIL]);
    }

    /**
     * This method launches the adequate actions against the SDK (through the FWK services) and returns the response data. 
     *
     * @return Element
     */
    protected function getResponseData(): ?Element {
        $this->checkCaptcha();
        $response = null;
        switch ($this->appliedParameters[Parameters::TYPE]) {
            case NewsletterSubscriptionActions::CHECK_STATUS:
                $response = $this->userService->newsletterCheckStatus($this->newsletterSubscriptionParametersGroup);
                break;
            case NewsletterSubscriptionActions::SUBSCRIBE:
                $response = $this->userService->newsletterSubscribe($this->newsletterSubscriptionParametersGroup);
                $this->responseMessage = $this->language->getLabelValue(LanguageLabels::NEWSLETTER_REGISTERED, $this->responseMessage);
                break;
            case NewsletterSubscriptionActions::UNSUBSCRIBE:
                $response = $this->userService->newsletterUnsubscribe($this->newsletterSubscriptionParametersGroup);
                $this->responseMessage = $this->language->getLabelValue(LanguageLabels::NEWSLETTER_UNREGISTERED, $this->responseMessage);
                break;
        }
        if (strlen(trim($response->getMessage())) > 0) {
            $this->responseMessage = $response->getMessage();
        }
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
