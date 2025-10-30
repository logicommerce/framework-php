<?php

namespace FWK\Enums\RouteTypes;

use SDK\Core\Enums\Enum;

/**
 * This is the InternalPhysicalLocation enumeration class.
 * This class declares enumerations for a banner route type.
 * <br> This class extends SDK\Core\Enums\Enum, see this class.
 *
 * @abstract
 *
 * @see InternalPhysicalLocation::CITIES
 * @see InternalPhysicalLocation::STATES
 *
 * @see Enum
 * 
 * @package FWK\Enums\RouteTypes
 */
abstract class InternalPhysicalLocation extends Enum {

    public const CITIES = 'PHYSICAL_LOCATION_INTERNAL_CITIES';

    public const STATES = 'PHYSICAL_LOCATION_INTERNAL_STATES';
}
