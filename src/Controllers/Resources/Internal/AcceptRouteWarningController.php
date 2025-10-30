<?php

namespace FWK\Controllers\Resources\Internal;

use FWK\Core\Controllers\BaseJsonController;
use FWK\Core\Resources\Session;
use SDK\Core\Dtos\Element;
use SDK\Core\Resources\BatchRequests;

/**
 * This is the AcceptRouteWarning controller.
 * This class extends BaseJsonController (FWK\Core\Controllers\BaseJsonController), see this class.
 *
 * @see BaseJsonController
 *
 * @package FWK\Controllers\Resources\Internal
 */
class AcceptRouteWarningController extends BaseJsonController {

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
        $session = Session::getInstance();
        $session->acceptRouteWarning();
        return new class($session) extends Element {
            private bool $acceptRouteWarning = true;

            public function __construct($session) {
                $this->acceptRouteWarning = $session->getRouteWarningAccepted();
            }

            public function jsonSerialize(): mixed {
                return ['acceptRouteWarning' => $this->acceptRouteWarning];
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
