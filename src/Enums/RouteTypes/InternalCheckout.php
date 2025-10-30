<?php

namespace FWK\Enums\RouteTypes;

use SDK\Core\Enums\Enum;

/**
 * This is the InternalCheckout enumeration class.
 * This class declares enumerations for a checkout route type.
 * <br> This class extends SDK\Core\Enums\Enum, see this class.
 *
 * @see InternalCheckout::ADD_CUSTOMER
 * @see InternalCheckout::CLEAR_BASKET
 * @see InternalCheckout::CONTINUE_SHOPPING
 * @see InternalCheckout::NEXT_STEP
 * @see InternalCheckout::OSC_BASKET
 * @see InternalCheckout::OSC_BUTTONS
 * @see InternalCheckout::OSC_LINKEDS
 * @see InternalCheckout::OSC_LOCKED_STOCKS
 * @see InternalCheckout::OSC_PAYMENTS
 * @see InternalCheckout::OSC_RECALCULATE
 * @see InternalCheckout::OSC_REWARD_POINTS
 * @see InternalCheckout::OSC_SAVE_FOR_LATER
 * @see InternalCheckout::OSC_SELECTABLE_GIFTS
 * @see InternalCheckout::OSC_SHIPPINGS
 * @see InternalCheckout::PICKING_DELIVERY_POINTS
 * @see InternalCheckout::SET_PICKUP_POINT_PROVIDERS
 * @see InternalCheckout::RECALCULATE_BASKET
 * @see InternalCheckout::SELECT_ADDRESS_BOOK
 * 
 * @see Enum
 *
 * @package FWK\Enums\RouteTypes
 */
class InternalCheckout extends Enum {

    public const ADD_CUSTOMER = 'CHECKOUT_INTERNAL_ADD_CUSTOMER';

    public const CLEAR_BASKET = 'CHECKOUT_INTERNAL_CLEAR_BASKET';

    public const CONTINUE_SHOPPING = 'CHECKOUT_INTERNAL_CONTINUE_SHOPPING';

    public const EXPRESS_CHECKOUT = 'CHECKOUT_INTERNAL_EXPRESS_CHECKOUT';

    public const NEXT_STEP = 'CHECKOUT_INTERNAL_NEXT_STEP';

    public const OSC_BASKET = 'CHECKOUT_INTERNAL_OSC_BASKET';

    public const OSC_BUTTONS = 'CHECKOUT_INTERNAL_OSC_BUTTONS';

    public const OSC_DISCOUNTS = 'CHECKOUT_INTERNAL_OSC_DISCOUNTS';

    public const OSC_LINKEDS = 'CHECKOUT_INTERNAL_OSC_LINKEDS';

    public const OSC_LOCKED_STOCKS = 'CHECKOUT_INTERNAL_OSC_LOCKED_STOCKS';

    public const OSC_PAYMENTS = 'CHECKOUT_INTERNAL_OSC_PAYMENTS';

    public const OSC_RECALCULATE = 'CHECKOUT_INTERNAL_OSC_RECALCULATE';

    public const OSC_REWARD_POINTS = 'CHECKOUT_INTERNAL_OSC_REWARD_POINTS';

    public const OSC_SAVE_FOR_LATER = 'CHECKOUT_INTERNAL_OSC_SAVE_FOR_LATER';

    public const OSC_SELECTABLE_GIFTS = 'CHECKOUT_INTERNAL_OSC_SELECTABLE_GIFTS';

    public const OSC_SHIPPINGS = 'CHECKOUT_INTERNAL_OSC_SHIPPINGS';

    public const PICKING_DELIVERY_POINTS = 'CHECKOUT_INTERNAL_PICKING_DELIVERY_POINTS';

    public const SET_PICKUP_POINT_PROVIDERS = 'CHECKOUT_INTERNAL_SET_PICKUP_POINT_PROVIDERS';

    public const RECALCULATE_BASKET = 'CHECKOUT_INTERNAL_RECALCULATE_BASKET';

    public const SELECT_ADDRESS_BOOK = 'CHECKOUT_INTERNAL_SELECT_ADDRESS_BOOK';
}
