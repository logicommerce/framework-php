<?php

namespace FWK\Controllers\Checkout;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Enums\Parameters;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Common\Route;
use SDK\Core\Dtos\ErrorField;
use SDK\Core\Dtos\ErrorFields;

/**
 * This is the checkout denied order controller.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @see BaseHtmlController
 *
 * @package FWK\Controllers\Checkout
 */
class DeniedOrderController extends BaseHtmlController {

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FilterInputFactory::getDeniedOrderParameters();
    }

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
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
    protected function setControllerBaseData(): void {
        $fields = $this->getRequestParam(Parameters::FIELDS, false, '');
        $errorFieldsArray = [];
        $arrayFields = json_decode($fields, true);
        if (is_array($arrayFields)) {
            foreach ($arrayFields as $field) {
                $newErrorField =  new ErrorField([
                    'name' => $field["name"],
                    'type' => $field["type"],
                    'additionalInformation' => $field["additionalInformation"]
                ]);
                array_push($errorFieldsArray, $newErrorField);
            }
        }
        $errorFields = new ErrorFields(['items' => $errorFieldsArray]);
        $this->setDataValue("errorFields", $errorFields);
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
