<?php

namespace FWK\Dtos\Documents\DocumentRow;

use FWK\Core\Dtos\Traits\DocumentRowTrait;
use SDK\Dtos\Documents\Rows\TransactionDocumentRowBundle as SDKTransactionDocumentRowBundle;
use FWK\Dtos\Documents\RichPrices\DocumentRowBundle as RichPricesDocumentRowBundle;

/**
 * This is the document row class.
 * The document rows will be stored in that class and will remain immutable (only get methods are available)
 *
 * @see Bundle::getRichPrices()
 * @see Bundle::setRichPrices()
 *
 * @see SDK\Dtos\Documents\Rows\TransactionDocumentRowBundle
 * @see FWK\Core\Dtos\Traits\DocumentRowTrait
 *
 * @package FWK\Dtos\Documents
 */
class TransactionDocumentRowBundle extends SDKTransactionDocumentRowBundle {
    use DocumentRowTrait;

    protected ?RichPricesDocumentRowBundle $richPrices = null;

    /**
     * Returns the richPrices.
     *
     * @return RichPricesDocumentRowBundle
     */
    public function getRichPrices(): RichPricesDocumentRowBundle {
        return $this->richPrices;
    }

    /**
     * Set the richPrices.
     * 
     * @param array|RichPricesDocumentRowBundle $richPrices
     *
     */
    public function setRichPrices(array|RichPricesDocumentRowBundle $richPrices): void {
        if ($richPrices instanceof $richPrices) {
            $this->richPrices = $richPrices;
        } else {
            $this->richPrices = new RichPricesDocumentRowBundle($richPrices);
        }
    }

    protected function setItems(array $items): void {
        $this->items = $this->setArrayField($items, TransactionDocumentRowProduct::class);
    }
}
