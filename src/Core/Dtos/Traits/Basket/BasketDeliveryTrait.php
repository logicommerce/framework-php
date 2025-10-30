<?php

namespace FWK\Core\Dtos\Traits\Basket;

use FWK\Core\Dtos\Factories\ShipmentFactory;
use FWK\Dtos\Basket\DeliveryRow;

/**
 * This is the BasketDelivery trait
 *
 * @see BasketDelivery::getOutputWarnings()
 * @see BasketDelivery::setOutputWarnings()
 * 
 * @package FWK\Core\Dtos\Traits
 */
trait BasketDeliveryTrait {

    protected array $outputWarnings = [];

    /**
     * Returns the outputWarnings
     *
     * @return array
     */
    public function getOutputWarnings(): array {
        return $this->outputWarnings;
    }

    /**
     * Sets the outputWarnings
     *
     * @param array 
     */
    public function setOutputWarnings(array $outputWarnings): void {
        $this->outputWarnings = $outputWarnings;
    }

    protected function setShipments(array $shipments): void {
        $this->shipments = $this->setArrayField($shipments, ShipmentFactory::class);
    }

    protected function setDeliveryRows(array $deliveryRows): void {
        $this->deliveryRows = $this->setArrayField($deliveryRows, DeliveryRow::class);
    }
}
