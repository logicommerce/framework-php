<?php

namespace FWK\Controllers\Account\Internal;

use FWK\Core\Controllers\BaseJsonController;
use FWK\Core\Controllers\Traits\CheckCaptcha;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\Form\FormFactory;
use FWK\Core\Resources\Loader;
use FWK\Core\Resources\Utils;
use FWK\Enums\LanguageLabels;
use FWK\Enums\Parameters;
use FWK\Enums\Services;
use FWK\Services\AccountService;
use SDK\Core\Dtos\Element;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Accounts\EmployeeVal;
use SDK\Dtos\Common\Route;
use SDK\Enums\AccountKey;
use SDK\Services\Parameters\Groups\Account\UpdateAccountRegisteredUsersParametersGroup;
use SDK\Services\Parameters\Groups\Account\RegisteredUserParametersGroup;

/**
 * This is the update account registered user controller.
 * This class extends BaseJsonController (FWK\Core\Controllers\BaseJsonController), see this class.
 *
 * @see BaseJsonController
 *
 * @package FWK\Controllers\Account\Internal
 */
class UpdateAccountRegisteredUserController extends BaseJsonController {
    use CheckCaptcha;
    private const REGISTERED_USER = 'registeredUser';

    protected bool $loggedInRequired = true;

    protected string $redirect = '';

    protected string $accountId = '';

    protected int $registeredUserId = 0;

    protected ?UpdateAccountRegisteredUsersParametersGroup $updateAccountRegisteredUsersParametersGroup = null;

    protected ?RegisteredUserParametersGroup $registeredUserParametersGroup = null;

    private ?AccountService $accountService = null;

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->updateAccountRegisteredUsersParametersGroup = new UpdateAccountRegisteredUsersParametersGroup();
        $this->accountService = Loader::service(Services::ACCOUNT);
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return
            FilterInputFactory::getRedirectParameter() +
            FilterInputFactory::getRoleIdParameter() +
            FilterInputFactory::getJobParameter() +
            FormFactory::getAccountRegisteredUserUpdate()->getInputFilterParameters();
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
     * This method launches the adequate actions against the SDK (through the FWK services) and returns the response data. 
     *
     * @return Element
     */
    protected function getResponseData(): ?Element {
        $requestParams = $this->getRequestParams();
        $this->accountId = $this->getRequestParam(Parameters::ACCOUNT_ID, true);
        $this->redirect = $this->getRequestParam(Parameters::REDIRECT, false, '');
        $this->redirect = str_replace(AccountKey::USED, $this->accountId, $this->redirect);
        $this->registeredUserId = $this->getRequestParam(Parameters::REGISTERED_USER_ID, true);
        unset($requestParams[Parameters::ACCOUNT_ID]);
        unset($requestParams[Parameters::REDIRECT]);
        unset($requestParams[Parameters::REGISTERED_USER_ID]);
        $this->accountService->generateParametersGroupFromArray($this->updateAccountRegisteredUsersParametersGroup, $requestParams);
        $this->accountService->applyRegisteredUserFields($this->updateAccountRegisteredUsersParametersGroup, $requestParams);
        if (count($this->updateAccountRegisteredUsersParametersGroup->toArray())) {
            $response = $this->accountService->updateAccountRegisteredUser($this->accountId, $this->registeredUserId, $this->updateAccountRegisteredUsersParametersGroup);
        } else {
            $response = new EmployeeVal();
        }

        if (!is_null($response->getError())) {
            $this->responseMessageError = Utils::getErrorLabelValue($response);
        }
        $this->responseMessage = $this->language->getLabelValue(
            LanguageLabels::SAVED,
            $this->responseMessage
        );

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
            Parameters::REDIRECT => $this->redirect,
            self::REGISTERED_USER => $registeredUser
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
