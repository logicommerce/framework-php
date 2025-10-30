<?php

namespace FWK\Controllers\Blog;

use FWK\Core\Controllers\BaseXmlController;
use SDK\Core\Resources\BatchRequests;
use SDK\Services\Parameters\Groups\Blog\BlogPostParametersGroup;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\Resources\Loader;
use FWK\Enums\Services;

/**
 * This is the blog RSS controller
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @see BaseHtmlController
 *
 * @package FWK\Controllers\Blog
 */
class RssController extends BaseXmlController {

    private ?BlogPostParametersGroup $blogPostParametersGroup = null;

    private array $appliedParameters = [];

    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
        $this->blogPostParametersGroup = new BlogPostParametersGroup();
        $this->appliedParameters = Loader::service(Services::BLOG)->generateParametersGroupFromArray($this->blogPostParametersGroup, $this->getRequestParams());
        Loader::service(Services::BLOG)->addGetRss($requests, self::CONTROLLER_ITEM, $this->blogPostParametersGroup);
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FilterInputFactory::getBlogRssParameters();
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
