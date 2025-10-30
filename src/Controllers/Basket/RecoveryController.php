<?php

namespace FWK\Controllers\Basket;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\Resources\Loader;
use FWK\Core\Resources\Response;
use FWK\Core\Resources\RoutePaths;
use FWK\Enums\Parameters;
use FWK\Enums\Services;
use SDK\Core\Resources\BatchRequests;

/**
 * This is the basket recovery controller.
 * This class extends BaseHtmlController, see this class.
 *
 * @controllerData: self::CONTROLLER_ITEM: \SDK\Dtos\Basket\Basket
 * 
 * @twigContent: \themes\{{themeName}}\{{version}}\Content\Basket\Recovery\default.html.twig
 * 
 * @RouteType: \SDK\Enums\RouteType::BASKET_RECOVERY
 *
 * @package FWK\Controllers\Basket
 */
class RecoveryController extends BaseHtmlController {

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FilterInputFactory::getHashParameter();
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

    /**
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     */
    final function setControllerBaseData(): void {
        $recoveryBasket = Loader::service(Services::ORDER)->recoveryBasket($this->getRequestParam(Parameters::HASH, true));
        if (is_null($recoveryBasket->getError())) {
            $redirect = self::getTheme()->getConfiguration()->getBasket()->getRecoveryBasketRouteTypeRedirect();
            if (strlen($redirect)) {
                Response::redirect(RoutePaths::getPath($redirect));
            }
        }
        $this->setDataValue(self::CONTROLLER_ITEM, $recoveryBasket);
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
