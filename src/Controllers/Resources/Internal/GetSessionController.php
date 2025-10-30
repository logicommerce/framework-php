<?php

namespace FWK\Controllers\Resources\Internal;

use FWK\Core\Controllers\BaseJsonController;
use FWK\Core\Resources\Response;
use FWK\Core\Resources\Session;
use SDK\Core\Dtos\Element;
use SDK\Core\Resources\ApiRequest;
use SDK\Core\Resources\BatchRequests;
use SDK\Core\Resources\Cookie;

/**
 * This is the GetSession controller.
 * This class extends BaseJsonController (FWK\Core\Controllers\BaseJsonController), see this class.
 *
 * @see BaseJsonController
 * 
 * @uses CheckCaptcha
 *
 * @package FWK\Controllers\Resources\Internal
 */
class GetSessionController extends BaseJsonController {

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
        Response::addHeader(Session::SESSION_TOKEN . ':' . Cookie::getCookieDefinition(Session::SESSION_TOKEN, session_id()));
        Response::addHeader(ApiRequest::BASKET_TOKEN . ':' . Cookie::getDefinition(ApiRequest::BASKET_TOKEN));
        return new class() extends Element {
            public function jsonSerialize(): mixed {
                return [
                    Session::SESSION_TOKEN => Cookie::getCookieDefinition(Session::SESSION_TOKEN, session_id()),
                    ApiRequest::BASKET_TOKEN => Cookie::getDefinition(ApiRequest::BASKET_TOKEN),
                ];
            }
        };
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
