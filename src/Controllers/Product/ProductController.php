<?php

namespace FWK\Controllers\Product;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\Resources\Loader;
use FWK\Enums\Services;
use SDK\Core\Resources\BatchRequests;
use FWK\Core\Resources\SeoItems;
use FWK\Core\Resources\Metatag;
use FWK\Core\Resources\Utils;
use SDK\Dtos\Common\Route;
use SDK\Enums\OptionsPriceMode;
use SDK\Services\Parameters\Groups\Product\ProductParametersGroup;

/**
 * This is the base product controller class.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @see BaseHtmlController
 *
 * @package FWK\Controllers\Product
 */
class ProductController extends BaseHtmlController {

    protected ProductParametersGroup $productParametersGroup;

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->productParametersGroup = new ProductParametersGroup();
    }

    /**
     * This method initialize applied parameters, runs previously to run preSendControllerBaseBatchData
     *
     */
    protected function initializeAppliedParameters(): void {
        $this->productParametersGroup->setOptionsPriceMode(OptionsPriceMode::CHEAPEST);
    }

    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
        Loader::service(Services::PRODUCT)->addGetProductById($requests, self::CONTROLLER_ITEM, $this->getRoute()->getId(), $this->productParametersGroup);
    }

    /**
     * This method is the one in charge of defining all the data batch requests that
     * are needed for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     * @return void
     */
    protected function setBatchData(BatchRequests $requests): void {
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
     *
     * @see \FWK\Core\Controllers\Controller::getSeoItems()
     */
    protected function getSeoItems(): ?SeoItems {
        $product = $this->getControllerData(self::CONTROLLER_ITEM);
        $seoItems = parent::getSeoItems();
        $seoItems->addMetatag((new Metatag())->setName(SeoItems::OG_TITLE)->setProperty(SeoItems::OG_TITLE)->setContent(Utils::cleanHtmlTags($product->getLanguage()->getName())));
        $seoItems->addMetatag((new Metatag())->setName(SeoItems::OG_URL)->setProperty(SeoItems::OG_URL)->setContent($product->getLanguage()->getUrlSeo()));
        if (strlen($product->getMainImages()->getMediumImage())) {
            $seoItems->addMetatag((new Metatag())->setName(SeoItems::OG_IMAGE)->setProperty(SeoItems::OG_IMAGE)->setContent($product->getMainImages()->getMediumImage()));
        }
        $seoItems->addMetatag((new Metatag())->setName(SeoItems::OG_URL)->setProperty(SeoItems::OG_URL)->setContent($product->getLanguage()->getUrlSeo()));
        $prodDesc = Utils::cleanHtmlTags($product->getLanguage()->getShortDescription());
        if (strlen($prodDesc) <= 5) {
            $prodDesc = Utils::cleanHtmlTags($product->getLanguage()->getLongDescription());
        } else {
            $prodDesc = $seoItems->getMetatag(SeoItems::DESCRIPTION)->getContent();
        }
        if (strlen($prodDesc) > 250) {
            $prodDesc = substr($prodDesc, 0, 250) . '...';
        }
        $seoItems->addMetatag((new Metatag())->setName(SeoItems::OG_DESCRIPTION)->setProperty(SeoItems::OG_DESCRIPTION)->setContent($prodDesc));
        return $seoItems;
    }
}
