<?php

namespace FWK\Dtos\Basket\BasketRows;

use FWK\Core\Dtos\Traits\BasketRowTrait;
use SDK\Dtos\Basket\BasketRows\Bundle as SDKBasketRowsBundle;

/**
 * This is the basket item class.
 * The basket items information will be stored in that class and will remain immutable (only get methods are available)
 *
 * @see SDK\Dtos\Basket\BasketRows\Bundle
 * 
 * @uses FWK\Core\Dtos\Traits\BasketRowTrait
 *
 * @package SDK\Dtos\Basket
 */
class Bundle extends SDKBasketRowsBundle {
    use BasketRowTrait;
}
