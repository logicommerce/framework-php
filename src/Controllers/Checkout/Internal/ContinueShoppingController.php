<?php

namespace FWK\Controllers\Checkout\Internal;

use SDK\Core\Dtos\Element;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Common\Route;
use FWK\Core\Controllers\BaseJsonController;
use FWK\Core\Resources\Loader;
use FWK\Enums\Services;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\Resources\RoutePaths;
use FWK\Core\Resources\Session;
use SDK\Enums\RouteType;

/**
 * This is the ContinueShoppingController controller class.
 * This class extends FWK\Core\Controllers\BaseJsonController, see this class.
 *
 * @see BaseJsonController
 *
 * @package FWK\Controllers\Checkout\Internal
 */
class ContinueShoppingController extends BaseJsonController {

    private $basketService = null;

    /**
     * Constructor method.
     * 
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->basketService = Loader::service(Services::BASKET);
        // Checkout no es una entitat no te ParametersGroup:
        // $this->loginParameters = new LoginParametersGroup();
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
     * This method launches the adequate actions against the SDK (through the FWK services) and returns the response data. 
     *
     * @return Element
     */
    protected function getResponseData(): ?Element {
        return Session::getInstance()->getBasket();
    }

    /**
     * This method parses the given Element and returns it.
     * 
     * @param Element $response
     * 
     * @return \SDK\Core\Dtos\Element
     */
    protected function parseResponseData(Element $basket) {
        return [
            'redirect' => RoutePaths::getPath(RouteType::HOME)
        ];
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
