<?php

namespace FWK\Dtos\Basket;

use FWK\Core\Dtos\Traits\Basket\PaymentSystemTrait;
use SDK\Dtos\Basket\PaymentSystem as SDKPaymentSystem;

/**
 * This is the PaymentSystem class
 *
 * @see PaymentSystem::getSelected()
 * @see PaymentSystem::setSelected()
 * 
 * @see SDK\Dtos\Basket\PaymentSystem
 *
 * @package FWK\Dtos\Basket
 */

class PaymentSystem extends SDKPaymentSystem {

    use PaymentSystemTrait;
}
