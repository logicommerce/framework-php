<?php

namespace FWK\Dtos\Documents;

use FWK\Dtos\Documents\RichPrices\Payment as RichPricesPayment;
use SDK\Dtos\Documents\Transactions\Purchases\DocumentPaymentSystem as SDKDocumentPaymentSystem;

/**
 * This is the DocumentPaymentSystem class
 *
 * @see DocumentPaymentSystem::getRichPrices()
 * @see DocumentPaymentSystem::setRichPrices()
 *
 * @see SDK\Dtos\Documents\Transactions\Purchases\DocumentPaymentSystem
 *
 * @package FWK\Dtos\Documents
 */
class DocumentPaymentSystem extends SDKDocumentPaymentSystem{
    
    protected ?RichPricesPayment $richPrices = null;

    /**
     * Returns the richPrices.
     *
     * @return null|RichPricesPayment
     */
    public function getRichPrices(): ?RichPricesPayment {
        return $this->richPrices;
    }

    /**
     * Set the richPrices.
     *
     * @param array|RichPricesPayment $richPrices
     *
     */
    public function setRichPrices(array|RichPricesPayment $richPrices): void {
        if($richPrices instanceof $richPrices){
            $this->richPrices = $richPrices;
        }else{
            $this->richPrices = new RichPricesPayment($richPrices);
        }
    }

}
