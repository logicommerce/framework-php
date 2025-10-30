<?php

namespace FWK\Dtos\Documents;

use SDK\Dtos\Documents\Transactions\Deliveries\ShippingDocumentDelivery as SDKShippingDocumentDelivery;

/**
 * This is the ShippingDocumentDelivery class
 *
 * @see SDK\Dtos\Documents\Transactions\Deliveries\ShippingDocumentDelivery
 *
 * @package FWK\Dtos\Documents
 */
class ShippingDocumentDelivery extends SDKShippingDocumentDelivery {

    protected function setShipments(array $shipments): void {
        $this->shipments = $this->setArrayField($shipments, DocumentShipment::class);
    }
}
