<?php

namespace FWK\Controllers\Account\Internal;

use FWK\Core\Controllers\BaseJsonController;
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
use SDK\Core\Services\Parameters\Groups\CustomTagDataParametersGroup;
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

    protected ?MasterUpdateParametersGroup $masterUpdateParametersGroup = null;

    protected ?RegisteredUserParametersGroup $registeredUserParametersGroup = null;

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
        $this->masterUpdateParametersGroup = new MasterUpdateParametersGroup();
        $this->registeredUserParametersGroup = new RegisteredUserParametersGroup();
        $this->accountService = Loader::service(Services::ACCOUNT);
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FilterInputFactory::getEmailParameter()
            + FilterInputFactory::getCustomTagsParameter()
            + FilterInputFactory::getRedirectParameter()
            + FilterInputFactory::getRoleIdParameter()
            + FilterInputFactory::getJobParameter()
            + FilterInputFactory::getParentIdParameter()
            + FormFactory::getAccountEditForm()->getInputFilterParameters();
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
        $this->accountId = $requestParams[Parameters::ACCOUNT_ID];

        $customTags = [];
        foreach ($requestParams[Parameters::CUSTOM_TAGS] as $id => $value) {
            if (!is_null($value)) {
                $parts = explode('_', $id);
                $customTagId = end($parts);

                $customTag = new UserCustomTagParametersGroup();
                $customTag->setCustomTagId($customTagId);
                $customTagData = new CustomTagDataParametersGroup();
                $objValue = json_decode($value);
                if (is_object($objValue) && property_exists($objValue, 'extension') && property_exists($objValue, 'fileName') && property_exists($objValue, 'value')) {
                    $customTagData->setExtension($objValue->extension);
                    $customTagData->setFileName($objValue->fileName);
                    $customTagData->setValue($objValue->value);
                    $customTag->setData($customTagData);
                } else {
                    $customTagData->setValue($value);
                    $customTag->setData($customTagData);
                }
                $customTags[] = $customTag;
            }
        }
        unset($requestParams[Parameters::CUSTOM_TAGS]);

        $birthday = $requestParams[Parameters::BIRTHDAY] ?? '';
        if (strlen(trim($birthday)) > 0) {
            $requestParams[Parameters::BIRTHDAY] = new \DateTime($birthday);
        } else {
            unset($requestParams[Parameters::BIRTHDAY]);
        }

        $this->accountService->generateParametersGroupFromArray($this->updateAccountParametersGroup, $requestParams);
        unset($requestParams[Parameters::P_ID]);
        unset($requestParams[Parameters::USERNAME]);
        unset($requestParams[Parameters::EMAIL]);
        unset($requestParams[Parameters::IMAGE]);

        $this->accountService->generateParametersGroupFromArray($this->registeredUserParametersGroup, $requestParams);
        $this->accountService->applyRegisteredUserFields($this->registeredUserParametersGroup, $requestParams);
        $this->accountService->generateParametersGroupFromArray($this->masterUpdateParametersGroup, $requestParams);
        if (count($this->registeredUserParametersGroup->toArray()) > 0) {
            $this->masterUpdateParametersGroup->setRegisteredUser($this->registeredUserParametersGroup);
        }

        if (count($this->masterUpdateParametersGroup->toArray()) > 0) {
            $this->updateAccountParametersGroup->setMaster($this->masterUpdateParametersGroup);
        }

        $this->updateAccountParametersGroup->setCustomTags($customTags);

        $parentId = null;
        if (isset($requestParams[Parameters::PARENT_ID])) {
            $parentId = (int)$requestParams[Parameters::PARENT_ID];
            unset($requestParams[Parameters::PARENT_ID]);
        }
        if ($parentId !== null) {
            $this->updateAccountParametersGroup->setParentAccountId($parentId);
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
