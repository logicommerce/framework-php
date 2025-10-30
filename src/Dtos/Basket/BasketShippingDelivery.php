<?php

namespace FWK\Dtos\Basket;

use FWK\Core\Dtos\Traits\Basket\BasketDeliveryTrait;
use SDK\Dtos\Basket\BasketShippingDelivery as SDKBasketShippingDelivery;

/**
 * This is the BasketShippingDelivery class.
 * The basket Shipping Delivery information will be stored in that class and will remain immutable (only get methods are available)
 *
 * @see SDKBasketShippingDelivery
 * @uses BasketDeliveryTrait;
 * 
 * @package FWK\Dtos\Basket
 */
class BasketShippingDelivery extends SDKBasketShippingDelivery {
    use BasketDeliveryTrait;
}
