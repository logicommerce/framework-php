<?php

namespace FWK\Controllers\User\Internal;

use FWK\Enums\Services;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\Resources\Loader;
use SDK\Core\Dtos\Element;
use SDK\Core\Resources\BatchRequests;
use FWK\Enums\LanguageLabels;
use FWK\Core\Controllers\BaseJsonController;
use FWK\Services\UserService;
use FWK\Enums\Parameters;
use FWK\Core\Resources\Utils;
use SDK\Dtos\Common\Route;
use FWK\Core\Controllers\Traits\CheckCaptcha;
use FWK\Core\FilterInput\FilterInput;
use SDK\Services\Parameters\Groups\User\UnsubscribeSubscriptionsParametersGroup;

/**
 * This is the UnsubscribeSubscriptionController controller class.
 * This class extends FWK\Core\Controllers\BaseJsonController, see this class.
 *
 * @see BaseJsonController
 *
 * @uses CheckCaptcha
 * 
 * @package FWK\Controllers\User\Internal
 */
class UnsubscribeSubscriptionController extends BaseJsonController {
    use CheckCaptcha;

    protected bool $loggedInRequired = true;

    private ?UserService $userService = null;

    private ?UnsubscribeSubscriptionsParametersGroup $unsubscribeSubscriptionsParametersGroup = null;

    /**
     * Constructor method.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->userService = Loader::service(Services::USER);
        $this->responseMessage = $this->language->getLabelValue(LanguageLabels::SUBSCRIPTION_UNSUBSCRIBED, $this->responseMessage);
        $this->unsubscribeSubscriptionsParametersGroup = new UnsubscribeSubscriptionsParametersGroup();
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return [
            Parameters::SUBSCRIPTION_TYPE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_TO_UPPER,
                FilterInput::CONFIGURATION_FILTER_KEY_FUNCTION_VALIDATOR => 'SDK\Enums\SubscriptionType::isValid'
            ])
        ];
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
        $this->appliedParameters = $this->userService->generateParametersGroupFromArray($this->unsubscribeSubscriptionsParametersGroup, $this->getRequestParams());
    }

    /**
     * This method launches the adequate actions against the SDK (through the FWK services) and returns the response data. 
     *
     * @return Element
     */
    protected function getResponseData(): ?Element {
        $response = $this->userService->unsubscribeSubscriptions($this->unsubscribeSubscriptionsParametersGroup);
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
