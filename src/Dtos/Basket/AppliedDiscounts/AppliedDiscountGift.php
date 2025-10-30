<?php

namespace FWK\Dtos\Basket\AppliedDiscounts;

use FWK\Core\Dtos\Traits\FillFromParentTrait;
use FWK\Core\Dtos\Traits\TotalValueTrait;
use SDK\Dtos\Basket\AppliedDiscounts\AppliedDiscountGift as SDKAppliedDiscountGift;

/**
 * This is the applied discount amount class.
 * 
 * @see SDKAppliedDiscountGift
 * @see TotalValueTrait
 * @see FillFromParentTrait
 *
 * @package FWK\Dtos\Basket\AppliedDiscounts
 */
class AppliedDiscountGift extends SDKAppliedDiscountGift {
    use TotalValueTrait, FillFromParentTrait;
}
