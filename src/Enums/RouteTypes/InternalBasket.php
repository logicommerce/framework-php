<?php

namespace FWK\Enums\RouteTypes;

use SDK\Core\Enums\Enum;

/**
 * This is the InternalBasket enumeration class.
 * This class declares enumerations for a basket route type.
 * <br> This class extends SDK\Core\Enums\Enum, see this class.
 *
 * @see InternalBasket::ADD_BUNDLE
 * @see InternalBasket::ADD_GIFT
 * @see InternalBasket::ADD_LINKED
 * @see InternalBasket::ADD_PRODUCT
 * @see InternalBasket::ADD_PRODUCTS
 * @see InternalBasket::ADD_VOUCHER
 * @see InternalBasket::ATTACHMENT
 * @see InternalBasket::DELETE_ROW
 * @see InternalBasket::DELETE_ROWS
 * @see InternalBasket::DELETE_SAVE_FOR_LATER_ROW
 * @see InternalBasket::DELETE_VOUCHER
 * @see InternalBasket::LOCKED_STOCK
 * @see InternalBasket::LOCKED_STOCK_RENEW
 * @see InternalBasket::MINI_BASKET
 * @see InternalBasket::REDEEM_REWARD_POINTS
 * @see InternalBasket::SAVE_FOR_LATER_ROW
 * @see InternalBasket::SET_DELIVERY
 * @see InternalBasket::SET_PAYMENT_SYSTEM
 * @see InternalBasket::TRANSFER_TO_BASKET_SAVE_FOR_LATER_ROW
 *
 * @see Enum
 *
 * @package FWK\Enums\RouteTypes
 */
class InternalBasket extends Enum {

    public const ADD_BUNDLE = 'BASKET_INTERNAL_ADD_BUNDLE';

    public const ADD_GIFT = 'BASKET_INTERNAL_ADD_GIFT';

    public const ADD_LINKED = 'BASKET_INTERNAL_ADD_LINKED';

    public const ADD_PRODUCT = 'BASKET_INTERNAL_ADD_PRODUCT';

    public const ADD_PRODUCTS = 'BASKET_INTERNAL_ADD_PRODUCTS';

    public const ADD_VOUCHER = 'BASKET_INTERNAL_ADD_VOUCHER';

    public const ATTACHMENT = 'BASKET_INTERNAL_ATTACHMENT';

    public const DELETE_ROW = 'BASKET_INTERNAL_DELETE_ROW';

    public const DELETE_ROWS = 'BASKET_INTERNAL_DELETE_ROWS';

    public const DELETE_SAVE_FOR_LATER_ROW = 'BASKET_INTERNAL_DELETE_SAVE_FOR_LATER_ROW';

    public const DELETE_VOUCHER = 'BASKET_INTERNAL_DELETE_VOUCHER';

    public const MINI_BASKET = 'BASKET_INTERNAL_MINI_BASKET';

    public const LOCKED_STOCK = 'BASKET_INTERNAL_LOCKED_STOCK';

    public const LOCKED_STOCK_RENEW = 'BASKET_INTERNAL_LOCKED_STOCK_RENEW';

    public const REDEEM_REWARD_POINTS = 'BASKET_INTERNAL_REDEEM_REWARD_POINTS';

    public const SAVE_FOR_LATER_ROW = 'BASKET_INTERNAL_SAVE_FOR_LATER_ROW';

    public const SET_DELIVERY = 'BASKET_INTERNAL_SET_DELIVERY';

    public const SET_PAYMENT_SYSTEM = 'BASKET_INTERNAL_SET_PAYMENT_SYSTEM';

    public const TRANSFER_TO_BASKET_SAVE_FOR_LATER_ROW = 'BASKET_INTERNAL_TRANSFER_TO_BASKET_SAVE_FOR_LATER_ROW';
}
