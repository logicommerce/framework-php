<?php

namespace FWK\Controllers;

use FWK\Core\Controllers\BaseXmlController;
use FWK\Core\Exceptions\CommerceException;
use SDK\Core\Resources\BatchRequests;
use FWK\Core\Resources\Loader;
use FWK\Enums\Services;
use FWK\Core\Resources\Response;
use SDK\Core\Resources\Server;

/**
 * This is the SitemapController class.
 * This class extends BaseXmlController, see this class.
 * 
 * @controllerData: self::XML_CONTENT: \SDK\Dtos\Common\DataFile
 * 
 * @RouteType: \SDK\Enums\RouteType::SITEMAP
 * 
 * @see BaseHtmlController
 *
 * @package FWK\Controllers
 */
class SitemapController extends BaseXmlController {

    public const XML_CONTENT = 'xmlContent';

    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
        Loader::service(Services::SITEMAP)->addGetSitemap($requests, self::CONTROLLER_ITEM);
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
        if (!is_null($data->getHttpStatus()) && $data->getHttpStatus()->getCode() === 202) {
            Response::addHeader(Server::get('SERVER_PROTOCOL') . ' 202 ' . $data->getHttpStatus()->getMessage());
        } else if (is_null($data)) {
            $this->breakControllerProcess('Missing data on the Service response', $onExceptionCode);
        } else if (!is_null($data->getError())) {
            $this->breakControllerProcess('Missing data on the Service response. ' . $data->getError()->getMessage(), $onExceptionCode);
        }
    }



    /**
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     */
    final protected function setControllerBaseData(): void {
        $this->setDataValue(self::XML_CONTENT, $this->getControllerData(self::CONTROLLER_ITEM)->getData());
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
