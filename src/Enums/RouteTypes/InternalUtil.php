<?php

namespace FWK\Enums\RouteTypes;

use SDK\Core\Enums\Enum;

/**
 * This is the InternalUtil enumeration class.
 * This class declares util route type enumerations.
 * <br> This class extends SDK\Core\Enums\Enum, see this class.
 *
 * @abstract
 *
 * @see InternalUtil::DEMO
 * @see InternalUtil::HEALTHCHECK
 * @see InternalUtil::PREVIEWDOCUMENTTEMPLATE
 *
 * @see Enum
 *
 * @package FWK\Enums\RouteTypes
 */
abstract class InternalUtil extends Enum {

    public const DEMO = 'UTIL_INTERNAL_DEMO';

    public const HEALTHCHECK = 'UTIL_HEALTH_CHECK';

    public const PREVIEWDOCUMENTTEMPLATE = 'UTIL_INTERNAL_PREVIEW_DOCUMENT_TEMPLATE';
}
