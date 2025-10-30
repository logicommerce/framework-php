<?php

namespace FWK\Dtos\Documents;

use FWK\Dtos\Documents\RichPrices\Discount as RichPricesDiscount;
use SDK\Dtos\Documents\Transactions\DocumentDiscount as SDKDocumentDiscount;

/**
 * This is the DocumentRowDiscount class
 *
 * @see DocumentRowDiscount::getShow()
 * @see DocumentRowDiscount::setShow()
 *
 * @see SDK\Dtos\Documents\Transactions\DocumentDiscount
 *
 * @package FWK\Dtos\Documents
 */
class DocumentRowDiscount extends SDKDocumentDiscount{
    
    protected ?RichPricesDiscount $richPrices = null;

    /**
     * Returns the richPrices.
     *
     * @return null|RichPricesDiscount
     */
    public function getRichPrices(): ?RichPricesDiscount {
        return $this->richPrices;
    }

    /**
     * Set the richPrices.
     *
     * @param array|RichPricesShipping $richPrices
     *
     */
    public function setRichPrices(array|RichPricesDiscount $richPrices): void {
        if($richPrices instanceof $richPrices){
            $this->richPrices = $richPrices;
        }else{
            $this->richPrices = new RichPricesDiscount($richPrices);
        }
    }

}
