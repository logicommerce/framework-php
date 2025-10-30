<?php

namespace FWK\Enums\RouteTypes;

use SDK\Core\Enums\Enum;

/**
 * This is the InternalGeolocation enumeration class.
 * This class declares enumerations for a geolocation route type.
 * <br> This class extends SDK\Core\Enums\Enum, see this class.
 *
 * @abstract
 *
 * @see InternalGeolocation::GET_COUNTRIES
 * @see InternalGeolocation::GET_LOCATIONS
 * @see InternalGeolocation::GET_LOCATIONS_PATH
 * @see InternalGeolocation::GET_LOCATIONS_LOCALITIES
 *
 * @see Enum
 *
 * @package FWK\Enums\RouteTypes
 */
abstract class InternalGeolocation extends Enum {

    public const GET_COUNTRIES = 'GEOLOCATION_INTERNAL_GET_COUNTRIES';

    public const GET_LOCATIONS = 'GEOLOCATION_INTERNAL_GET_LOCATIONS';
    
    public const GET_LOCATIONS_PATH = 'GEOLOCATION_INTERNAL_GET_LOCATIONS_PATH';
    
    public const GET_LOCATIONS_LOCALITIES = 'GEOLOCATION_INTERNAL_GET_LOCATIONS_LOCALITIES';
    
}
