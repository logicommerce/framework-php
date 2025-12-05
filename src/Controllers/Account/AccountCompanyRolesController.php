<?php

namespace FWK\Controllers\Account;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\Form\FormFactory;
use FWK\Core\Resources\Loader;
use FWK\Core\Resources\Utils;
use FWK\Core\Theme\Dtos\ItemList;
use FWK\Enums\Services;
use FWK\Services\AccountService;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Common\Route;
use SDK\Services\Parameters\Groups\Account\CompanyRolesParametersGroup;

/**
 * This is the account company structure controller.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @see BaseHtmlController
 *
 * @package FWK\Controllers\Account
 */
class AccountCompanyRolesController extends BaseHtmlController {

    public const COMPANY_ROLES = 'companyRoles';
    public const COMPANY_ROLES_FILTER_FORM = 'companyRolesFilterForm';
    public const COMPANY_ROLES_FILTER = 'companyRolesFilter';
    public const COMPANY_ROLES_ERROR = 'companyRolesError';
    public const ITEM_LIST_DATA = 'itemListData';

    protected AccountService $accountService;
    protected ?CompanyRolesParametersGroup $companyRolesParametersGroup = null;

    protected ?ItemList $itemListConfiguration = null;
    protected array $additionalRequestParameters = [];
    protected array $companyRolesFilter = [];

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        $this->itemListConfiguration = self::getTheme()->getConfiguration()->getAccount()->getCompanyRolesList();
        parent::__construct($route);
        $this->accountService = Loader::service(Services::ACCOUNT);
        $this->companyRolesParametersGroup = new CompanyRolesParametersGroup();
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FilterInputFactory::getPaginableItemsParameter()
            + FilterInputFactory::getSortableItemsParameters("CompanyRolesSort")
            + FormFactory::getCompanyRolesFilters()->getInputFilterParameters();
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
     */
    protected function initializeAppliedParameters(): void {
        $requestParams = array_filter($this->getRequestParams(), function ($value) {
            return !is_null($value) && $value !== '-';
        });

        $companyRolesRequest = array_merge(
            $this->itemListConfiguration->getDefaultParametersValues(),
            $requestParams,
            $this->additionalRequestParameters,
            $this->itemListConfiguration->getRequestParameters()
        );


        $this->companyRolesFilter = $this->accountService->generateParametersGroupFromArray(
            $this->companyRolesParametersGroup,
            $companyRolesRequest
        );
    }

    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
        $this->accountService->addGetCompanyRoles($requests, self::COMPANY_ROLES, $this->companyRolesParametersGroup);
    }

    /**
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     */
    protected function setControllerBaseData(): void {
        $items[self::COMPANY_ROLES] = $this->getControllerData(self::COMPANY_ROLES);
        $items[self::COMPANY_ROLES_FILTER_FORM] = FormFactory::getCompanyRolesFilters($this->companyRolesParametersGroup);
        $items[self::COMPANY_ROLES_ERROR] = "";
        if (!is_null($items[self::COMPANY_ROLES]->getError())) {
            $items[self::COMPANY_ROLES_ERROR] = Utils::getErrorLabelValue($items[self::COMPANY_ROLES]);
        }
        $this->setDataValue(self::CONTROLLER_ITEM, $items);
        $this->setDataValue(self::ITEM_LIST_DATA, $this->itemListConfiguration);
        $this->setDataValue(self::COMPANY_ROLES_FILTER, $this->companyRolesFilter);
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
        // Additional batch requests can be added here if needed
    }

    protected function setData(array $additionalData = []): void {
    }
}
