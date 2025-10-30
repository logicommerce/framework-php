<?php

namespace FWK\Controllers\Checkout\Internal;

use SDK\Core\Resources\BatchRequests;
use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\Controllers\Traits\RichSaveForLaterRows;
use FWK\Core\Resources\Loader;
use FWK\Core\Resources\Response;
use FWK\Core\Resources\Utils;
use FWK\Enums\Services;

/**
 * This is the OSC internal save for later controller.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @see BaseHtmlController
 *
 * @package FWK\Controllers\Checkout\Internal
 */
class OscSaveForLaterController extends BaseHtmlController {
    use RichSaveForLaterRows;

    protected bool $loggedInRequired = true;

    /**
     * This method validate if the session is logged in. Else generate a forbidden response
     *
     * @return void
     */
    protected function validateLoggedIn(): void {
        if (!Utils::isSessionLoggedIn($this->getSession())) {
            Response::forbidden();
        }
    }

    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
        Loader::service(Services::USER)->addGetSaveForLaterListRows($requests, self::CONTROLLER_ITEM);
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
        $saveForLater = $this->getControllerData(self::CONTROLLER_ITEM);
        if (is_null($saveForLater->getError())) {
            $this->deleteControllerData(self::CONTROLLER_ITEM);
            $this->setDataValue(self::CONTROLLER_ITEM, $this->getRichSaveForLaterRows($saveForLater));
        }
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
}
