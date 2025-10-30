<?php

namespace FWK\Dtos\Basket\BasketRows;

use FWK\Core\Dtos\Traits\BasketRowTrait;
use SDK\Dtos\Basket\BasketRows\VoucherPurchase as SDKBasketRowsVoucherPurchase;

/**
 * This is the basket voucher purchase class.
 * The basket voucher purchase information will be stored in that class and will remain immutable (only get methods are available)
 *
  * 
 * @see SDK\Dtos\Basket\BasketRows\VoucherPurchase
 * @uses FWK\Core\Dtos\Traits\BasketRowTrait
 * 
 * @package FWK\Dtos\Basket\BasketRows
 */
class VoucherPurchase extends SDKBasketRowsVoucherPurchase {
    use BasketRowTrait;

}
