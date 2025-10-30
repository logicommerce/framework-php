<?php

namespace FWK\Dtos\Documents\DocumentRow;

use FWK\Core\Dtos\Traits\DocumentRowProductTrait;
use FWK\Core\Dtos\Traits\DocumentRowTrait;
use SDK\Dtos\Documents\Rows\TransactionDocumentRowVoucherPurchase as SDKTransactionDocumentRowVoucherPurchase;

/**
 * This is the Voucher Purchase row class.
 * the Voucher Purchase rows will be stored in that class and will remain immutable (only get methods are available)
 *
 * @uses DocumentRowTrait
 * @uses DocumentRowProductTrait
 *
 * @package FWK\Dtos\Documents
 */
class TransactionDocumentRowVoucherPurchase extends SDKTransactionDocumentRowVoucherPurchase {
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
