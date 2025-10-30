<?php

namespace FWK\Dtos\Basket\BasketRows;

use FWK\Core\Dtos\Traits\BasketRowTrait;
use FWK\Dtos\Basket\BasketRows\Product as FWKBasketRowsProduct;
use SDK\Dtos\Basket\BasketRows\Linked as SDKBasketRowsLinked;

/**
 * This is the basket linked class.
 * The basket linkeds information will be stored in that class and will remain immutable (only get methods are available)
 *
 * @see Linked::getParentItem()
 * @see Linked::setParentItem()
 * 
 * @see SDK\Dtos\Basket\BasketRows\Linked
 * 
 * @uses FWK\Core\Dtos\Traits\BasketRowTrait
 * 
 * @package FWK\Dtos\Basket\BasketRows
 */
class Linked extends SDKBasketRowsLinked {
    use BasketRowTrait;

    protected ?FWKBasketRowsProduct $parentItem = null;

    /**
     * Returns the parentItem.
     *
     * @return FWKBasketRowsProduct|null
     */
    public function getParentItem(): ?FWKBasketRowsProduct {
        return $this->parentItem;
    }

    /**
     * Set the parentItem.
     * 
     * @param FWKBasketRowsProduct $parentItem
     *
     */
    public function setParentItem(array|FWKBasketRowsProduct $parentItem): void {
        if (is_array($parentItem)) {
            $this->parentItem = new FWKBasketRowsProduct($parentItem);
        } else {
            $this->parentItem = $parentItem;
        }
    }
}
