<?php

namespace FWK\Controllers\Banner\Internal;

use FWK\Core\Resources\Loader;
use FWK\Core\Controllers\BaseJsonController;
use SDK\Core\Dtos\Element;
use SDK\Core\Resources\BatchRequests;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Enums\Parameters;
use FWK\Services\BannerService;

/**
 * This is the banner DoneClickController class.
 *
 * @controllerData: 
 *  <p>self::RESPONSE[self::DATA] => \SDK\Core\Dtos\Status</p>
 * @RouteType: \FWK\Enums\RouteTypes\InternalBanner::DONE_CLICK
 * 
 * @package FWK\Controllers\Banner\Internal
 */
class DoneClickController extends BaseJsonController {

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
     * This method launches the adequate actions against the SDK (through the FWK services) and returns the response data. 
     *
     * @return Element
     */
    protected function getResponseData(): ?Element {
        $this->appliedParameters = [
            Parameters::ID => $this->getRequestParam(Parameters::ID, true)
        ];
        /** @var BannerService $bannerService */
        $bannerService = Loader::service(\FWK\Enums\Services::BANNER);
        return $bannerService->doneClick($this->getRequestParam(Parameters::ID));
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
