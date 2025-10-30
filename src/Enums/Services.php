<?php

namespace FWK\Enums;

use SDK\Core\Enums\Enum;

/**
 * This is the Services enumeration class.
 * This class declares enumerations for Services.
 * <br> This class extends SDK\Core\Enums\Enum, see this class.
 *
 * @abstract
 *
 * @see Services::ACCOUNT
 * @see Services::AREA
 * @see Services::BANNER
 * @see Services::BASKET
 * @see Services::BLOG
 * @see Services::BRAND
 * @see Services::CATEGORY
 * @see Services::CONTACT
 * @see Services::DATA_FEED
 * @see Services::DISCOUNT
 * @see Services::FORM
 * @see Services::GEOLOCATION
 * @see Services::KIMERA
 * @see Services::LEGAL_TEXT
 * @see Services::LMS
 * @see Services::NEWS
 * @see Services::ORDER
 * @see Services::PAGE
 * @see Services::PHYSICAL_LOCATIONS
 * @see Services::PLUGIN
 * @see Services::PRODUCT
 * @see Services::ROUTE
 * @see Services::SESSION
 * @see Services::SETTINGS
 * @see Services::SITEMAP
 * @see Services::TRACKER
 * @see Services::USER
 *
 * @see Enum
 * 
 * @package FWK\Enums
 */
abstract class Services extends Enum {

    public const ACCOUNT = 'account';

    public const AREA = 'area';

    public const BANNER = 'banner';

    public const BASKET = 'basket';

    public const BLOG = 'blog';

    public const BRAND = 'brand';

    public const CATEGORY = 'category';

    public const CONTACT = 'contact';

    public const DATA_FEED = 'dataFeed';

    public const DISCOUNT = 'discount';

    public const FORM = 'form';

    public const GEOLOCATION = 'geolocation';

    public const KIMERA = 'kimera';

    public const LEGAL_TEXT = 'legalText';

    public const LMS = 'lms';

    public const NEWS = 'news';

    public const ORDER = 'order';

    public const PAGE = 'page';

    public const PHYSICAL_LOCATIONS = 'physicalLocations';

    public const PLUGIN = 'plugin';

    public const PRODUCT = 'product';

    public const ROUTE = 'route';

    public const SESSION = 'Session';

    public const SETTINGS = 'settings';

    public const SITEMAP = 'sitemap';

    public const TRACKER = 'tracker';

    public const USER = 'user';
}
