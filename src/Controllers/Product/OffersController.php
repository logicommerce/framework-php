<?php

namespace FWK\Controllers\Product;

use FWK\Core\Controllers\BaseHtmlController;
use SDK\Core\Resources\BatchRequests;
use FWK\Core\Controllers\FiltrableProductListTrait;
use FWK\Core\Resources\Loader;
use FWK\Enums\Services;
use SDK\Dtos\Common\Route;

/**
 * This is the offers product controller class.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @see BaseHtmlController
 *
 * @package FWK\Controllers\Product
 */
class OffersController extends BaseHtmlController {

    use FiltrableProductListTrait;

    public const PAGE = 'page';

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        $this->initFiltrableProductList(self::CONTROLLER_ITEM, self::getTheme()->getConfiguration()->getOffers()->getProductList());
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
        $this->addGetProductsToBatchRequest($requests);
        if ($this->getRoute()->getId() > 0) {
            Loader::service(Services::PAGE)->addGetPageById($requests, self::PAGE, $this->getRoute()->getId());
        } else {
            $this->setDataValue(self::PAGE, null);
        }
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
