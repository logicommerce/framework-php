<?php

namespace FWK\Controllers\Account\Internal;

use FWK\Core\Controllers\BaseJsonController;
use FWK\Core\Controllers\Traits\CheckCaptcha;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\Form\FormFactory;
use FWK\Core\Resources\Loader;
use FWK\Core\Resources\RoutePaths;
use FWK\Core\Resources\Utils;
use FWK\Enums\LanguageLabels;
use FWK\Enums\Parameters;
use FWK\Enums\RouteType;
use FWK\Enums\Services;
use FWK\Services\AccountService;
use SDK\Core\Dtos\Element;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Common\Route;
use SDK\Services\Parameters\Groups\Account\ApproveAccountRegisteredUserParametersGroup;
use SDK\Services\Parameters\Groups\Account\RegisteredUserApproveUpdateParametersGroup;

/**
 * This is the ApproveAccountRegisteredUserController class.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @see BaseHtmlController
 *
 * @package FWK\Controllers\Account\Internal
 */
class ApproveAccountRegisteredUserController extends BaseJsonController {
    use CheckCaptcha;

    public const REGISTERED_USER = "registeredUser";

    protected ?AccountService $accountService = null;

    protected ?ApproveAccountRegisteredUserParametersGroup $approveAccountRegisteredUserParametersGroup = null;

    protected string $hash = "";

    protected string $accountId = "";

    protected string $registeredUserId = "";

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->accountService = Loader::service(Services::ACCOUNT);
        $this->approveAccountRegisteredUserParametersGroup = new ApproveAccountRegisteredUserParametersGroup();
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FormFactory::getAccountRegisteredUsersApprove()->getInputFilterParameters();
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
     * This method initialize applied parameters, runs previously to run preSendControllerBaseBatchData
     *
     */
    protected function initializeAppliedParameters(): void {
        $registeredUserApproveUpdate = new RegisteredUserApproveUpdateParametersGroup();

        $data = $this->getRequestParams();
        $birthday = $data[Parameters::BIRTHDAY] ?? '';
        if (strlen(trim($birthday)) > 0) {
            $data[Parameters::BIRTHDAY] = new \DateTime($birthday);
        } else {
            unset($data[Parameters::BIRTHDAY]);
        }
        $this->accountService->generateParametersGroupFromArray($registeredUserApproveUpdate, $data);
        $this->accountService->generateParametersGroupFromArray($this->approveAccountRegisteredUserParametersGroup, $data);
        $this->approveAccountRegisteredUserParametersGroup->setRegisteredUser($registeredUserApproveUpdate);
    }

    /**
     * This method launches the adequate actions against the SDK (through the FWK services) and returns the response data. 
     *
     * @return Element
     */
    protected function getResponseData(): ?Element {
        $this->checkCaptcha();
        $this->hash = $this->getRequestParam(Parameters::HASH, true);
        $this->accountId = $this->getRequestParam(Parameters::ACCOUNT_ID, true);
        $this->registeredUserId = $this->getRequestParam(Parameters::REGISTERED_USER_ID, true);

        $response = $this->accountService->approveRegisteredUser($this->accountId, $this->registeredUserId, $this->approveAccountRegisteredUserParametersGroup);
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
        $replacements = [
            '{' . Parameters::ACCOUNT_ID . '}' => $this->accountId,
            '{' . Parameters::REGISTERED_USER_ID . '}' => $this->registeredUserId
        ];
        $path =  RoutePaths::getPath(RouteType::ACCOUNT_REGISTERED_USER_APPROVE) . "?hash=" . $this->hash;
        $path = str_replace(array_keys($replacements), array_values($replacements), $path);

        $data =  [
            Parameters::REDIRECT => $path,
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
