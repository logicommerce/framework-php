<?php

namespace FWK\Dtos\Basket\AppliedDiscounts;

use FWK\Core\Dtos\Traits\FillFromParentTrait;
use FWK\Core\Dtos\Traits\TotalValueTrait;
use SDK\Dtos\Basket\AppliedDiscounts\AppliedDiscountUnit as SDKAppliedDiscountUnit;

/**
 * This is the applied discount amount class.
 * 
 * @see SDKAppliedDiscountUnit
 * @see TotalValueTrait
 * @see FillFromParentTrait
 *
 * @package FWK\Dtos\Basket\AppliedDiscounts
 */
class AppliedDiscountUnit extends SDKAppliedDiscountUnit {
    use TotalValueTrait, FillFromParentTrait;
}
