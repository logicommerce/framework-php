<?php

namespace FWK\Controllers\Basket\Internal;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\FilterInput\FilterInput;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Enums\Parameters;
use SDK\Core\Resources\BatchRequests;

/**
 * This is the LockedStockController controller class.
 * This class extends FWK\Core\Controllers\BaseHtmlController, see this class.
 *
 * @twigContent: \themes\{{themeName}}\{{version}}\Content\Basket\Internal\LockedStock\default.html.twig
 * 
 * @RouteType: \FWK\Enums\RouteTypes\InternalBasket::MINI_BASKET
 *
 * @package FWK\Controllers\Basket\Internal
 */
class LockedStockController extends BaseHtmlController {

    public const EXPIRED = 'expired';

    public const WARNING = 'warning';

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return [
            Parameters::TYPE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_TO_LOWER,
                FilterInput::CONFIGURATION_FILTER_KEY_FUNCTION_VALIDATOR => function ($value) {
                    return $value === self::EXPIRED || $value === self::WARNING;
                }
            ])
        ];
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
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
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
