<?php

namespace FWK\Enums;

use SDK\Core\Enums\Enum;

/**
 * This is the TwigContentTypes enumeration class.
 * This class declares TwigContentTypes enumerations.
 * <br> This class extends SDK\Core\Enums\Enum, see this class.
 *
 * @abstract
 *
 * @see TwigContentTypes::HTML
 * @see TwigContentTypes::JS
 * @see TwigContentTypes::JSON
 * @see TwigContentTypes::JSONP
 * @see TwigContentTypes::PDF
 * @see TwigContentTypes::XML
 * @see TwigContentTypes::CORE
 *
 * @see Enum
 *
 * @package FWK\Enums
 */
abstract class TwigContentTypes extends Enum {

    public const HTML = 'html';

    public const JS = 'js';

    public const JSON = 'json';

    public const JSONP = 'jsonp';

    public const PDF = 'pdf';

    public const XML = 'xml';

    public const CORE = 'core';
}
