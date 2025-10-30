<?php

namespace FWK\Dtos\Catalog;

use FWK\Core\Dtos\Traits\RelatedItemsTrait;
use SDK\Dtos\Catalog\BundleGrouping as SDKBundleGrouping;

/**
 * This is the BundleGrouping container class.
 * 
 * @see BundleGrouping::getProducts()
 * @see BundleGrouping::setProducts()
 * 
 * @see RelatedItemsTrait
 *
 * @package FWK\Dtos\Catalog
 */
class BundleGrouping extends SDKBundleGrouping {
    use RelatedItemsTrait;

    protected array $products = [];

    /**
     * Returns the products.
     *
     * @return array
     */
    public function getProducts(): array {
        return $this->products;
    }

    /**
     * Set the products.
     * 
     * @param $products array
     *
     */
    public function setProducts(array $products): void {
        $this->products = $products;
    }
}
