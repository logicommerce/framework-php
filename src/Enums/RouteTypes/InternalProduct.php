<?php

namespace FWK\Enums\RouteTypes;

use SDK\Core\Enums\Enum;

/**
 * This is the InternalProduct enumeration class.
 * This class declares enumerations for a product route type.
 * <br> This class extends SDK\Core\Enums\Enum, see this class.
 *
 * @see InternalProduct::ADD_COMMENT
 * @see InternalProduct::GET_BUNDLE_COMBINATION_DATA
 * @see InternalProduct::GET_PRODUCT_COMBINATION_DATA
 * @see InternalProduct::SET_CONTACT
 * @see InternalProduct::SET_RECOMMEND
 * @see InternalProduct::SUBSCRIBE_STOCK
 *
 * @see Enum
 *
 * @package FWK\Enums\RouteTypes
 */
class InternalProduct extends Enum {

    public const ADD_COMMENT = 'PRODUCT_INTERNAL_ADD_COMMENT';

    public const GET_BUNDLE_COMBINATION_DATA = 'PRODUCT_INTERNAL_GET_BUNDLE_COMBINATION_DATA';

    public const GET_PRODUCT_COMBINATION_DATA = 'PRODUCT_INTERNAL_GET_PRODUCT_COMBINATION_DATA';

    public const SET_CONTACT = 'PRODUCT_INTERNAL_SET_CONTACT';

    public const SET_RECOMMEND = 'PRODUCT_INTERNAL_SET_RECOMMEND';

    public const SUBSCRIBE_STOCK = 'PRODUCT_INTERNAL_SUBSCRIBE_STOCK';

    public const DISCOUNTS = 'PRODUCT_INTERNAL_DISCOUNTS';
}
