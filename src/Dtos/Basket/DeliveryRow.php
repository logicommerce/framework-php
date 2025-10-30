<?php

namespace FWK\Dtos\Basket;

use SDK\Dtos\Basket\DeliveryRow as SDKDeliveryRow;

/**
 * This is the DeliveryRow class
 *
 * @see SDK\Dtos\Basket\DeliveryRow
 * 
 * @package FWK\Dtos\Basket
 */
class DeliveryRow extends SDKDeliveryRow{

    protected function setBasketRowData(array $basketRowData): void {
        $this->basketRowData = new BasketRowData($basketRowData);
    }

}