<?php

namespace FWK\Controllers\Account;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\Resources\Loader;
use FWK\Enums\Parameters;
use FWK\Enums\Services;
use FWK\Services\AccountService;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Common\Route;
use SDK\Enums\AccountKey;
use SDK\Services\Parameters\Groups\Account\CompanyStructureParametersGroup;

/**
 * This is the account company structure controller.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @see BaseHtmlController
 *
 * @package FWK\Controllers\Account
 */
class CompanyStructureController extends BaseHtmlController {

    public const COMPANY_STRUCTURE = "companyStructure";
    public const COMPANY_STRUCTURE_FORM = "companyStructureForm";

    protected string $q = '';
    protected AccountService $accountService;
    protected bool $loggedInRequired = true;
    protected bool $companyAccountsRequired = true;
    protected ?CompanyStructureParametersGroup $companyStructureParametersGroup = null;

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->accountService = Loader::service(Services::ACCOUNT);
        $this->companyStructureParametersGroup = new CompanyStructureParametersGroup();
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
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
        return FilterInputHandler::PARAMS_FROM_GET;
    }

    /**
     * This method initialize applied parameters, runs previously to run preSendControllerBaseBatchData
     */
    protected function initializeAppliedParameters(): void {
        $this->q = $this->getRequestParam(Parameters::Q, false, '');
    }

    /**
     * This method validate context, runs previously to run preSendControllerBaseBatchData
     */
    protected function validateRequestContext(): bool {
        return true;
    }

    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
        // Add batch request for company structure data using the correct endpoint
        $this->accountService->addGetCompanyStructure($requests, self::COMPANY_STRUCTURE, AccountKey::USED, $this->companyStructureParametersGroup);
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

    /**
     * This method runs after the batch requests (defined in the setBatchData methods) are resolved,
     * so here you can work with the response of the batch requests and calculate and set more needed data.
     *
     * @param array $additionalData
     *              Set additional data to the controller data
     * 
     * @return void
     */
    protected function setData(array $additionalData = []): void {
        // The company structure data is automatically available from the batch request
        // Additional processing can be done here if needed

        // TODO: Add permissions logic or other data processing if required
    }
}
