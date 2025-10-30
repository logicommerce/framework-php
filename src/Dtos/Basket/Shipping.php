<?php

namespace FWK\Dtos\Basket;

use SDK\Dtos\Basket\Shipping as SDKShipping;

/**
 * This is the Shipping class
 *
 * @see Shipping::getSelected()
 * @see Shipping::setSelected()
 * 
 * @see SDK\Dtos\Basket\Shipping
 *
 * @package FWK\Dtos\Basket
 */
class Shipping extends SDKShipping{

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
