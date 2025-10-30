<?php

namespace FWK\Dtos\Documents\DocumentRow;

use FWK\Core\Dtos\Traits\DocumentRowProductTrait;
use FWK\Core\Dtos\Traits\DocumentRowTrait;
use SDK\Dtos\Documents\Rows\TransactionDocumentRowGift as SDKTransactionDocumentRowGift;

/**
 * This is the gift row class.
 * the gift rows will be stored in that class and will remain immutable (only get methods are available)
 *
 * @see Product
 *
 * @package FWK\Dtos\Documents
 */
class TransactionDocumentRowGift extends SDKTransactionDocumentRowGift {
    use DocumentRowTrait, DocumentRowProductTrait;

    /**
     * Set the className.
     * 
     * @param string $className
     *
     */
    public function setClassName(string $className): void {
        $this->className = $className;
    }
}
