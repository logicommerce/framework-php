<?php

namespace FWK\Dtos\Basket;

use FWK\Core\Dtos\Traits\Basket\BasketDeliveryTrait;
use SDK\Dtos\Basket\BasketPickingDelivery as SDKBasketPickingDelivery;

/**
 * This is the delivery class.
 * The basket delivery information will be stored in that class and will remain immutable (only get methods are available)
 *
 * @see SDKBasketPickingDelivery
 * @uses BasketDeliveryTrait;
 *
 * @package FWK\Dtos\Basket
 */
class BasketPickingDelivery extends SDKBasketPickingDelivery {
    use BasketDeliveryTrait;
}
