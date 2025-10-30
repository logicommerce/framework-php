<?php

namespace FWK\Controllers\User\Internal;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\Resources\Loader;
use FWK\Enums\Parameters;
use FWK\Enums\Services;
use SDK\Core\Resources\BatchRequests;

/**
 * This is the user rma controller.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @see BaseHtmlController
 *
 * @package FWK\Controllers\User
 */
class RmaController extends BaseHtmlController {

    protected bool $loggedInRequired = true;

    public const RMA = 'rma';

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FilterInputFactory::getIdParameter();
    }

    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
        Loader::service(Services::ORDER)->addGetRMA($requests, self::RMA, $this->getRequestParam(Parameters::ID, true));
    }

    /**
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     */
    protected function setControllerBaseData(): void {
        $aux = [];
        $aux[self::RMA] = $this->getControllerData(self::RMA);
        $this->setDataValue(self::CONTROLLER_ITEM, $aux);
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
     * This method runs after the batch requests (defined in the setBatchData methods) are resolved,
     * so here you can work with the response of the batch requests and calculate and set more needed data.
     *
     * @param array $additionalData
     *              Set additiona data to the controller data
     * 
     * @return void
     */
    protected function setData(array $additionalData = []): void {
    }
}
