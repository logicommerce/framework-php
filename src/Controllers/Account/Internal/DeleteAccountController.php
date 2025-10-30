<?php

namespace FWK\Controllers\Account\Internal;

use FWK\Enums\Services;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\Resources\Loader;
use SDK\Dtos\Common\Route;
use FWK\Core\Form\FormFactory;
use SDK\Core\Dtos\Element;
use SDK\Core\Resources\BatchRequests;
use FWK\Core\Controllers\BaseJsonController;
use FWK\Enums\LanguageLabels;
use FWK\Enums\Parameters;
use FWK\Core\Resources\Utils;
use FWK\Core\Controllers\Traits\CheckCaptcha;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\Resources\RoutePaths;
use FWK\Enums\RouteType;
use FWK\Services\AccountService;
use SDK\Enums\AccountType;
use SDK\Services\Parameters\Groups\Account\DeleteAccountParametersGroup;

/**
 * This is the DeleteAccountController controller class.
 * This class extends FWK\Core\Controllers\BaseJsonController, see this class.
 *
 * @see BaseJsonController
 * 
 * @uses CheckCaptcha
 *
 * @package FWK\Controllers\User\Internal
 */
class DeleteAccountController extends BaseJsonController {
    use CheckCaptcha;

    protected bool $loggedInRequired = true;

    private ?AccountService $accountService = null;

    private ?DeleteAccountParametersGroup $deleteAccountParameterGroup = null;

    /**
     * Constructor method.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->accountService = Loader::service(Services::ACCOUNT);
        $this->deleteAccountParameterGroup = new DeleteAccountParametersGroup();
        if (in_array($this->getSession()->getBasket()->getAccount()->getType(), AccountType::getCompanyTypes(), true)) {
            $this->responseMessage = $this->language->getLabelValue(LanguageLabels::COMPANY_DELETED, $this->responseMessage);
        } else {
            $this->responseMessage = $this->language->getLabelValue(LanguageLabels::ACCOUNT_DELETED, $this->responseMessage);
        }
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FormFactory::getDeleteAccount()->getInputFilterParameters() + FilterInputFactory::getIdParameter();
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
        $this->deleteAccountParameterGroup->setPassword($this->getRequestParam(Parameters::PASSWORD, true));
        $response = $this->accountService->deleteAccount($this->getRequestParam(Parameters::ID, true), $this->deleteAccountParameterGroup);
        $this->responseMessageError = Utils::getErrorLabelValue($response);
        return $response;
    }

    protected function parseResponseData(Element $registeredUser) {
        $data =  [
            Parameters::REDIRECT => RoutePaths::getPath(RouteType::ACCOUNT_COMPANY_STRUCTURE),
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
