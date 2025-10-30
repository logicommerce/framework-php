<?php

namespace FWK\Dtos\Basket;

use SDK\Dtos\Basket\Shipment as SDKShipment;

/**
 * This is the Shipment class
 *
 * @see SDK\Dtos\Basket\Shipment
 * 
 * @package FWK\Dtos\Basket
 */
class Shipment extends SDKShipment{

    protected function setShippings(array $shippings): void {
        $this->shippings = $this->setArrayField($shippings, Shipping::class);
    }

    protected function setRows(array $rows): void {
        $this->rows = $this->setArrayField($rows, ShipmentRow::class);
    }

}
