<?php

namespace FWK\Dtos\Basket\AppliedDiscounts;

use FWK\Core\Dtos\Traits\FillFromParentTrait;
use FWK\Core\Dtos\Traits\TotalValueTrait;
use SDK\Dtos\Basket\AppliedDiscounts\AppliedDiscountAmountTotal as SDKAppliedDiscountAmountTotal;

/**
 * This is the applied discount amount class.
 * 
 * @see SDKAppliedDiscountAmountTotal
 * @see TotalValueTrait
 * @see FillFromParentTrait
 *
 * @package FWK\Dtos\Basket\AppliedDiscounts
 */
class AppliedDiscountAmountTotal extends SDKAppliedDiscountAmountTotal {
    use TotalValueTrait, FillFromParentTrait;
}
