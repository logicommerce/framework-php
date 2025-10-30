<?php

namespace FWK\Dtos\Basket;

use FWK\Core\Dtos\Factories\BasketRowFactory;
use FWK\Core\Dtos\Traits\FillFromParentTrait;
use SDK\Core\Dtos\Factories\BasketDeliveryFactory;
use SDK\Dtos\Basket\Basket as SDKBasket;

/**
 * This is the Basket class
 *
 * @see SDK\Dtos\Basket\Basket
 * 
 * @package FWK\Dtos\Basket
 */
class Basket extends SDKBasket {
    use FillFromParentTrait;

    protected function setDelivery(array $delivery): void {
        $this->delivery = BasketDeliveryFactory::getElement($delivery);
    }

    protected function setItems(array $items): void {
        $this->items = $this->setArrayField($items, BasketRowFactory::class);
    }

    protected function setVouchers(array $vouchers): void {
        $this->vouchers = new VoucherGroup($vouchers);
    }
}
