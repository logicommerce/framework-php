<?php

namespace FWK\Enums\RouteTypes;

use SDK\Core\Enums\Enum;

/**
 * This is the InternalBanner enumeration class.
 * This class declares enumerations for a banner route type.
 * <br> This class extends SDK\Core\Enums\Enum, see this class.
 *
 * @abstract
 *
 * @see InternalBanner::DONE_CLICK
 *
 * @see Enum
 * 
 * @package FWK\Enums\RouteTypes
 */
abstract class InternalBanner extends Enum {

    public const DONE_CLICK = 'BANNER_INTERNAL_DONE_CLICK';
}
