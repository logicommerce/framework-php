<?php

namespace FWK\Controllers\Account\Internal;

use FWK\Core\Controllers\BaseJsonController;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\Resources\Loader;
use FWK\Core\Theme\Dtos\ItemList;
use FWK\Enums\Parameters;
use FWK\Enums\Services;
use FWK\Services\AccountService;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Common\Route;
use SDK\Core\Application;
use SDK\Core\Dtos\Element;
use SDK\Enums\IncludeCompanyStructure;
use SDK\Enums\UserKeyCriteria;
use SDK\Services\Parameters\Groups\Account\SearchAccountRegisteredUserParametersGroup;

/**
 * This is the SearchAccountRegisteredUserController class.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @see BaseHtmlController
 *
 * @package FWK\Controllers\Account\Internal
 */
class SearchAccountRegisteredUserController extends BaseJsonController {

    protected string $userKeyCriteria = '';

    protected ?ItemList $itemListConfiguration = null;

    protected ?AccountService $accountService = null;

    protected ?SearchAccountRegisteredUserParametersGroup $searchAccountRegisteredUserParametersGroup = null;


    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->userKeyCriteria = Application::getInstance()->getEcommerceSettings()->getUserAccountsSettings()->getUserKeyCriteria();
        $this->itemListConfiguration = self::getTheme()->getConfiguration()->getAccount()->getRegisteredUsersList();
        $this->accountService = Loader::service(Services::ACCOUNT);
        $this->searchAccountRegisteredUserParametersGroup = new SearchAccountRegisteredUserParametersGroup();
    }

    /**
     * This method returns an array of parameters, indicating in each node the parameter name and 
     * the filter to apply. This method must be overridden in extended controllers to add new 
     * parameters to self::requestParams.
     * 
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FilterInputFactory::getDataParameters() + FilterInputFactory::getAccountIdParameter() + FilterInputFactory::getPaginableItemsParameter();
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
        return FilterInputHandler::PARAMS_FROM_GET;
    }

    /**
     * This method launches the adequate actions against the SDK (through the FWK services) and 
     * returns the response data. 
     *
     * @return Element
     */
    protected function getResponseData(): ?Element {
        $data = $this->getRequestParam(Parameters::DATA, false, '');
        $accountId = $this->getRequestParam(Parameters::ACCOUNT_ID, false, '');
        $page = $this->getRequestParam(Parameters::PAGE, false, 1);

        if ($data != '') {
            switch ($this->userKeyCriteria) {
                case UserKeyCriteria::PID:
                    $this->searchAccountRegisteredUserParametersGroup->setPId($data);
                    break;
                case UserKeyCriteria::EMAIL:
                    $this->searchAccountRegisteredUserParametersGroup->setEmail($data);
                    break;
                case UserKeyCriteria::USERNAME:
                    $this->searchAccountRegisteredUserParametersGroup->setUsername($data);
                    break;
                default:
                    throw new CommerceException(self::class . 'Undefined user key criteria: ' . $this->userKeyCriteria, CommerceException::FORM_FACTORY_UNDEFINED_USER_KEY_CRITERIA);
            }
        }
        $registeredUsersRequest = array_merge(
            $this->itemListConfiguration->getDefaultParametersValues(),
            $this->getRequestParams()
        );
        $this->accountService->generateParametersGroupFromArray($this->searchAccountRegisteredUserParametersGroup, $registeredUsersRequest);
        $this->searchAccountRegisteredUserParametersGroup->setIncludeCompanyStructure(IncludeCompanyStructure::ALL);
        $this->searchAccountRegisteredUserParametersGroup->setPage($page);
        return $this->accountService->getRegisteredUserSearch($accountId, $this->searchAccountRegisteredUserParametersGroup);
    }

    /**
     * This method is the one in charge of defining all the data batch requests that are needed for
     * the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request where the method will add the batch requests.
     * @return void
     */
    protected function setBatchData(BatchRequests $request): void {
    }

    /**
     * This method is the one in charge of defining all the data batch requests that are basic for
     * the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
    }
}
