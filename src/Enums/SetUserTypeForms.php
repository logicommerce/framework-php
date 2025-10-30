<?php

namespace FWK\Enums;

use SDK\Core\Enums\Enum;

/**
 * This is the SetUserTypeForms enumeration class.
 * This class declares SetUserTypeForms enumerations.
 * <br> This class extends SDK\Core\Enums\Enum, see this class.
 *
 * @abstract
 *
 * @see SetUserTypeForms::USER
 * @see SetUserTypeForms::BILLING
 * @see SetUserTypeForms::SHIPPING
 *
 * @see Enum
 *
 * @package FWK\Enums
 */
abstract class SetUserTypeForms extends Enum {

    public const USER = 'user';

    public const BILLING = 'billing';
    
    public const SHIPPING = 'shipping';

}

