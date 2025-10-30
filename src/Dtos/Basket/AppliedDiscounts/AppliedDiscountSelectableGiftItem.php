<?php

namespace FWK\Dtos\Basket\AppliedDiscounts;

use FWK\Core\Dtos\Traits\FillFromParentTrait;
use FWK\Core\Dtos\Traits\TotalValueTrait;
use SDK\Dtos\Basket\AppliedDiscounts\AppliedDiscountSelectableGiftItem as SDKAppliedDiscountSelectableGiftItem;

/**
 * This is the applied discount amount class.
 * 
 * @see SDKAppliedDiscountSelectableGiftItem
 * @see TotalValueTrait
 * @see FillFromParentTrait
 *
 * @package FWK\Dtos\Basket\AppliedDiscounts
 */
class AppliedDiscountSelectableGiftItem extends SDKAppliedDiscountSelectableGiftItem {
    use TotalValueTrait, FillFromParentTrait;
}
