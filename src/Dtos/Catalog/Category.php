<?php

namespace FWK\Dtos\Catalog;

use FWK\Core\Dtos\Traits\RelatedItemsTrait;
use SDK\Core\Dtos\ElementCollection;
use SDK\Dtos\Catalog\Category as SDKCategory;

/**
 * This is the Category container class.
 * 
 * @see Category::getProducts()
 * @see Category::setProducts()
 * 
 * @see RelatedItemsTrait
 *
 * @package FWK\Dtos\Catalog
 */
class Category extends SDKCategory {
    use RelatedItemsTrait;

    protected ?ElementCollection $products = null;

    protected ?ElementCollection $subcategories = null;

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

    /**
     * Returns the subcategories.
     *
     * @return null|ElementCollection
     */
    public function getSubcategories(): ?ElementCollection {
        return $this->subcategories;
    }

    /**
     * Set the subcategories.
     * 
     * @param $subcategories ElementCollection
     *
     */
    public function setSubcategories(ElementCollection $subcategories): void {
        $this->subcategories = $subcategories;
    }
}
