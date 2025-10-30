<?php

namespace FWK\Dtos\Basket\AppliedDiscounts;

use FWK\Core\Dtos\Traits\FillFromParentTrait;
use FWK\Core\Dtos\Traits\TotalValueTrait;
use SDK\Dtos\Basket\AppliedDiscounts\AppliedDiscountRewardPoints as SDKAppliedDiscountRewardPoints;

/**
 * This is the applied discount amount class.
 * 
 * @see SDKAppliedDiscountRewardPoints
 * @see TotalValueTrait
 * @see FillFromParentTrait
 *
 * @package FWK\Dtos\Basket\AppliedDiscounts
 */
class AppliedDiscountRewardPoints extends SDKAppliedDiscountRewardPoints {
    use TotalValueTrait, FillFromParentTrait;
}
