<?php

namespace FWK\Dtos\Basket;

use SDK\Dtos\Basket\ShipmentRow as SDKShipmentRow;

/**
 * This is the ShipmentRow class
 *
 * @see SDK\Dtos\Basket\ShipmentRow
 * 
 * @package FWK\Dtos\Basket
 */
class ShipmentRow extends SDKShipmentRow{

    protected function setBasketRowData(array $basketRowData): void {
        $this->basketRowData = new BasketRowData($basketRowData);
    }

}
