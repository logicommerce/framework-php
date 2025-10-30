<?php

namespace FWK\Dtos\Documents\DocumentRow;

use FWK\Core\Dtos\Traits\DocumentRowProductTrait;
use FWK\Core\Dtos\Traits\DocumentRowTrait;
use SDK\Dtos\Documents\Rows\TransactionDocumentRowLinked as SDKTransactionDocumentRowLinked;

/**
 * This is the Linked row class.
 * the Linked rows will be stored in that class and will remain immutable (only get methods are available)
 *
 * @uses DocumentRowTrait
 * @uses DocumentRowProductTrait
 *
 * @package FWK\Dtos\Documents
 */
class TransactionDocumentRowLinked extends SDKTransactionDocumentRowLinked {
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
