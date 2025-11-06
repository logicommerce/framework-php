<?php

namespace FWK\Enums\RouteTypes;

use SDK\Core\Enums\Enum;

/**
 * This is the InternalUser enumeration class.
 * This class declares enumerations for a user route type.
 * <br> This class extends SDK\Core\Enums\Enum, see this class.
 *
 * @abstract
 *
 * @see InternalUser::ADD_USER
 * @see InternalUser::ADD_USER_FAST_REGISTER
 * @see InternalUser::ADD_WISHLIST_PRODUCT
 * @see InternalUser::DELETE_ACCOUNT
 * @see InternalUser::DELETE_ADDRESS_BOOK
 * @see InternalUser::DELETE_PAYMENT_CARD
 * @see InternalUser::DELETE_SHOPPING_LIST
 * @see InternalUser::DELETE_SHOPPING_LIST_ROWS
 * @see InternalUser::DELETE_WISHLIST_PRODUCT
 * @see InternalUser::DELIVERY_NOTE
 * @see InternalUser::DELIVERY_NOTE_PDF
 * @see InternalUser::EXISTS
 * @see InternalUser::INVOICE
 * @see InternalUser::INVOICE_PDF
 * @see InternalUser::LOCATIONS_PATH
 * @see InternalUser::LOGIN
 * @see InternalUser::LOGIN_SIMULATION
 * @see InternalUser::LOGOUT
 * @see InternalUser::LOGOUT_SIMULATION
 * @see InternalUser::LOST_PASSWORD
 * @see InternalUser::NEW_PASSWORD 
 * @see InternalUser::NEWSLETTER
 * @see InternalUser::ORDER_SHIPMENTS
 * @see InternalUser::RECOVERY_BASKET 
 * @see InternalUser::RETURN
 * @see InternalUser::RETURN_REQUEST
 * @see InternalUser::RETURN_TRACING
 * @see InternalUser::RMA 
 * @see InternalUser::RMA_CORRECTIVE_INVOICE_PDF
 * @see InternalUser::RMA_CORRECTIVE_INVOICE
 * @see InternalUser::RMA_PDF
 * @see InternalUser::RMA_RETURNS_PDF
 * @see InternalUser::RMA_RETURNS
 * @see InternalUser::SALES_AGENT_CUSTOMER_ORDERS
 * @see InternalUser::SALES_AGENT_CUSTOMERS
 * @see InternalUser::SALES_AGENT_SALES
 * @see InternalUser::SEND_SHOPPING_LIST_ROWS
 * @see InternalUser::SEND_WISHLIST
 * @see InternalUser::SET_ADDRESS_BOOK
 * @see InternalUser::SET_CURRENCY
 * @see InternalUser::SET_SHOPPING_LIST
 * @see InternalUser::SET_SHOPPING_LIST_ROW
 * @see InternalUser::UNSUBSCRIBE_SUBSCRIPTION
 * @see InternalUser::UPDATE_PASSWORD
 * @see InternalUser::VERIFY_RESEND
 *
 * @see Enum
 *
 * @package FWK\Enums\RouteTypes
 */
abstract class InternalUser extends Enum {

    public const ADD_USER = 'USER_INTERNAL_ADD_USER';

    public const ADD_USER_FAST_REGISTER = 'USER_INTERNAL_ADD_USER_FAST_REGISTER';

    public const ADD_WISHLIST_PRODUCT = 'USER_INTERNAL_ADD_WISHLIST_PRODUCT';

    public const DELETE_ACCOUNT = 'USER_INTERNAL_DELETE_ACCOUNT';

    public const DELETE_ADDRESS_BOOK = 'USER_INTERNAL_DELETE_ADDRESS_BOOK';

    public const DELETE_PAYMENT_CARD = 'USER_INTERNAL_DELETE_PAYMENT_CARD';

    public const DELETE_STOCK_ALERT = 'USER_INTERNAL_DELETE_STOCK_ALERT';

    public const DELETE_WISHLIST_PRODUCT = 'USER_INTERNAL_DELETE_WISHLIST_PRODUCT';

    public const DELETE_SHOPPING_LIST = 'USER_INTERNAL_DELETE_SHOPPING_LIST';

    public const DELETE_SHOPPING_LIST_ROWS = 'USER_INTERNAL_DELETE_SHOPPING_LIST_ROWS';

    public const DELIVERY_NOTE = 'USER_INTERNAL_DELIVERY_NOTE';

    public const DELIVERY_NOTE_PDF = 'USER_INTERNAL_DELIVERY_NOTE_PDF';

    public const EXISTS = 'USER_INTERNAL_EXISTS';

    public const INVOICE = 'USER_INTERNAL_INVOICE';

    public const INVOICE_PDF = 'USER_INTERNAL_INVOICE_PDF';

    public const LOCATIONS_PATH = 'USER_INTERNAL_LOCATIONS_PATH';

    public const LOGIN = 'USER_INTERNAL_LOGIN';

    public const LOGOUT = 'USER_INTERNAL_LOGOUT';

    /** @deprecated */
    public const LOGIN_SIMULATION = 'USER_INTERNAL_LOGIN_SIMULATION';

    /** @deprecated */
    public const LOGOUT_SIMULATION = 'USER_INTERNAL_LOGOUT_SIMULATION';

    public const LOST_PASSWORD = 'USER_INTERNAL_LOST_PASSWORD';

    public const NEW_PASSWORD = 'USER_INTERNAL_NEW_PASSWORD';

    public const NEWSLETTER = 'USER_INTERNAL_NEWSLETTER';

    public const OAUTH = 'USER_INTERNAL_OAUTH';

    public const RECOVERY_BASKET = 'USER_INTERNAL_RECOVERY_BASKET';

    public const RETURN_REQUEST = 'USER_INTERNAL_RETURN_REQUEST';

    public const RETURN = 'USER_INTERNAL_RETURN';

    public const RETURN_TRACING = 'USER_INTERNAL_RETURN_TRACING';

    public const RETURN_TRACING_FORM = 'USER_INTERNAL_RETURN_TRACING_FORM';

    public const RMA = 'USER_INTERNAL_RMA';

    public const RMA_PDF = 'USER_INTERNAL_RMA_PDF';

    public const RMA_RETURNS = 'USER_INTERNAL_RMA_RETURNS';

    public const RMA_RETURNS_PDF = 'USER_INTERNAL_RMA_RETURNS_PDF';

    public const RMA_CORRECTIVE_INVOICE = 'USER_INTERNAL_RMA_CORRECTIVE_INVOICE';

    public const RMA_CORRECTIVE_INVOICE_PDF = 'USER_INTERNAL_RMA_CORRECTIVE_INVOICE_PDF';

    public const SEND_WISHLIST = 'USER_INTERNAL_SEND_WISHLIST';

    public const SEND_SHOPPING_LIST_ROWS = 'USER_INTERNAL_SEND_SHOPPING_LIST_ROWS';

    public const SET_ADDRESS_BOOK = 'USER_INTERNAL_SET_ADDRESS_BOOK';

    public const SET_CURRENCY = 'USER_INTERNAL_SET_CURRENCY';

    public const SET_SHOPPING_LIST = 'USER_INTERNAL_SET_SHOPPING_LIST';

    public const SET_SHOPPING_LIST_ROW = 'USER_INTERNAL_SET_SHOPPING_LIST_ROW';

    /** @deprecated */
    public const SALES_AGENT_CUSTOMERS = 'USER_INTERNAL_SALES_AGENT_CUSTOMERS';

    /** @deprecated */
    public const SALES_AGENT_CUSTOMER_ORDERS = 'USER_INTERNAL_SALES_AGENT_CUSTOMER_ORDERS';

    /** @deprecated */
    public const SALES_AGENT_SALES = 'USER_INTERNAL_SALES_AGENT_SALES';

    public const UNSUBSCRIBE_SUBSCRIPTION = 'USER_INTERNAL_UNSUBSCRIBE_SUBSCRIPTION';

    public const UPDATE_PASSWORD = 'USER_INTERNAL_UPDATE_PASSWORD';

    public const ORDER_SHIPMENTS = 'USER_INTERNAL_ORDER_SHIPMENTS';

    public const VERIFY_RESEND = 'USER_INTERNAL_VERIFY_RESEND';
}
