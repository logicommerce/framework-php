<?php

namespace FWK\Dtos\Documents;

use FWK\Dtos\Documents\RichPrices\Shipping as RichPricesShipping;
use SDK\Dtos\Documents\Transactions\Deliveries\DocumentShipping as SDKDocumentShipping;

/**
 * This is the DocumentShipping class
 *
 * @see DocumentShipping::getRichPrices()
 * @see DocumentShipping::setRichPrices()
 *
 * @package FWK\Dtos\Documents
 */
class DocumentShipping extends SDKDocumentShipping{
    
    protected ?RichPricesShipping $richPrices = null;

    /**
     * Returns the richPrices.
     *
     * @return null|RichPricesShipping
     */
    public function getRichPrices(): ?RichPricesShipping {
        return $this->richPrices;
    }

    /**
     * Set the richPrices.
     * 
     * @param array|RichPricesShipping $richPrices
     *
     */
    public function setRichPrices(array|RichPricesShipping $richPrices): void {
        if($richPrices instanceof $richPrices){
            $this->richPrices = $richPrices;
        }else{
            $this->richPrices = new RichPricesShipping($richPrices);
        }
    }

    protected function setDiscounts(array $discounts): void {
        $this->discounts = $this->setArrayField($discounts, DocumentRowDiscount::class);
    }

}
