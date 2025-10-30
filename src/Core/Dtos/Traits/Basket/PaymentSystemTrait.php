<?php

namespace FWK\Core\Dtos\Traits\Basket;

/**
 * This is the Payment System Trait
 * 
 * @see PaymentSystemTrait::getSelected()
 * @see PaymentSystemTrait::setSelected()
 * 
 * @package FWK\Core\Dtos\Traits
 */
trait PaymentSystemTrait {

    protected bool $selected = false;

    /**
     * Returns the selected
     *
     * @return bool
     */
    public function getSelected(): bool {
        return $this->selected;
    }

    /**
     * Sets the selected
     *
     * @param bool 
     */
    public function setSelected(bool $selected): void {
        $this->selected = $selected;
    }
}
