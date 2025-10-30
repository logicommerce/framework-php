<?php

namespace FWK\Dtos\User;

use FWK\Core\Dtos\Traits\FillFromParentTrait;
use SDK\Core\Dtos\Element;
use SDK\Dtos\User\ShoppingListRow as SDKShoppingListRow;

/**
 * This is the ShoppingListRow class
 *
 * @see ShoppingListRow::getItem()
 * @see ShoppingListRow::setItem()
 *
 * @package FWK\Dtos\User
 */
class ShoppingListRow extends SDKShoppingListRow {
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
