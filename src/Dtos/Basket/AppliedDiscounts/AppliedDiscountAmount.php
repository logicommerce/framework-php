<?php

namespace FWK\Dtos\Basket\AppliedDiscounts;

use FWK\Core\Dtos\Traits\FillFromParentTrait;
use FWK\Core\Dtos\Traits\TotalValueTrait;
use SDK\Dtos\Basket\AppliedDiscounts\AppliedDiscountAmount as SDKAppliedDiscountAmount;

/**
 * This is the applied discount amount class.
 * 
 * @see SDKAppliedDiscountAmount
 * @see TotalValueTrait
 * @see FillFromParentTrait
 *
 * @package FWK\Dtos\Basket\AppliedDiscounts
 */
class AppliedDiscountAmount extends SDKAppliedDiscountAmount {
    use TotalValueTrait, FillFromParentTrait;
}
