<?php

namespace FWK\Controllers\User\Internal;

use FWK\Core\Controllers\BasePdfController;
use FWK\Core\Exceptions\CommerceException;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Common\Route;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\Resources\Loader;
use FWK\Core\Resources\Response;
use FWK\Core\Resources\RoutePaths;
use FWK\Enums\Services;
use FWK\Enums\Parameters;
use FWK\Core\Resources\Utils;
use FWK\Enums\RouteType;

/**
 * This is the RMA Corrective Invoice pdf controller.
 * This class extends BasePdfController (FWK\Core\Controllers\BasePdfController), see this class.
 *
 * @see BasePdfController
 *
 * @package FWK\Controllers\User\Internal
 */
class RmaCorrectiveInvoicePdfController extends BasePdfController {

    protected bool $loggedInRequired = true;

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
        return FilterInputFactory::getIdParameter();
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
        Loader::service(Services::ORDER)->addGetRMACorrectiveInvoicePDF($requests, self::CONTROLLER_ITEM, $this->getRequestParam(Parameters::ID, true));
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
        $this->setFileName('RmaCorrectiveInvoice_' . $this->getRequestParam(Parameters::ID));
        $this->setDataValue('pdfContent', Utils::getPdfContent($this->getControllerData(self::CONTROLLER_ITEM)));
    }

    /**
     * This method checks if the data required for the correct run of the controller has a correct value, in opposite, it breaks the execution of the controller
     *
     * @param Object $data
     *            is the data required for the correct run of the controller
     * @param string $onExceptionCode
     *            is the Exception to be thrown if the check is ko.
     *            
     * @return void
     */
    protected function checkCriticalServiceLoaded(?Object $data, string $onExceptionCode = CommerceException::CONTROLLER_UNDEFINED_CRITICAL_DATA): void {
        if (!is_null($data->getError()) && $data->getError()->getStatus() === 403) {
            Response::redirect(RoutePaths::getPath(RouteType::USER));
        } else {
            parent::checkCriticalServiceLoaded($data, $onExceptionCode);
        }
    }
}
