<?php

namespace FWK\Controllers\Account;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\Controllers\ControllersFactory;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\Form\FormFactory;
use FWK\Core\Resources\Loader;
use FWK\Core\Resources\Session;
use FWK\Enums\Parameters;
use FWK\Enums\Services;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Accounts\MasterVal;
use SDK\Dtos\Common\Route;
use SDK\Enums\AccountKey;
use SDK\Services\AccountService;

/**
 * This is the registered user update controller.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @see BaseHtmlController
 *
 * @package FWK\Controllers\Account
 */
class AccountRegisteredUserController extends BaseHtmlController {
    public const REGISTERED_USER_UPDATE_FORM = 'registeredUserUpdateForm';

    public const COMPANY_ROLES = 'companyRoles';

    protected bool $loggedInRequired = true;

    protected string $accountId = '';

    protected int $registeredUserId = 0;

    protected ?MasterVal $registeredUser = null;

    protected ?AccountService $accountService = null;

    protected array $ids = [];
    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->accountService = Loader::service(Services::ACCOUNT);
        $this->ids = ControllersFactory::extractIdsFromUrl($route);
    }
    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return  FilterInputFactory::getRegisteredUserIdParameter() + FilterInputFactory::getAccountIdParameter();
    }
    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
        $this->accountId = !empty($this->ids[Parameters::ACCOUNT_ID]) ? $this->ids[Parameters::ACCOUNT_ID] : Session::getInstance()->getBasket()->getAccount()->getId();
        if (empty($this->ids[Parameters::REGISTERED_USER_ID]) || $this->ids[Parameters::REGISTERED_USER_ID] == AccountKey::ME) {
            $this->registeredUserId = Session::getInstance()->getBasket()->getRegisteredUser()->getId();
        } else {
            $this->registeredUserId = $this->ids[Parameters::REGISTERED_USER_ID];
        }
        $this->accountService->addGetRegisteredUsersWithRegisteredId($requests, self::CONTROLLER_ITEM, $this->accountId, $this->registeredUserId);
        $this->accountService->addGetCompanyRoles($requests, self::COMPANY_ROLES);
    }
    /**
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     */
    protected function setControllerBaseData(): void {
        $this->registeredUser = $this->getControllerData(self::CONTROLLER_ITEM);
        $companyRoles = $this->getControllerData(self::COMPANY_ROLES);
        $this->setDataValue(self::REGISTERED_USER_UPDATE_FORM, FormFactory::getAccountRegisteredUserUpdate($this->registeredUser, $companyRoles?->getItems()));
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
