<?php

namespace FWK\Controllers\News;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\Controllers\FiltrableNewsListTrait;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Common\Route;

/**
 * This is the news list controller.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @controllerData: 
 *  <p>self::CONTROLLER_ITEM: \SDK\Core\Dtos\ElementCollection of \SDK\Dtos\Catalog\News</p>
 *  <p>self::NEWS_FILTER: array with the applied filters</p>
 * 
 * @twigContent: \themes\{{themeName}}\{{version}}\Content\NewsList\default.html.twig
 * 
 * @RouteType: \SDK\Enums\RouteType::NEWS_LIST
 * 
 * @uses FiltrableNewsListTrait
 *
 * @package FWK\Controllers\News
 */
class NewsListController extends BaseHtmlController {
    use FiltrableNewsListTrait;

    /**
     * Constructor method.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        $this->initFiltrableNewsList(self::getTheme()->getConfiguration()->getNews()->getNewsList());
        parent::__construct($route);
    }

    /**
     * This method initialize applied parameters, runs previously to run
     * preSendControllerBaseBatchData
     *
     */
    protected function initializeAppliedParameters(): void {
        $this->initializeFiltrableNewsAppliedParameters();
    }

    /**
     * This method is the one in charge of defining all the data batch requests that are basic for
     * the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
        $this->addGetNewsToBatchRequest($requests, self::CONTROLLER_ITEM);
    }

    /**
     * This method is in charge of defining the basic data necessary for the correct operation of
     * the controller.
     */
    protected function setControllerBaseData(): void {
        $this->setFiltrableNewsControllerBaseData(self::NEWS_FILTER);
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
     * @param array $additionalData Set additiona data to the controller data
     * 
     * @return void
     */
    protected function setData(array $additionalData = []): void {
    }
}
