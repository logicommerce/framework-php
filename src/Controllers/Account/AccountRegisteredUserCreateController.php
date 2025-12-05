<?php

namespace FWK\Controllers\Account;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\Form\FormFactory;
use FWK\Core\Resources\Loader;
use FWK\Enums\Services;
use FWK\Services\AccountService;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Common\Route;
use SDK\Enums\AccountKey;

/**
 * This is the account registered user create controller.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @see BaseHtmlController
 *
 * @package FWK\Controllers\Account
 */
class AccountRegisteredUserCreateController extends BaseHtmlController {

    public const ACCOUNT_ID = 'accountId';

    public const COMPANY_ROLES = 'companyRoles';

    public const REGISTERED_USER_NEW_CREATE_FORM = "registeredUserNewCreateForm";

    protected bool $loggedInRequired = true;

    protected ?AccountService $accountService = null;

    protected string $id_account = AccountKey::USED;
    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->accountService = Loader::service(Services::ACCOUNT);
        $this->id_account = empty($route->getId()) ?  AccountKey::USED : $route->getId();
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
        $this->accountService->addGetCompanyRoles($requests, self::COMPANY_ROLES);
    }

    /**
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     */
    protected function setControllerBaseData(): void {
        $items = [];
        $companyRoles = $this->getControllerData(self::COMPANY_ROLES);
        $items[self::ACCOUNT_ID] = $this->id_account;
        $items[self::REGISTERED_USER_NEW_CREATE_FORM] = FormFactory::getAccountRegisteredUserCreate($this->id_account, $companyRoles->getItems() ?? []);

        $this->setDataValue(self::CONTROLLER_ITEM, $items);
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
