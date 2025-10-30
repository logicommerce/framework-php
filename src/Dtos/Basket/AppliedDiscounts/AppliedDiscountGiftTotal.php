<?php

namespace FWK\Dtos\Basket\AppliedDiscounts;

use FWK\Core\Dtos\Traits\FillFromParentTrait;
use FWK\Core\Dtos\Traits\TotalValueTrait;
use SDK\Dtos\Basket\AppliedDiscounts\AppliedDiscountGiftTotal as SDKAppliedDiscountGiftTotal;

/**
 * This is the applied discount amount class.
 * 
 * @see SDKAppliedDiscountGiftTotal
 * @see TotalValueTrait
 * @see FillFromParentTrait
 *
 * @package FWK\Dtos\Basket\AppliedDiscounts
 */
class AppliedDiscountGiftTotal extends SDKAppliedDiscountGiftTotal {
    use TotalValueTrait, FillFromParentTrait;
}
