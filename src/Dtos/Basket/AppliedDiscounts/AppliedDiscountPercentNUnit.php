<?php

namespace FWK\Dtos\Basket\AppliedDiscounts;

use FWK\Core\Dtos\Traits\FillFromParentTrait;
use FWK\Core\Dtos\Traits\TotalValueTrait;
use SDK\Dtos\Basket\AppliedDiscounts\AppliedDiscountPercentNUnit as SDKAppliedDiscountPercentNUnit;

/**
 * This is the applied discount amount class.
 * 
 * @see SDKAppliedDiscountPercentNUnit
 * @see TotalValueTrait
 * @see FillFromParentTrait
 *
 * @package FWK\Dtos\Basket\AppliedDiscounts
 */
class AppliedDiscountPercentNUnit extends SDKAppliedDiscountPercentNUnit {
    use TotalValueTrait, FillFromParentTrait;
}
