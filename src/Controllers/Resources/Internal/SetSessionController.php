<?php

namespace FWK\Controllers\Resources\Internal;

use FWK\Core\Controllers\BaseJsonController;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\Resources\Response;
use FWK\Enums\Parameters;
use SDK\Core\Dtos\Element;
use SDK\Core\Resources\ApiRequest;
use SDK\Core\Resources\BatchRequests;
use SDK\Core\Resources\Cookie;

/**
 * This is the SetSession controller.
 * It sets the basketToken cookie with the given basketToken parameter and redirects to the given redirect path.
 * On the next request the Session detects that the basketToken cookie differs from the session one and resets the basket.
 * If any of the parameters is missing or invalid, it returns the json error response instead.
 * This class extends BaseJsonController (FWK\Core\Controllers\BaseJsonController), see this class.
 *
 * @see BaseJsonController
 *
 * @RouteType: \FWK\Enums\RouteTypes\InternalResources::SET_SESSION
 *
 * @package FWK\Controllers\Resources\Internal
 */
class SetSessionController extends BaseJsonController {

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FilterInputFactory::getSetSessionParameters();
    }

    /**
     * This method initialize applied parameters, runs previously to run preSendControllerBaseBatchData
     *
     */
    protected function initializeAppliedParameters(): void {
    }

    /**
     * This method launches the adequate actions against the SDK (through the FWK services) and returns the response data.
     *
     * @return Element
     */
    protected function getResponseData(): ?Element {
        $basketToken = $this->getRequestParam(Parameters::BASKET_TOKEN, true);
        $redirect = $this->getRequestParam(Parameters::REDIRECT, true);
        Cookie::set(ApiRequest::BASKET_TOKEN, $basketToken);
        Response::redirect($redirect, 302);
        return null;
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
