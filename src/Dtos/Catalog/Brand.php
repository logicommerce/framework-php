<?php

namespace FWK\Dtos\Catalog;

use FWK\Core\Dtos\Traits\RelatedItemsTrait;
use SDK\Core\Dtos\ElementCollection;
use SDK\Dtos\Catalog\Brand as SDKBrand;

/**
 * This is the Brand container class.
 * 
 * @see Brand::getProducts()
 * @see Brand::setProducts()
 * 
 * @see RelatedItemsTrait
 *
 * @package FWK\Dtos\Catalog
 */
class Brand extends SDKBrand {
    use RelatedItemsTrait;

    protected ?ElementCollection $products = null;

    /**
     * Returns the products.
     *
     * @return null|ElementCollection
     */
    public function getProducts(): ?ElementCollection {
        return $this->products;
    }

    /**
     * Set the products.
     * 
     * @param $products ElementCollection
     *
     */
    public function setProducts(ElementCollection $products): void {
        $this->products = $products;
    }
}
