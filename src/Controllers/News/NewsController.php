<?php

namespace FWK\Controllers\News;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\Resources\Loader;
use FWK\Enums\Services;
use FWK\Services\NewsService;
use SDK\Core\Resources\BatchRequests;

/**
 * This is the base news controller.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @controllerData: self::CONTROLLER_ITEM: \SDK\Core\Dtos\ElementCollection - \SDK\Dtos\Catalog\News
 * 
 * @twigContent: \themes\{{themeName}}\{{version}}\Content\News\default.html.twig
 * 
 * @RouteType: \SDK\Enums\RouteType::NEWS
 *
 * @package FWK\Controllers\News
 */
class NewsController extends BaseHtmlController {

    /**
     * This method is the one in charge of defining all the data batch requests that are basic for
     * the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
        /** @var NewsService $newsService */
        $newsService = Loader::service(Services::NEWS);
        $newsService->addGetNewsById($requests, self::CONTROLLER_ITEM, $this->getRoute()->getId());
    }

    /**
     * This method is the one in charge of defining all the data batch requests that are needed for
     * the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request where the method will add the batch requests.
     * @return void
     */
    protected function setBatchData(BatchRequests $request): void {
    }

    /**
     * This method runs after the batch requests (defined in the setBatchData methods) are resolved,
     * so here you can work with the response of the batch requests and calculate and set more
     * needed data.
     *
     * @param array $additionalData Set additional data to the controller data.
     * 
     * @return void
     */
    protected function setData(array $additionalData = []): void {
    }
}
