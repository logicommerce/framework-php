<?php

namespace FWK\Controllers\Account\Internal;

use FWK\Enums\Services;
use FWK\Core\Resources\Loader;
use SDK\Core\Dtos\Element;
use SDK\Core\Resources\BatchRequests;
use FWK\Core\Controllers\BaseJsonController;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\Form\FormFactory;
use FWK\Core\Resources\Route;
use FWK\Core\Resources\RoutePaths;
use FWK\Core\Resources\Utils;
use FWK\Enums\LanguageLabels;
use FWK\Enums\Parameters;
use FWK\Enums\RouteType;
use FWK\Services\AccountService;
use SDK\Enums\AccountKey;
use SDK\Services\Parameters\Groups\Account\AddCompanyRoleParametersGroup;
use SDK\Services\Parameters\Groups\Account\CompanyRolePermissionsValuesParametersGroup;
use SDK\Services\Parameters\Groups\Account\UpdateCompanyRoleParametersGroup;

/**
 * This is the save company role controller.
 * This class extends FWK\Core\Controllers\BaseJsonController, see this class.
 *
 * @see BaseJsonController
 *
 * @package FWK\Controllers\Account\Internal
 */
class SaveCompanyRoleController extends BaseJsonController {

    protected bool $loggedInRequired = true;

    protected bool $companyAccountsRequired = true;

    protected ?AccountService $accountService = null;

    protected UpdateCompanyRoleParametersGroup $updateCompanyRoleParametersGroup;

    protected AddCompanyRoleParametersGroup $addCompanyRoleParametersGroup;

    protected int $roleId = 0;

    public function __construct(Route $route) {
        parent::__construct($route);
        $this->accountService = Loader::service(Services::ACCOUNT);
        $this->updateCompanyRoleParametersGroup = new UpdateCompanyRoleParametersGroup();
        $this->addCompanyRoleParametersGroup = new AddCompanyRoleParametersGroup();
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
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FormFactory::getSaveCompanyRoleForm()->getInputFilterParameters() +
            FilterInputFactory::getRolesPermissionParameters() +
            FilterInputFactory::getIdParameter();
    }

    /**
     * This method launches the adequate actions against the SDK (through the FWK services) and returns the response data. 
     *
     * @return Element
     */
    protected function getResponseData(): ?Element {
        $requestParams = $this->getRequestParams();
        if (isset($requestParams[Parameters::ID])) {
            $this->roleId = $requestParams[Parameters::ID];
            unset($requestParams[Parameters::ID]);
        }

        $permissions = $requestParams[Parameters::ROLE_PERMISSIONS];
        unset($requestParams[Parameters::ROLE_PERMISSIONS]);
        $rolesPermissions = new CompanyRolePermissionsValuesParametersGroup();
        $this->accountService->generateParametersGroupFromArray($rolesPermissions, $permissions);

        if ($this->roleId > 0) {
            $this->accountService->generateParametersGroupFromArray($this->updateCompanyRoleParametersGroup, $requestParams);
            $this->updateCompanyRoleParametersGroup->setPermissions($rolesPermissions);
            $response =  $this->accountService->updateCompanyRole($this->roleId, $this->updateCompanyRoleParametersGroup);

            if (!is_null($response->getError())) {
                $this->responseMessageError = Utils::getErrorLabelValue($response);
            }

            $this->responseMessage = $this->language->getLabelValue(
                LanguageLabels::SAVED,
                $this->responseMessage
            );

            return $response;
        }

        $this->accountService->generateParametersGroupFromArray($this->addCompanyRoleParametersGroup, $requestParams);
        $this->addCompanyRoleParametersGroup->setPermissions($rolesPermissions);
        $response =  $this->accountService->createCompanyRole(AccountKey::USED, $this->addCompanyRoleParametersGroup);

        if (!is_null($response->getError())) {
            $this->responseMessageError = Utils::getErrorLabelValue($response);
        }

        $this->responseMessage = $this->language->getLabelValue(
            LanguageLabels::SAVED,
            $this->responseMessage
        );

        return $response;
    }

    protected function parseResponseData(Element $registeredUser) {
        $data =  [
            Parameters::REDIRECT => RoutePaths::getPath(RouteType::ACCOUNT_COMPANY_ROLES)
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
