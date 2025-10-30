<?php

namespace FWK\Enums\RouteTypes;

use SDK\Core\Enums\Enum;

/**
 * This is the InternalProductComparison enumeration class.
 * This class declares enumerations for a comparison route type.
 * <br> This class extends SDK\Core\Enums\Enum, see this class.
 *
 * @abstract
 *
 * @see InternalProductComparison::BASKET
 *
 * @see Enum
 *
 * @package FWK\Enums\RouteTypes
 */
abstract class InternalProductComparison extends Enum {

    public const ADD_COMPARISON_PRODUCT = 'PRODUCT_COMPARISON_INTERNAL_ADD_COMPARISON_PRODUCT';

    public const DELETE_COMPARISON_PRODUCT = 'PRODUCT_COMPARISON_INTERNAL_DELETE_COMPARISON_PRODUCT';

    public const PRODUCT_COMPARISON_PREVIEW = 'PRODUCT_COMPARISON_INTERNAL_PRODUCT_COMPARISON_PREVIEW';

}
