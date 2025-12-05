<?php

namespace FWK\Controllers\Account\Internal;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\Form\FormFactory;
use FWK\Core\Resources\Loader;
use FWK\Enums\Parameters;
use FWK\Enums\Services;
use FWK\Services\AccountService;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Common\Route;
use SDK\Enums\AccountKey;

/**
 * This is the account registered user move controller.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @see BaseHtmlController
 *
 * @package FWK\Controllers\Account\Internal
 */
class AccountRegisteredUserMoveController extends BaseHtmlController {

    public const REGISTERED_USER_MOVE_FORM = 'registeredUserMoveForm';

    public const COMPANY_STRUCTURES = 'companyStructures';

    protected bool $loggedInRequired = true;

    protected ?AccountService $accountService = null;

    protected string $accountId = AccountKey::USED;

    protected int $registeredUserId = 0;

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FilterInputFactory::getRegisteredUserIdParameter() + FilterInputFactory::getAccountIdParameter();
    }

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->accountService = Loader::service(Services::ACCOUNT);
    }
    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
        $this->accountId = $this->getRequestParam(Parameters::ACCOUNT_ID, true);
        $this->registeredUserId = $this->getRequestParam(Parameters::REGISTERED_USER_ID, true);
        $this->accountService->addGetCompanyStructure($requests, self::COMPANY_STRUCTURES, $this->accountId);
    }
    /**
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     */
    protected function setControllerBaseData(): void {
        $companyStructures = $this->getControllerData(self::COMPANY_STRUCTURES);
        $this->setDataValue(self::REGISTERED_USER_MOVE_FORM, FormFactory::getAccountRegisteredUserMove($this->accountId, $this->registeredUserId, $companyStructures));
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
