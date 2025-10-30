<?php

namespace FWK\Dtos\Basket\BasketRows;

use FWK\Core\Dtos\Traits\BasketRowTrait;
use SDK\Dtos\Basket\BasketRows\Gift as SDKBasketRowsGift;

/**
 * This is the basket gift class.
 * The basket gifts information will be stored in that class and will remain immutable (only get methods are available)
 *
 * @see SDK\Dtos\Basket\BasketRows\Gift
 * 
 * @uses FWK\Core\Dtos\Traits\BasketRowTrait
 * 
 * @package FWK\Dtos\Basket\BasketRows
 */
class Gift extends SDKBasketRowsGift {
    use BasketRowTrait;
}
