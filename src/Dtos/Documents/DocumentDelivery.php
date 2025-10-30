<?php

namespace FWK\Dtos\Documents;

use SDK\Dtos\Documents\Transactions\Deliveries\DocumentDelivery as SDKDocumentDelivery;

/**
 * This is the DocumentDelivery class
 *
 * @see SDK\Dtos\Documents\Transactions\Deliveries\DocumentDelivery
 *
 * @package FWK\Dtos\Documents
 */
class DocumentDelivery extends SDKDocumentDelivery{
    
    protected function setShipments(array $shipments): void {
        $this->shipments = $this->setArrayField($shipments, DocumentShipment::class);
    }

}
