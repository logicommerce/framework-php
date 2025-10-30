<?php

namespace FWK\Dtos\Documents\DocumentRow;

use FWK\Core\Dtos\Traits\DocumentRowProductTrait;
use FWK\Core\Dtos\Traits\DocumentRowTrait;
use SDK\Dtos\Documents\Rows\TransactionDocumentRowProduct as SDKTransactionDocumentRowProduct;


/**
 * This is the product row class.
 * the product rows will be stored in that class and will remain immutable (only get methods are available)
 *
 * @uses DocumentRowTrait
 * @uses DocumentRowProductTrait
 *
 * @package FWK\Dtos\Documents
 */
class TransactionDocumentRowProduct extends SDKTransactionDocumentRowProduct {
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
