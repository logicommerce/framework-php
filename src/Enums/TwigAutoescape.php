<?php

namespace FWK\Enums;

use SDK\Core\Enums\Enum;

/**
 * This is the TwigAutoescape enumeration class.
 * This class declares enumerations for the Twig autoescape configuration option.
 * <br> This class extends SDK\Core\Enums\Enum, see this class.
 * 
 * @abstract
 * 
 * @see TwigAutoescape::AUTOESCAPE_NAME 
 * @see TwigAutoescape::AUTOESCAPE_HTML 
 * @see TwigAutoescape::AUTOESCAPE_JS 
 * @see TwigAutoescape::AUTOESCAPE_CSS 
 * @see TwigAutoescape::AUTOESCAPE_URL 
 * @see TwigAutoescape::AUTOESCAPE_HTML_ATTR 
 * @see TwigAutoescape::AUTOESCAPE_NEW_STRATEGY 
 * 
 * @see Enum
 * 
 * @package FWK\Enums
 */
abstract class TwigAutoescape extends Enum {
    
    public const AUTOESCAPE_NAME = 1;
    
    public const AUTOESCAPE_HTML = 2;
    
    public const AUTOESCAPE_JS = 3;
    
    public const AUTOESCAPE_CSS = 4;
    
    public const AUTOESCAPE_URL = 5;
    
    public const AUTOESCAPE_HTML_ATTR = 6;
    
    public const AUTOESCAPE_NEW_STRATEGY = 7;
    
}
