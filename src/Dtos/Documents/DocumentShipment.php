<?php

namespace FWK\Dtos\Documents;

use SDK\Dtos\Documents\Transactions\Deliveries\DocumentShipment as SDKDocumentShipment;

/**
 * This is the DocumentShipment class
 *
 * @see SDK\Dtos\Documents\Transactions\Deliveries\DocumentShipment
 *
 * @package FWK\Dtos\Documents
 */
class DocumentShipment extends SDKDocumentShipment{
    
    protected function setShipping(array $shipping): void {
        $this->shipping = new DocumentShipping($shipping);
    }

}
