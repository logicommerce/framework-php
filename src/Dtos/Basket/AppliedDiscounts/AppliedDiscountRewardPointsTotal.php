<?php

namespace FWK\Dtos\Basket\AppliedDiscounts;

use FWK\Core\Dtos\Traits\FillFromParentTrait;
use FWK\Core\Dtos\Traits\TotalValueTrait;
use SDK\Dtos\Basket\AppliedDiscounts\AppliedDiscountRewardPointsTotal as SDKAppliedDiscountRewardPointsTotal;

/**
 * This is the applied discount amount class.
 * 
 * @see SDKAppliedDiscountRewardPointsTotal
 * @see TotalValueTrait
 * @see FillFromParentTrait
 *
 * @package FWK\Dtos\Basket\AppliedDiscounts
 */
class AppliedDiscountRewardPointsTotal extends SDKAppliedDiscountRewardPointsTotal {
    use TotalValueTrait, FillFromParentTrait;
}
