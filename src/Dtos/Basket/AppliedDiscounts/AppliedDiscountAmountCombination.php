<?php

namespace FWK\Dtos\Basket\AppliedDiscounts;

use FWK\Core\Dtos\Traits\FillFromParentTrait;
use FWK\Core\Dtos\Traits\TotalValueTrait;
use SDK\Dtos\Basket\AppliedDiscounts\AppliedDiscountAmountCombination as SDKAppliedDiscountAmountCombination;

/**
 * This is the applied discount amount class.
 * 
 * @see SDKAppliedDiscountAmountCombination
 * @see TotalValueTrait
 * @see FillFromParentTrait
 *
 * @package FWK\Dtos\Basket\AppliedDiscounts
 */
class AppliedDiscountAmountCombination extends SDKAppliedDiscountAmountCombination {
    use TotalValueTrait, FillFromParentTrait;
}
