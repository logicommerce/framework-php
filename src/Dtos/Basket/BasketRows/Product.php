<?php

namespace FWK\Dtos\Basket\BasketRows;

use FWK\Core\Dtos\Traits\BasketRowTrait;
use SDK\Dtos\Basket\BasketRows\Product as SDKBasketRowsProduct;

/**
 * This is the basket product class.
 * The basket products information will be stored in that class and will remain immutable (only get methods are available)
 *
 * @see SDK\Dtos\Basket\BasketRows\Product
 * 
 * @uses FWK\Core\Dtos\Traits\BasketRowTrait
 *
 * @package FWK\Dtos\Basket\BasketRows
 */
class Product extends SDKBasketRowsProduct {
    use BasketRowTrait;
}
