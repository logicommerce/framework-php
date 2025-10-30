<?php

namespace FWK\Enums;

use SDK\Core\Enums\Enum;

/**
 * This is the ApplicableFilterOrderBy enumeration class.
 * This class declares enumerations for applicable filters sort.
 * <br> This class extends SDK\Core\Enums\Enum, see this class.
 *
 * @abstract
 *
 * @see ApplicableFilterOrderBy::SORT_ORDER_BY_NAME
 * @see ApplicableFilterOrderBy::SORT_ORDER_BY_POSITION 
 *
 * @see Enum
 *
 * @package FWK\Enums
 */
abstract class ApplicableFilterOrderBy extends Enum {

    public const SORT_ORDER_BY_NAME = 'name';

    public const SORT_ORDER_BY_POSITION = 'position';
}
