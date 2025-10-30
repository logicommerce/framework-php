<?php

namespace FWK\Controllers\Account\Internal;

use FWK\Core\Controllers\BaseJsonController;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\Resources\Loader;
use FWK\Core\Resources\RoutePaths;
use FWK\Core\Resources\Utils;
use FWK\Enums\LanguageLabels;
use FWK\Enums\Parameters;
use FWK\Enums\RouteType;
use FWK\Enums\Services;
use FWK\Services\AccountService;
use SDK\Core\Dtos\Element;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Common\Route;
use SDK\Enums\AccountKey;
use SDK\Enums\AccountOrderApprovalDecision;
use SDK\Services\Parameters\Groups\Account\AccountOrdersApprovalDecisionParametersGroup;

/**
 * This is the orders approval decision controller.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @see BaseHtmlController
 *
 * @package FWK\Controllers\Account\Internal
 */
class OrdersApprovalDecisionController extends BaseJsonController {

    protected ?AccountService $accountService = null;

    protected ?AccountOrdersApprovalDecisionParametersGroup $accountOrdersApprovalDecisionParametersGroup = null;

    protected string $orderId = "";

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->accountService = Loader::service(Services::ACCOUNT);
        $this->accountOrdersApprovalDecisionParametersGroup = new AccountOrdersApprovalDecisionParametersGroup();
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FilterInputFactory::getOrdersApprovalDecisionParameter();
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
        $data = $this->getRequestParams();
        $this->orderId = $data[Parameters::ORDER_ID] ?? '';
        $this->accountOrdersApprovalDecisionParametersGroup->setDecision($data[Parameters::DECISION] ?? '');
    }

    /**
     * This method launches the adequate actions against the SDK (through the FWK services) and returns the response data. 
     *
     * @return Element
     */
    protected function getResponseData(): ?Element {
        $response = $this->accountService->ordersApprovalDecision($this->orderId, $this->accountOrdersApprovalDecisionParametersGroup);
        if (!is_null($response->getError())) {
            $this->responseMessageError = Utils::getErrorLabelValue($response);
        }

        $decision = $this->getRequestParam(Parameters::DECISION, false, '');

        if ($decision == AccountOrderApprovalDecision::APPROVE) {
            $this->responseMessage = $this->language->getLabelValue(
                LanguageLabels::ACCOUNT_REGISTERED_USER_APPROVE_MISSAGE,
                $this->responseMessage
            );
        } else if ($decision == AccountOrderApprovalDecision::REJECT) {
            $this->responseMessage = $this->language->getLabelValue(
                LanguageLabels::ACCOUNT_REGISTERED_USER_REJECT_MISSAGE,
                $this->responseMessage
            );
        }
        return $response;
    }

    /**
     * This method parses the given Element and returns it.
     * 
     * @param Element $response
     * 
     * @return \SDK\Core\Dtos\Element
     */
    protected function parseResponseData(Element $registeredUser) {
        $data =  [
            Parameters::REDIRECT => str_replace("{" . Parameters::ID_USED . "}", AccountKey::USED, RoutePaths::getPath(RouteType::ACCOUNT_ORDERS)),
        ];

        return $data;
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
