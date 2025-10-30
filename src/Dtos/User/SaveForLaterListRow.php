<?php

namespace FWK\Dtos\User;

use FWK\Core\Dtos\Traits\FillFromParentTrait;
use SDK\Core\Dtos\Element;
use SDK\Dtos\User\SaveForLaterListRow as SDKSaveForLaterListRow;

/**
 * This is the SaveForLaterListRow class
 *
 * @see SaveForLaterListRow::getItem()
 * @see SaveForLaterListRow::setItem()
 *
 * @package FWK\Dtos\User
 */
class SaveForLaterListRow extends SDKSaveForLaterListRow {
    use FillFromParentTrait;

    protected ?Element $item = null;

    /**
     * Returns the item.
     *
     * @return Element|null
     */
    public function getItem(): ?Element {
        return $this->item;
    }

    /**
     * Set the item.
     * 
     * @param Element $item
     *
     */
    public function setItem(Element $item): void {
        $this->item = $item;
    }
}
