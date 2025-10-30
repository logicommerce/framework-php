<?php

namespace FWK\Dtos\Basket;

use SDK\Dtos\Basket\AppliedDiscount;
use SDK\Dtos\Basket\DiscountCode as SDKDiscountCode;

/**
 * This is the DiscountCode class
 *
 * @see DiscountCode::getAppliedDiscount()
 * @see DiscountCode::setAppliedDiscount()
 *
 * @see SDK\Dtos\Basket\DiscountCode
 * 
 * @package FWK\Dtos\Basket
 */
class DiscountCode extends SDKDiscountCode{

    protected ?AppliedDiscount $appliedDiscount = null;
    
    /**
     * Returns the appliedDiscount
     *
     * @return null|AppliedDiscount
     */
    public function getAppliedDiscount(): ?AppliedDiscount {
        return $this->appliedDiscount;
    }

    /**
     * Sets the appliedDiscount
     *
     * @param AppliedDiscount 
     */
    public function setAppliedDiscount(AppliedDiscount $appliedDiscount): void {
        $this->appliedDiscount = $appliedDiscount;
    }

}
