<?php

namespace FWK\Enums;

use SDK\Core\Enums\Enum;

/**
 * This is the TcDataItems enumeration class.
 * This class declares enumerations related to theme configurations.
 * <br> This class extends SDK\Core\Enums\Enum, see this class.
 *
 * @abstract
 * 
 * @see TcDataItems::FORBIDDEN_ROUTE_TYPE
 * @see TcDataItems::FORBIDDEN_STATUS
 * @see TcDataItems::EVENTS
 * @see TcDataItems::POPUP
 * @see TcDataItems::GOOGLE_API_KEY
 * @see TcDataItems::SELECT_COUNTRY
 *  
 * @see Enum
 * 
 * @package FWK\Enums
 */
abstract class TcDataItems extends Enum {

    public const FORBIDDEN_ROUTE_TYPE = 'routeType';
    
    public const FORBIDDEN_STATUS = 'status';
    
    public const EVENTS = 'events';
    
    public const POPUP = 'popup';
    
    public const SELECT_COUNTRY = 'selectCountry';
    
    public const GOOGLE_API_KEY = 'googleApiKey';
    
}
