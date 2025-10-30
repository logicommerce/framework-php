<?php

namespace FWK\Dtos\Basket\AppliedDiscounts;

use FWK\Core\Dtos\Traits\FillFromParentTrait;
use FWK\Core\Dtos\Traits\TotalValueTrait;
use SDK\Dtos\Basket\AppliedDiscounts\AppliedDiscountSelectableGift as SDKAppliedDiscountSelectableGift;

/**
 * This is the applied discount amount class.
 * 
 * @see SDKAppliedDiscountSelectableGift
 * @see TotalValueTrait
 * @see FillFromParentTrait
 *
 * @package FWK\Dtos\Basket\AppliedDiscounts
 */
class AppliedDiscountSelectableGift extends SDKAppliedDiscountSelectableGift {
    use TotalValueTrait, FillFromParentTrait;
}
