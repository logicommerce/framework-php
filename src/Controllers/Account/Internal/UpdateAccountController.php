<?php

namespace FWK\Controllers\Account\Internal;

use DateTime;
use FWK\Core\Controllers\BaseJsonController;
use FWK\Core\FilterInput\FilterInput;
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
use SDK\Dtos\Common\Route;
use SDK\Services\Parameters\Groups\Account\MasterUpdateParametersGroup;
use SDK\Services\Parameters\Groups\Account\RegisteredUserParametersGroup;
use SDK\Services\Parameters\Groups\Account\UpdateAccountParametersGroup;
use SDK\Services\Parameters\Groups\User\UserCustomTagParametersGroup;

/**
 * This is the create registered user controller.
 * This class extends BaseJsonController (FWK\Core\Controllers\BaseJsonController), see this class.
 *
 * @see BaseJsonController
 *
 * @package FWK\Controllers\Account\Internal
 */
class UpdateAccountController extends BaseJsonController {

    protected bool $loggedInRequired = true;

    protected string $accountId = '';

    protected ?UpdateAccountParametersGroup $updateAccountParametersGroup = null;

    private ?AccountService $accountService = null;

    private ?String $redirect = null;

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->updateAccountParametersGroup = new UpdateAccountParametersGroup();
        $this->accountService = Loader::service(Services::ACCOUNT);
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FormFactory::getAccountEditForm()->getInputFilterParameters()
            + FilterInputFactory::getEmailParameter()
            + FilterInputFactory::getCustomTagsParameter()
            + FilterInputFactory::getRedirectParameter()
            + FilterInputFactory::getRoleIdParameter()
            + FilterInputFactory::getJobParameter()
            + FilterInputFactory::getParentIdParameter();
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
        $this->redirect = $this->getRequestParam(Parameters::REDIRECT, false, null);
        $this->accountId = $requestParams[Parameters::ID];
        unset($requestParams[Parameters::REDIRECT]);
        unset($requestParams[Parameters::ID]);
        unset($requestParams[Parameters::PATH]);
        $masterParams = [];
        $registeredUserParams = [];

        $this->moveParamIfExists($requestParams, $masterParams, Parameters::JOB);
        if (isset($requestParams[Parameters::ROLE_ID])) {
            $masterParams[Parameters::ROLE_ID] = $requestParams[Parameters::ROLE_ID];
        }
        unset($requestParams[Parameters::ROLE_ID]);

        $this->moveParamIfExists($requestParams, $registeredUserParams, Parameters::REGISTERED_USER_P_ID, Parameters::P_ID);
        $this->moveParamIfExists($requestParams, $registeredUserParams, Parameters::REGISTERED_USER_EMAIL, Parameters::EMAIL);
        $this->moveParamIfExists($requestParams, $registeredUserParams, Parameters::USERNAME);
        $this->moveParamIfExists($requestParams, $registeredUserParams, Parameters::GENDER);
        $this->moveParamIfExists($requestParams, $registeredUserParams, Parameters::FIRST_NAME);
        $this->moveParamIfExists($requestParams, $registeredUserParams, Parameters::LAST_NAME);
        if (isset($requestParams[Parameters::BIRTHDAY])) {
            $registeredUserParams[Parameters::BIRTHDAY] = new DateTime($requestParams[Parameters::BIRTHDAY]);
            unset($requestParams[Parameters::BIRTHDAY]);
        }
        $this->moveParamIfExists($requestParams, $registeredUserParams, Parameters::IMAGE2, Parameters::IMAGE);

        $customTagsData = [];
        if (isset($requestParams[Parameters::CUSTOM_TAGS])) {
            foreach ($requestParams[Parameters::CUSTOM_TAGS] as $key => $value) {
                if ($value == null) {
                    continue;
                }
                $parts = explode('_', $key);
                $customTagId = end($parts);

                $customTagParams = new UserCustomTagParametersGroup();
                $customTagParams->setCustomTagId((int)$customTagId);
                $customTagParams->setValue($value);

                $customTagsData[] = $customTagParams;
            }
            unset($requestParams[Parameters::CUSTOM_TAGS]);
        }

        $masterParametersGroup = null;
        if (!empty($masterParams) || !empty($registeredUserParams)) {
            $masterParametersGroup = new MasterUpdateParametersGroup();

            if (isset($masterParams[Parameters::JOB]) && $masterParams[Parameters::JOB] != '') {
                $masterParametersGroup->setJob($masterParams[Parameters::JOB]);
            }
            if (isset($masterParams[Parameters::ROLE_ID])) {
                $masterParametersGroup->setRoleId($masterParams[Parameters::ROLE_ID]);
            }

            if (!empty($registeredUserParams)) {
                $registeredUserGroup = new RegisteredUserParametersGroup();
                $this->accountService->generateParametersGroupFromArray($registeredUserGroup, $registeredUserParams);
                $masterParametersGroup->setRegisteredUser($registeredUserGroup);
            }
        }

        unset($requestParams[Parameters::MASTER]);

        // Handle parentId for company structure moves (only if present)
        $parentId = null;
        if (isset($requestParams[Parameters::PARENT_ID])) {
            $parentId = (int)$requestParams[Parameters::PARENT_ID];
            unset($requestParams[Parameters::PARENT_ID]);
        }

        $this->accountService->generateParametersGroupFromArray($this->updateAccountParametersGroup, $requestParams);

        if ($masterParametersGroup) {
            $this->updateAccountParametersGroup->setMaster($masterParametersGroup);
        }

        if ($parentId !== null) {
            $this->updateAccountParametersGroup->setParentAccountId($parentId);
        }

        foreach ($customTagsData as $customTag) {
            $this->updateAccountParametersGroup->addCustomTag($customTag);
        }
        $response = $this->accountService->updateAccountById($this->accountId, $this->updateAccountParametersGroup);

        if (!is_null($response->getError())) {
            $this->responseMessageError = Utils::getErrorLabelValue($response);
        }
        $this->responseMessage = $this->language->getLabelValue(
            LanguageLabels::SAVED,
            $this->responseMessage
        );

        return $response;
    }

    private function moveParamIfExists(array &$source, array &$target, string $sourceKey, string $targetKey = null): void {
        if (isset($source[$sourceKey])) {
            $target[$targetKey ?? $sourceKey] = $source[$sourceKey];
            unset($source[$sourceKey]);
        }
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
