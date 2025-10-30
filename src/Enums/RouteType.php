<?php

namespace FWK\Enums;

use SDK\Enums\RouteType as SDKRouteType;
use SDK\Enums\PageType;

/**
 * This is the RouteType enumeration class.
 * This class declares route types enumerations.
 * <br> This class extends SDK\Enums\RouteType, see this class.
 *
 * @abstract
 *
 * @see RouteType::APP
 * @see RouteType::BANNER
 * @see RouteType::DEMO
 * @see RouteType::ERROR
 * @see RouteType::GEOLOCATION
 * @see RouteType::HEALTHCHECK
 * @see RouteType::UTIL
 * @see RouteType::MODULE
 * @see RouteType::BLOG_HOME
 * 
 * @see RouteType::PAGE_CUSTOM
 * @see RouteType::PAGE_MODULE
 * @see RouteType::PAGE_NEWSLETTER
 * @see RouteType::PAGE_SUBPAGES
 * @see RouteType::PAGE_SITEMAP
 *
 * @see SDKRouteType
 * 
 * @package FWK\Enums
 */
abstract class RouteType extends SDKRouteType {

    public const APP = 'APP';

    public const BANNER = 'BANNER';

    public const CLOSE_COMMERCE = 'CLOSE_COMMERCE';

    public const DEMO = 'DEMO';

    public const ERROR = 'ERROR';

    public const GEOLOCATION = 'GEOLOCATION';

    public const HEALTHCHECK = 'HEALTHCHECK';

    public const RESOURCES = 'RESOURCES';

    public const UTIL = 'UTIL';

    public const PAGE_CUSTOM = RouteType::PAGE . "_" . PageType::CUSTOM;

    public const PAGE_MODULE = RouteType::PAGE . "_" . PageType::MODULE;

    public const PAGE_NEWSLETTER = RouteType::PAGE . "_" . PageType::NEWSLETTER;

    public const PAGE_SUBPAGES = RouteType::PAGE . "_" . PageType::SUBPAGES;

    public const PAGE_SITEMAP = RouteType::PAGE . "_" . PageType::SITEMAP;
}
