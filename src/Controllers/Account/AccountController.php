<?php

namespace FWK\Controllers\Account;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\Controllers\ControllersFactory;
use FWK\Core\Controllers\Traits\AddDefaultCountryAndLocationsTrait;
use FWK\Core\Form\FormFactory;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Common\Route;
use FWK\Core\Resources\Loader;
use FWK\Core\Resources\Session;
use FWK\Core\Resources\Utils;
use FWK\Enums\Services;
use FWK\Services\AccountService;
use FWK\Services\UserService;
use SDK\Enums\AccountKey;
use SDK\Enums\CustomCompanyRoleTarget;
use SDK\Enums\SessionType;
use SDK\Services\Parameters\Groups\Account\CompanyRolesParametersGroup;

/**
 * This is the account company structure controller.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @see BaseHtmlController
 *
 * @package FWK\Controllers\Account
 */
class AccountController extends BaseHtmlController {
    use AddDefaultCountryAndLocationsTrait;

    public const COMPANY_ROLES = 'companyRoles';

    public const CUSTOM_TAGS = 'customTags';

    public const DEFAULT_SELECTED_COUNTRY = 'defaultSelectedCountry';

    public const DEFAULT_SELECTED_COUNTRY_LOCATIONS = 'defaultSelectedCountryLocations';

    public const FORM = 'form';

    public const ACCOUNT_NAME = 'accountName';

    public const USER_WARNINGS = 'userWarnings';

    protected const ACCOUNT = 'account';

    protected ?AccountService $accountService = null;

    protected ?UserService $userService = null;

    protected int $id = 0;

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->id = $route->getId() ?? 0;
        $this->accountService = Loader::service(Services::ACCOUNT);
        $this->userService = Loader::service(Services::USER);
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
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
        $companyRolesParametersGroup = new CompanyRolesParametersGroup();
        $companyRolesParametersGroup->setTarget(CustomCompanyRoleTarget::COMPANY_DIVISION_MASTER);
        $this->accountService->addGetAccounts($requests, self::ACCOUNT, $this->id > 0 ? $this->id : AccountKey::USED);
        $this->accountService->addGetCompanyRoles($requests, self::COMPANY_ROLES, AccountKey::USED, $companyRolesParametersGroup);
        $this->userService->addGetCustomTags($requests, self::CUSTOM_TAGS, self::getTheme()->getConfiguration()->getUser()->getUserCustomTagsParametersGroup());
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
     *              Set additional data to the controller data
     * 
     * @return void
     */
    protected function setData(array $additionalData = []): void {
        $account = $this->getControllerData(self::ACCOUNT);
        if (Session::getInstance()->getBasket()->getType() == SessionType::ANONYMOUS) {
            $aux[self::FORM] = FormFactory::setUser(FormFactory::SET_USER_TYPE_ADD_USER, $account?->getMaster()?->getRegisteredUser(), $this->getControllerData(self::CUSTOM_TAGS));
            $aux[self::DEFAULT_SELECTED_COUNTRY] = $this->getDefaultCountry();
            $aux[self::DEFAULT_SELECTED_COUNTRY_LOCATIONS] = $this->getDefaultCountryLocations();
        } else {
            $aux[self::ACCOUNT_NAME] = $account->getName();
            $aux[self::FORM] = FormFactory::getAccountEditForm($account, $this->getControllerData(self::COMPANY_ROLES)->getItems() ?? [], $this->getControllerData(self::CUSTOM_TAGS), );
        }
        $aux[self::USER_WARNINGS] = Utils::getUserWarnings();
        $this->setDataValue(self::CONTROLLER_ITEM, $aux);
    }
}
