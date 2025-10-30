<?php

namespace FWK\Controllers\Resources\Internal;

use FWK\Core\Controllers\BasePdfController;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Common\Route;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\Resources\Loader;
use FWK\Enums\Services;
use FWK\Enums\Parameters;
use FWK\Core\Resources\Utils;

/**
 * This is the Order pdf controller.
 * This class extends BasePdfController (FWK\Core\Controllers\BasePdfController), see this class.
 *
 * @see BasePdfController
 *
 * @package FWK\Controllers\Resources\Internal
 */
class OrderPdfController extends BasePdfController {

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->setContentDisposition(self::CONTENT_DISPOSITION_INLINE);
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FilterInputFactory::getTokenParameter() + FilterInputFactory::getIdParameter();
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
        Loader::service(Services::ORDER)->addGetPDFOrder($requests, self::CONTROLLER_ITEM, $this->getRequestParam(Parameters::ID, true), $this->getRequestParam(Parameters::TOKEN, false, ''));
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
        $this->setFileName('order_' . $this->getRequestParam(Parameters::ID));
        $this->setDataValue('pdfContent', Utils::getPdfContent($this->getControllerData(self::CONTROLLER_ITEM)));
    }
}
