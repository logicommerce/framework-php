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
use FWK\ViewHelpers\Account\Macro\RegisteredUserUpdateForm;
use SDK\Core\Dtos\Element;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Common\Route;
use SDK\Services\Parameters\Groups\Account\UpdateRegisteredUserParametersGroup;

/**
 * This is the create registered user controller.
 * This class extends BaseJsonController (FWK\Core\Controllers\BaseJsonController), see this class.
 *
 * @see BaseJsonController
 *
 * @package FWK\Controllers\Account\Internal
 */
class UpdateRegisteredUserController extends BaseJsonController {
    use CheckCaptcha;
    private const REGISTERED_USER = 'registeredUser';

    protected bool $loggedInRequired = true;

    protected string $redirect = '';

    protected string $accountId = '';

    protected int $registeredUserId = 0;

    protected ?UpdateRegisteredUserParametersGroup $updateRegisteredUserParametersGroup = null;

    private ?AccountService $accountService = null;

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->updateRegisteredUserParametersGroup = new UpdateRegisteredUserParametersGroup();
        $this->accountService = Loader::service(Services::ACCOUNT);
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FormFactory::setRegisteredUser(null)->getInputFilterParameters();
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
        $this->checkCaptcha();
        $this->accountId = $this->getRequestParam(Parameters::ACCOUNT_ID, true);
        $this->registeredUserId = $this->getRequestParam(Parameters::REGISTERED_USER_ID, true);

        $data = $this->getRequestParams();
        unset($data[Parameters::ACCOUNT_ID]);
        unset($data[Parameters::REGISTERED_USER_ID]);
        if (strlen(trim($data[Parameters::BIRTHDAY])) > 0) {
            $data[Parameters::BIRTHDAY] = new \DateTime($data[Parameters::BIRTHDAY]);
        } else {
            unset($data[Parameters::BIRTHDAY]);
        }

        $this->accountService->generateParametersGroupFromArray($this->updateRegisteredUserParametersGroup, $data);
        $this->accountService->applyRegisteredUserFields($this->updateRegisteredUserParametersGroup, $data);
        $response = $this->accountService->updateRegisteredUserMe($this->updateRegisteredUserParametersGroup);

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
            Parameters::REDIRECT => RoutePaths::getPath(RouteType::REGISTERED_USER),
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
