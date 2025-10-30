<?php

namespace FWK\Controllers\Checkout\Internal;

use SDK\Core\Dtos\Element;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Common\Route;
use FWK\Core\Controllers\BaseJsonController;
use FWK\Enums\Services;
use FWK\Core\Resources\Loader;
use FWK\Enums\LanguageLabels;
use FWK\Services\BasketService;

/**
 * This is the ClearBasketController class.
 * This class extends BaseJsonController, see this class.
 *
 * @see BaseJsonController
 *
 * @package FWK\Controllers\Checkout\Internal
 */
class ClearBasketController extends BaseJsonController {

    private ?BasketService $basketService = null;

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->basketService = Loader::service(Services::BASKET);
        $this->responseMessage = $this->language->getLabelValue(LanguageLabels::CLEAR_BASKET_OK, $this->responseMessage);
        $this->responseMessageError = $this->language->getLabelValue(LanguageLabels::ERROR, $this->responseMessageError);
    }

    /**
     * This method launches the adequate actions against the SDK (through the FWK services) and returns the response data. 
     *
     * @return Element
     */
    protected function getResponseData(): ?Element {
        return $this->basketService->clear();
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
