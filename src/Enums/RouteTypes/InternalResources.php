<?php

namespace FWK\Enums\RouteTypes;

use SDK\Core\Enums\Enum;

/**
 * This is the InternalResources enumeration class.
 * This class declares enumerations for a page route type.
 * <br> This class extends SDK\Core\Enums\Enum, see this class.
 *
 * @see InternalResources::ACCEPT_ROUTE_WARNING
 * @see InternalResources::ASYNC_JS
 * @see InternalResources::CUSTOMIZE_JS
 * @see InternalResources::ENVIRONMENT_JS
 * @see InternalResources::GET_SESSION
 * @see InternalResources::ORDER_PDF
 * @see InternalResources::PHP_COMMERCE_CLEAN_CACHE
 * @see InternalResources::PLUGIN_EXECUTE
 * @see InternalResources::PLUGIN_ROUTE
 * @see InternalResources::RELATED_ITEMS
 * @see InternalResources::SEND_DATA
 * @see InternalResources::SEND_MAIL
 * @see InternalResources::SET_NAVIGATION_COUNTRY
 *
 * @see Enum
 *
 * @package FWK\Enums\RouteTypes
 */
class InternalResources extends Enum {

    public const ACCEPT_ROUTE_WARNING = 'RESOURCES_INTERNAL_ACCEPT_ROUTE_WARNING';

    public const ASYNC_JS = 'RESOURCES_INTERNAL_ASYNC_JS';

    public const CUSTOMIZE_JS = 'RESOURCES_INTERNAL_CUSTOMIZE_JS';

    public const ENVIRONMENT_JS = 'RESOURCES_INTERNAL_ENVIRONMENT_JS';

    public const GET_SESSION = 'RESOURCES_INTERNAL_GET_SESSION';

    public const ORDER_PDF = 'RESOURCES_INTERNAL_ORDER_PDF';

    public const PHP_COMMERCE_CLEAN_CACHE = 'RESOURCES_INTERNAL_PHP_COMMERCE_CLEAN_CACHE';

    public const PLUGIN_EXECUTE = 'RESOURCES_INTERNAL_PLUGIN_EXECUTE';

    public const PLUGIN_ROUTE = 'RESOURCES_INTERNAL_PLUGIN_ROUTE';

    public const RELATED_ITEMS = 'RESOURCES_INTERNAL_RELATED_ITEMS';

    public const SEND_DATA = 'RESOURCES_INTERNAL_SEND_DATA';

    public const SEND_MAIL = 'RESOURCES_INTERNAL_SEND_MAIL';

    public const SET_NAVIGATION_COUNTRY = 'RESOURCES_INTERNAL_SET_NAVIGATION_COUNTRY';
}
