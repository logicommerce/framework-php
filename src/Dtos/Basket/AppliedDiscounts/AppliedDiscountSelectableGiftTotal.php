<?php

namespace FWK\Dtos\Basket\AppliedDiscounts;

use FWK\Core\Dtos\Traits\FillFromParentTrait;
use FWK\Core\Dtos\Traits\TotalValueTrait;
use SDK\Dtos\Basket\AppliedDiscounts\AppliedDiscountSelectableGiftTotal as SDKAppliedDiscountSelectableGiftTotal;

/**
 * This is the applied discount amount class.
 * 
 * @see SDKAppliedDiscountSelectableGiftTotal
 * @see TotalValueTrait
 * @see FillFromParentTrait
 *
 * @package FWK\Dtos\Basket\AppliedDiscounts
 */
class AppliedDiscountSelectableGiftTotal extends SDKAppliedDiscountSelectableGiftTotal {
    use TotalValueTrait, FillFromParentTrait;
}
