<?php

namespace FWK\Core\Dtos\Traits;

use FWK\Dtos\Documents\DocumentRowDiscount;
use FWK\Dtos\Documents\RichPrices\DocumentRowItem as RichPricesDocumentRowItem;

/**
 * This is the Basket Row Trait
 * 
 * @see DocumentRowProductTrait::getRichPrices()
 * @see DocumentRowProductTrait::setRichPrices()
 * 
 * @package FWK\Core\Dtos\Traits
 */
trait DocumentRowProductTrait {

    protected ?RichPricesDocumentRowItem $richPrices = null;

    /**
     * Returns the richPrices.
     *
     * @return RichPricesDocumentRowItem|NULL
     */
    public function getRichPrices(): ?RichPricesDocumentRowItem {
        return $this->richPrices;
    }

    /**
     * Set the richPrices.
     *
     * @param array|RichPricesDocumentRowItem $richPrices
     * @return void
     */
    public function setRichPrices(array|RichPricesDocumentRowItem $richPrices): void {
        if ($richPrices instanceof $richPrices) {
            $this->richPrices = $richPrices;
        } else {
            $this->richPrices = new RichPricesDocumentRowItem($richPrices);
        }
    }

    protected function setDiscounts(array $discounts): void {
        $this->discounts = $this->setArrayField($discounts, DocumentRowDiscount::class);
    }
}
