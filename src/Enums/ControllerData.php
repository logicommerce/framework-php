<?php

namespace FWK\Enums;

use SDK\Core\Enums\Enum;

/**
 * This is the ControllerData enumeration class.
 * This class declares enumerations for controller's data.
 * <br> This class extends SDK\Core\Enums\Enum, see this class.
 *
 * @abstract
 *
 * @see ControllerData::CACHE_HASH
 * @see ControllerData::COMMERCE_CALENDAR
 * @see ControllerData::COMMERCE_DATE_TIME
 * @see ControllerData::CONTENT
 * @see ControllerData::CORE_MODE
 * @see ControllerData::LANGUAGE_SHEET
 * @see ControllerData::LAYOUT
 * @see ControllerData::MACROS_CORE
 * @see ControllerData::MACROS_CORE_ACCOUNT
 * @see ControllerData::MACROS_CORE_BASKET
 * @see ControllerData::MACROS_CORE_BLOG
 * @see ControllerData::MACROS_CORE_CATEGORY
 * @see ControllerData::MACROS_CORE_DOCUMENT
 * @see ControllerData::MACROS_CORE_FORM
 * @see ControllerData::MACROS_CORE_INCIDENCES
 * @see ControllerData::MACROS_CORE_PAGE
 * @see ControllerData::MACROS_CORE_PRODUCT
 * @see ControllerData::MACROS_CORE_THIRD_PARTY
 * @see ControllerData::MACROS_CORE_USER
 * @see ControllerData::MACROS_CORE_UTIL
 * @see ControllerData::REQUEST_PARAMETERS
 * @see ControllerData::ROUTE
 * @see ControllerData::ROUTE_PATHS
 * @see ControllerData::SEO_ITEMS
 * @see ControllerData::SERVER_TIME
 * @see ControllerData::SESSION
 * @see ControllerData::SETTINGS
 * @see ControllerData::THEME_CONFIGURATION
 * @see ControllerData::TIMER
 * @see ControllerData::VERSION
 * @see ControllerData::VIEW_HELPERS
 *
 * @see Enum
 *
 * @package FWK\Enums
 */
abstract class ControllerData extends Enum {

    public const CACHE_HASH = 'cacheHash';

    public const COMMERCE_CALENDAR = 'commerceCalendar';

    public const COMMERCE_DATE_TIME = 'commerceDateTime';

    public const CONTENT = 'content';

    public const CORE_MODE = 'coreMode';

    public const LANGUAGE_SHEET = 'languageSheet';

    public const LAYOUT = 'layout';

    public const MACROS_CORE = 'macrosCore';

    public const MACROS_CORE_ACCOUNT = 'account';

    public const MACROS_CORE_BASKET = 'basket';

    public const MACROS_CORE_BLOG = 'blog';

    public const MACROS_CORE_CATEGORY = 'category';

    public const MACROS_CORE_DOCUMENT = 'document';

    public const MACROS_CORE_FORM = 'form';

    public const MACROS_CORE_INCIDENCES = 'incidence';

    public const MACROS_CORE_PAGE = 'page';

    public const MACROS_CORE_PRODUCT = 'product';

    public const MACROS_CORE_THIRD_PARTY = 'thirdParty';

    public const MACROS_CORE_USER = 'user';

    public const MACROS_CORE_UTIL = 'util';

    public const REQUEST_PARAMETERS = 'requestParams';

    public const ROUTE = 'route';

    public const ROUTE_PATHS = 'routePaths';

    public const SEO_ITEMS = 'seoItems';

    public const SERVER_TIME = 'serverTime';

    public const SESSION = 'session';

    public const SETTINGS = 'settings';

    public const THEME_CONFIGURATION = 'themeConfiguration';

    public const TIMER = 'timer';

    public const VERSION = 'version';

    public const VIEW_HELPERS = 'viewHelpers';
}
