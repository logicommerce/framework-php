<?php

namespace FWK\Controllers\Basket;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\Resources\Loader;
use FWK\Core\Resources\Response;
use FWK\Core\Resources\RoutePaths;
use FWK\Core\Resources\Router;
use FWK\Core\Resources\Utils;
use FWK\Enums\Parameters;
use FWK\Enums\Services;
use SDK\Core\Resources\BatchRequests;
use SDK\Services\Parameters\Groups\Basket\SetRowsParametersGroup;

/**
 * This is the base basket controller
 * This class extends BaseHtmlController, see this class.
 *
 * @controllerData: 
 *   <p>self::SET_ROWS_INCIDENCES: \SDK\Dtos\WarningAddProduct[]</p>
 *   <p>self::SET_ROWS_ERROR_MESSAGE: string</p>
 * 
 * @twigContent: \themes\{{themeName}}\{{version}}\Content\Basket\Basket\default.html.twig
 * 
 * @RouteType: \SDK\Enums\RouteType::BASKET
 * 
 * @filterParams: \FWK\Core\FilterInput\FilterInputFactory::getHParameter()
 *
 * @package FWK\Controllers\Basket
 */
class BasketController extends BaseHtmlController {

    protected const SET_ROWS_INCIDENCES = 'setRowsIncidences';

    protected const SET_ROWS_ERROR_MESSAGE = 'setRowsErrorMessage';

    protected ?string $hash = null;

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FilterInputFactory::getHParameter();
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
        $this->hash = $this->getRequestParam(Parameters::H, false, "");

        if (!empty($this->hash)) {
            $setRowsParametersGroup = new SetRowsParametersGroup();
            $setRowsParametersGroup->setHash($this->hash);
            $responseSetRows = Loader::service(Services::BASKET)->setRows($setRowsParametersGroup);
            if (!is_null($responseSetRows->getError())) {
                $this->setDataValue(self::SET_ROWS_ERROR_MESSAGE, Utils::getErrorLabelValue($responseSetRows));
            } else {
                $this->setDataValue(self::SET_ROWS_INCIDENCES, $responseSetRows->getIncidences());
            }
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
        $redirect = self::getTheme()->getConfiguration()->getBasket()->getRecoveryBasketRouteTypeRedirect();
        if (strlen($redirect)) {
            Response::redirect(RoutePaths::getPath($redirect));
        }
    }
}
