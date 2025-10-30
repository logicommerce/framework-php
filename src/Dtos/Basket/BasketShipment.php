<?php

namespace FWK\Dtos\Basket;

use SDK\Dtos\Basket\BasketShipment as SDKBasketShipment;

/**
 * This is the BasketShipment class
 *
 * @see SDK\Dtos\Basket\BasketShipment
 * 
 * @package FWK\Dtos\Basket
 */
class BasketShipment extends SDKBasketShipment{

    protected function setShipping(array $shipping): void {
        $this->shipping = new Shipping($shipping);
    }

}