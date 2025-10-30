<?php

namespace FWK\Dtos\Documents;

use SDK\Dtos\Documents\Transactions\Deliveries\PickingDocumentDelivery as SDKPickingDocumentDelivery;

/**
 * This is the PickingDocumentDelivery class
 *
 * @see SDK\Dtos\Documents\Transactions\Deliveries\PickingDocumentDelivery
 *
 * @package FWK\Dtos\Documents
 */
class PickingDocumentDelivery extends SDKPickingDocumentDelivery {

    protected function setShipments(array $shipments): void {
        $this->shipments = $this->setArrayField($shipments, DocumentShipment::class);
    }
}
