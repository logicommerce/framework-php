<?php

namespace FWK\Controllers\Account\Internal;

use FWK\Core\Controllers\BaseJsonController;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\Resources\Loader;
use FWK\Enums\Parameters;
use SDK\Core\Dtos\Element;
use FWK\Enums\Services;
use SDK\Core\Resources\BatchRequests;

/**
 * This is the login simulation controller.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @see BaseHtmlController
 *
 * @package FWK\Controllers\Account\Internal
 */
class LoginSimulationController extends BaseJsonController {

    private const USER = 'user';

    protected bool $loggedInRequired = true;

    protected bool $salesAgentRequired = true;

    protected string $redirect = '';

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FilterInputFactory::getCustomerIdParameter();
    }

    /**
     * This method launches the adequate actions against the SDK (through the FWK services) and returns the response data. 
     *
     * @return Element
     */
    protected function getResponseData(): ?Element {
        $response = Loader::service(Services::ACCOUNT)->salesAgentLogin($this->getRequestParam(Parameters::CUSTOMER_ID, true));
        $this->redirect = $this->getRequestParam(Parameters::REDIRECT, false, '');
        //$this->responseMessageError = Utils::getErrorLabelValue($response);
        return $response;
    }

    /**
     * This method parses the given Element and returns it.
     * 
     * @param Element $response
     * 
     * @return \SDK\Core\Dtos\Element
     */
    protected function parseResponseData(Element $user) {
        return [
            Parameters::REDIRECT => $this->redirect,
            self::USER => $this->getSession()->getUser()
        ];
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
