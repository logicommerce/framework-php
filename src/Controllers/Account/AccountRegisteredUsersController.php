<?php

namespace FWK\Controllers\Account;

use DateTime;
use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\Form\FormFactory;
use FWK\Core\Resources\Loader;
use FWK\Core\Resources\Utils;
use FWK\Core\Theme\Dtos\ItemList;
use FWK\Enums\Parameters;
use FWK\Enums\Services;
use FWK\Services\AccountService;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Common\Route;
use SDK\Core\Application;
use SDK\Enums\AccountKey;
use SDK\Enums\UserKeyCriteria;
use SDK\Services\Parameters\Groups\Account\AccountRegisteredUsersParametersGroup;

/**
 * This is the registered users controller.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @see BaseHtmlController
 *
 * @package FWK\Controllers\Account
 */
class AccountRegisteredUsersController extends BaseHtmlController {

    public const ACCOUNT = 'account';
    public const ACCOUNT_ID = 'accountId';
    public const COMPANY_ROLES = 'companyRoles';
    public const REGISTERED_USERS = "registeredUsers";
    public const REGISTERED_USERS_FORM = "registeredUsersForm";
    public const REGISTERED_USERS_FILTER = 'registeredUsersFilter';
    public const REGISTERED_USERS_ERROR = 'registeredUsersError';
    public const ITEM_LIST_DATA = 'itemListData';

    protected bool $loggedInRequired = true;

    protected string $userKeyCriteria = '';

    protected ?ItemList $itemListConfiguration = null;
    protected array $additionalRequestParameters = [];
    protected array $registeredUsersFilter = [];

    protected ?AccountService $accountService = null;
    protected ?AccountRegisteredUsersParametersGroup $accountRegisteredUsersParametersGroup = null;

    protected string $accountId = AccountKey::USED;

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        $this->itemListConfiguration = self::getTheme()->getConfiguration()->getAccount()->getRegisteredUsersList();
        parent::__construct($route);
        $this->accountService = Loader::service(Services::ACCOUNT);
        $this->accountRegisteredUsersParametersGroup = new AccountRegisteredUsersParametersGroup();
        $this->userKeyCriteria = Application::getInstance()->getEcommerceSettings()->getUserAccountsSettings()->getUserKeyCriteria();
        $this->accountId = empty($route->getId()) ?  AccountKey::USED : $route->getId();
    }
    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return
            FilterInputFactory::getRoleIdParameter() +
            FilterInputFactory::getPaginableItemsParameter() +
            FilterInputFactory::getSortableItemsParameters("RegisteredUserSort") +
            FormFactory::getAccountRegisteredUsers()->getInputFilterParameters();
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
        $requestParams = array_filter($this->getRequestParams(), function ($value) {
            return !is_null($value) && $value !== '-';
        });
        $registeredUsersRequest = array_merge(
            $this->itemListConfiguration->getDefaultParametersValues(),
            $requestParams,
            $this->additionalRequestParameters,
            $this->itemListConfiguration->getRequestParameters()
        );

        if (isset($registeredUsersRequest[Parameters::ADDED_FROM]) && !empty($registeredUsersRequest[Parameters::ADDED_FROM])) {
            $registeredUsersRequest[Parameters::ADDED_FROM] = new DateTime($registeredUsersRequest[Parameters::ADDED_FROM]);
        }
        if (isset($registeredUsersRequest[Parameters::ADDED_TO]) && !empty($registeredUsersRequest[Parameters::ADDED_TO])) {
            $registeredUsersRequest[Parameters::ADDED_TO] = new DateTime($registeredUsersRequest[Parameters::ADDED_TO]);
        }

        if (isset($registeredUsersRequest[Parameters::Q]) && !empty($registeredUsersRequest[Parameters::Q])) {
            $q = $registeredUsersRequest[Parameters::Q];
            switch ($this->userKeyCriteria) {
                case UserKeyCriteria::PID:
                    $registeredUsersRequest[Parameters::P_ID] = $q;
                    break;
                case UserKeyCriteria::EMAIL:
                    $registeredUsersRequest[Parameters::EMAIL] = $q;
                    break;
                case UserKeyCriteria::USERNAME:
                    $registeredUsersRequest[Parameters::USERNAME] = $q;
                    break;
            }
            unset($registeredUsersRequest[Parameters::Q]);
        }
        $this->registeredUsersFilter = $this->accountService->generateParametersGroupFromArray($this->accountRegisteredUsersParametersGroup, $registeredUsersRequest);
    }
    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
        $this->accountService->addGetRegisteredUsers($requests, self::REGISTERED_USERS, $this->accountId, $this->accountRegisteredUsersParametersGroup);
        $this->accountService->addGetCompanyRoles($requests, self::COMPANY_ROLES, $this->accountId);
        $this->accountService->addGetAccounts($requests, self::ACCOUNT, $this->accountId);
    }
    /**
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     */
    protected function setControllerBaseData(): void {
        $aux = [];
        $aux[self::ACCOUNT] = $this->getControllerData(self::ACCOUNT);
        $aux[self::REGISTERED_USERS] = $this->getControllerData(self::REGISTERED_USERS);
        $aux[self::REGISTERED_USERS_ERROR] = "";
        if (!is_null($aux[self::REGISTERED_USERS]->getError())) {
            $aux[self::REGISTERED_USERS_ERROR] = Utils::getErrorLabelValue($aux[self::REGISTERED_USERS]);
        }

        $companyRoles = $this->getControllerData(self::COMPANY_ROLES);
        $aux[self::REGISTERED_USERS_FORM] = FormFactory::getAccountRegisteredUsers(
            $this->accountRegisteredUsersParametersGroup,
            $companyRoles?->getItems()
        );
        $aux[self::ACCOUNT_ID] = $this->accountId;
        $this->setDataValue(self::CONTROLLER_ITEM, $aux);
        $this->setDataValue(self::REGISTERED_USERS_FILTER, $this->registeredUsersFilter);
    }
    /**
     * This method is the one in charge of defining all the data batch requests that
     * are needed for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method w2025-06-11T23:59:59+02:00ill add the batch requests.
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
        $this->setDataValue(self::ITEM_LIST_DATA, $this->itemListConfiguration);
    }
}
