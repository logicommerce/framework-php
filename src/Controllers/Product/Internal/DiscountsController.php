<?php

namespace FWK\Controllers\Product\Internal;

use SDK\Core\Resources\BatchRequests;
use FWK\Core\Resources\Loader;
use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Enums\Parameters;

/**
 * This is the product DiscountController class.
 * This class extends BaseHtmlController, see this class.
 *
 * @see BaseHtmlController
 *
 * @package FWK\Controllers\Product\Internal
 */
class DiscountsController extends BaseHtmlController {

    public const PRODUCT_ID = 'productId';

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
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
        Loader::service(\FWK\Enums\Services::PRODUCT)->addGetProductDiscounts(
            $requests,
            self::CONTROLLER_ITEM,
            $this->getRequestParam(Parameters::ID, true)
        );
    }

    /**
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     *
     */
    protected function setControllerBaseData(): void {
        $this->setDataValue(self::PRODUCT_ID, $this->getRequestParam(Parameters::ID, true));
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
     * This private method sends the batch requests of the controller to the SDK to obtain the data and returns the batch result
     *
     * @return array with the result of the batch request
     */
    protected function setBatchData(BatchRequests $requests): void {
    }
}
