<?php

namespace FWK\Dtos\Basket;

use SDK\Dtos\Basket\VoucherGroup as SDKVoucherGroup;

/**
 * This is the VoucherGroup class
 *
 * @see SDK\Dtos\Basket\VoucherGroup
 * 
 * @package FWK\Dtos\Basket
 */
class VoucherGroup extends SDKVoucherGroup{

    protected function setDiscountCodes(array $discountCodes): void {
        $this->discountCodes = $this->setArrayField($discountCodes, DiscountCode::class);
    }

}
