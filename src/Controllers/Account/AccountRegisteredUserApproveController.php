<?php

namespace FWK\Controllers\Account;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\Controllers\ControllersFactory;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\Form\FormFactory;
use FWK\Core\Resources\Loader;
use FWK\Enums\Parameters;
use FWK\Enums\Services;
use FWK\Services\AccountService;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Common\Route;
use SDK\Services\Parameters\Groups\Account\AccountRegisteredUsersPendingApprovalParametersGroup;

/**
 * This is the account registered user approve controller.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @see BaseHtmlController
 *
 * @package FWK\Controllers\Account
 */
class AccountRegisteredUserApproveController extends BaseHtmlController {

    public const REGISTERED_USER_APPROVE_FORM = "registeredUserApproveForm";

    public const REGISTERED_USER = "registeredUser";

    public const NOT_PENDING_APPROVAL_MESSAGE = "notPendingApprovalMessage";

    protected ?AccountService $accountService = null;

    protected ?AccountRegisteredUsersPendingApprovalParametersGroup $accountRegisteredUsersPendingApprovalParametersGroup = null;

    protected string $hash = "";

    protected array $ids = [];

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->ids = ControllersFactory::extractIdsFromUrl($route);
        $this->accountService = Loader::service(Services::ACCOUNT);
        $this->accountRegisteredUsersPendingApprovalParametersGroup = new AccountRegisteredUsersPendingApprovalParametersGroup();
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FilterInputFactory::getHashParameter();
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
        return FilterInputHandler::PARAMS_FROM_GET;
    }

    /**
     * This method initialize applied parameters, runs previously to run preSendControllerBaseBatchData
     *
     */
    protected function initializeAppliedParameters(): void {
    }

    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
        $this->hash = $this->getRequestParam(Parameters::HASH, true);
        $this->accountRegisteredUsersPendingApprovalParametersGroup->setHash($this->hash);
        Loader::service(Services::ACCOUNT)->addGetRegisteredUsersPendingApproval($requests, self::REGISTERED_USER, $this->ids[Parameters::ACCOUNT_ID] ?? 0, $this->ids[Parameters::REGISTERED_USER_ID] ?? 0, $this->accountRegisteredUsersPendingApprovalParametersGroup);
    }

    /**
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     */
    protected function setControllerBaseData(): void {
        $registeredUser = $this->getControllerData(self::REGISTERED_USER);
        if (!is_null($registeredUser->getError())) {
            if ($registeredUser->getError()->getCode() == 'A01000-NOT_PENDING_APPROVAL') {
                $this->setDataValue(self::NOT_PENDING_APPROVAL_MESSAGE, true);
            }
        }
        $this->setDataValue(self::REGISTERED_USER_APPROVE_FORM, FormFactory::getAccountRegisteredUsersApprove($registeredUser, $this->hash));
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
     * This method runs after the batch requests (defined in the setBatchData methods) are resolved,
     * so here you can work with the response of the batch requests and calculate and set more needed data.
     *
     * @param array $additionalData
     *              Set additiona data to the controller data
     * 
     * @return void
     */
    protected function setData(array $additionalData = []): void {
    }
}
