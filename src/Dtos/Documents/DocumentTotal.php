<?php

namespace FWK\Dtos\Documents;

use FWK\Dtos\Documents\RichPrices\Totals as RichPricesTotals;
use SDK\Dtos\Documents\Transactions\DocumentTotal as SDKDocumentTotal;

/**
 * This is the DocumentTotal class
 *
 * @see DocumentTotal::getRichPrices()
 * @see DocumentTotal::setRichPrices()
 *
 * @package FWK\Dtos\Documents
 */
class DocumentTotal extends SDKDocumentTotal{
    
    protected ?RichPricesTotals $richPrices = null;

    /**
     * Returns the richPrices.
     *
     * @return null|RichPricesTotals
     */
    public function getRichPrices(): ?RichPricesTotals {
        return $this->richPrices;
    }

    /**
     * Set the richPrices.
     * 
     * @param array|RichPricesTotals $richPrices
     *
     */
    public function setRichPrices(array|RichPricesTotals $richPrices): void {
        if($richPrices instanceof $richPrices){
            $this->richPrices = $richPrices;
        }else{
            $this->richPrices = new RichPricesTotals($richPrices);
        }
    }

}
