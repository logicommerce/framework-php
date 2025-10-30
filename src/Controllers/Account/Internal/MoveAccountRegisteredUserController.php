<?php

namespace FWK\Controllers\Account\Internal;

use FWK\Core\Controllers\BaseJsonController;
use FWK\Core\Controllers\Traits\CheckCaptcha;
use FWK\Core\FilterInput\FilterInputFactory;
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
use SDK\Enums\AccountKey;
use SDK\Services\Parameters\Groups\Account\UpdateAccountRegisteredUsersParametersGroup;

/**
 * This class handles the movement of registered users between accounts.
 * This class extends BaseJsonController (FWK\Core\Controllers\BaseJsonController), see this class.
 *
 * @see BaseJsonController
 *
 * @package FWK\Controllers\Account\Internal
 */
class MoveAccountRegisteredUserController extends BaseJsonController {
    use CheckCaptcha;

    private const REGISTERED_USER = 'registeredUser';

    protected bool $loggedInRequired = true;

    protected string $redirect = '';

    protected string $accountId = '';

    protected string $id = '';

    protected int $registeredUserId = 0;

    protected ?UpdateAccountRegisteredUsersParametersGroup $updateAccountRegisteredUsersParametersGroup = null;

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
            FilterInputFactory::getAccountIdParameter() +
            FormFactory::getAccountRegisteredUserMove()->getInputFilterParameters();
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
        $data = $this->getRequestParams();
        $this->accountId = $data[Parameters::ACCOUNT_ID] ?? '';
        $this->id = $data[Parameters::ID] ?? AccountKey::USED;
        $this->registeredUserId = $data[Parameters::REGISTERED_USER_ID] ?? 0;

        $this->accountService->generateParametersGroupFromArray($this->updateAccountRegisteredUsersParametersGroup, $data);
        $response = $this->accountService->updateAccountRegisteredUser($this->id, $this->registeredUserId, $this->updateAccountRegisteredUsersParametersGroup);

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
            Parameters::REDIRECT => str_replace(AccountKey::USED, $this->accountId, RoutePaths::getPath(RouteType::ACCOUNT_REGISTERED_USERS)),
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
