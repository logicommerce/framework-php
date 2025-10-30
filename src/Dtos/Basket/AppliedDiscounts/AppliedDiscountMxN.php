<?php

namespace FWK\Dtos\Basket\AppliedDiscounts;

use FWK\Core\Dtos\Traits\FillFromParentTrait;
use FWK\Core\Dtos\Traits\TotalValueTrait;
use SDK\Dtos\Basket\AppliedDiscounts\AppliedDiscountMxN as SDKAppliedDiscountMxN;

/**
 * This is the applied discount amount class.
 * 
 * @see SDKAppliedDiscountMxN
 * @see TotalValueTrait
 * @see FillFromParentTrait
 *
 * @package FWK\Dtos\Basket\AppliedDiscounts
 */
class AppliedDiscountMxN extends SDKAppliedDiscountMxN {
    use TotalValueTrait, FillFromParentTrait;
}
