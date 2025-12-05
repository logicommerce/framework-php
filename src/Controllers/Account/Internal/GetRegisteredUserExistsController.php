<?php

namespace FWK\Controllers\Account\Internal;

use FWK\Core\Controllers\BaseJsonController;
use FWK\Core\FilterInput\FilterInput;
use FWK\Core\Resources\Loader;
use FWK\Enums\Parameters;
use FWK\Enums\Services;
use SDK\Core\Application;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Common\Route;
use SDK\Core\Dtos\Element;
use SDK\Enums\UserKeyCriteria;
use SDK\Services\Parameters\Groups\Account\RegisteredUserExistsParametersGroup;

/**
 * This is the get registered user exists controller.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @see BaseHtmlController
 *
 * @package FWK\Controllers\Account\Internal
 */
class GetRegisteredUserExistsController extends BaseJsonController {

    protected string $userKeyCriteria = '';
    protected ?RegisteredUserExistsParametersGroup $registeredUserExistsParametersGroup = null;

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->userKeyCriteria = Application::getInstance()->getEcommerceSettings()->getUserAccountsSettings()->getUserKeyCriteria();
        $this->registeredUserExistsParametersGroup = new RegisteredUserExistsParametersGroup();
    }

    /**
     * This method returns an array of parameters, indicating in each node the parameter name and 
     * the filter to apply. This method must be overridden in extended controllers to add new 
     * parameters to self::requestParams.
     * 
     * @return mixed
     */
    protected function getFilterParams(): array {
        return [Parameters::Q => new FilterInput([
            FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false
        ])] + [Parameters::REGISTERED_USER_SEARCH_TYPE => new FilterInput([
            FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false
        ])];
    }

    /**
     * This method launches the adequate actions against the SDK (through the FWK services) and 
     * returns the response data. 
     *
     * @return Element
     */
    protected function getResponseData(): ?Element {
        $q = $this->getRequestParam(Parameters::Q, false, '');
        if ($this->getRequestParam(Parameters::REGISTERED_USER_SEARCH_TYPE, false, '') == UserKeyCriteria::USERNAME) {
            $this->userKeyCriteria = UserKeyCriteria::USERNAME;
        }
        switch ($this->userKeyCriteria) {
            case UserKeyCriteria::PID:
                $this->registeredUserExistsParametersGroup->setPId($q);
                break;
            case UserKeyCriteria::EMAIL:
                $this->registeredUserExistsParametersGroup->setEmail($q);
                break;
            case UserKeyCriteria::USERNAME:
                $this->registeredUserExistsParametersGroup->setUsername($q);
                break;
        }
        return Loader::service(Services::ACCOUNT)->getRegisteredUsersExists($this->registeredUserExistsParametersGroup);
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
