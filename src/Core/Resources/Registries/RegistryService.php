<?php

namespace FWK\Core\Resources\Registries;

/**
 * This is the RegistryService class, this class defines the registry key of each service
 * and allows to store for each key the corresponding service instance.
 *
 * @see RegistryService::ACCOUNT_SERVICE
 * @see RegistryService::AREA_SERVICE
 * @see RegistryService::BANNER_SERVICE
 * @see RegistryService::BASKET_SERVICE
 * @see RegistryService::BLOG_SERVICE
 * @see RegistryService::BRAND_SERVICE
 * @see RegistryService::CATEGORY_SERVICE
 * @see RegistryService::CONTACT_SERVICE
 * @see RegistryService::DATA_FEED_SERVICE
 * @see RegistryService::ORDER_SERVICE
 * @see RegistryService::FORM_SERVICE
 * @see RegistryService::GEOLOCATION_SERVICE
 * @see RegistryService::KIMERA_SERVICE
 * @see RegistryService::LEGAL_TEXT_SERVICE
 * @see RegistryService::LMS_SERVICE
 * @see RegistryService::NEWS_SERVICE
 * @see RegistryService::PAGES_SERVICE
 * @see RegistryService::PHYSICAL_LOCATIONS_SERVICE
 * @see RegistryService::PLUGIN_SERVICE
 * @see RegistryService::PRODUCT_SERVICE
 * @see RegistryService::ROUTE_SERVICE
 * @see RegistryService::SESSION_SERVICE
 * @see RegistryService::SETTINGS_SERVICE
 * @see RegistryService::SITEMAP_SERVICE
 * @see RegistryService::TRACKER_SERVICE
 * @see RegistryService::USER_SERVICE
 *
 * @see RegistryTrait
 *
 * @package FWK\Core\Resources\Registries
 */
abstract class RegistryService {

    use \SDK\Core\RegistryTrait;

    // These constants sets the ones that can be used as keys on this Registry
    public const ACCOUNT_SERVICE = 'accountService';

    public const AREA_SERVICE = 'areaService';

    public const BANNER_SERVICE = 'bannerService';

    public const BASKET_SERVICE = 'basketService';

    public const BLOG_SERVICE = 'blogService';

    public const BRAND_SERVICE = 'brandService';

    public const CATEGORY_SERVICE = 'categoryService';

    public const CONTACT_SERVICE = 'contactService';

    public const DATA_FEED_SERVICE = 'dataFeedService';

    public const DISCOUNT_SERVICE = 'DiscountService';

    public const FORM_SERVICE = 'formService';

    public const GEOLOCATION_SERVICE = 'geolocationService';

    public const KIMERA_SERVICE = 'KimeraService';

    public const LEGAL_TEXT_SERVICE = 'legalTextService';

    public const LMS_SERVICE = 'lmsService';

    public const NEWS_SERVICE = 'newsService';

    public const ORDER_SERVICE = 'orderService';

    public const PAGES_SERVICE = 'pagesService';

    public const PHYSICAL_LOCATIONS_SERVICE = 'physicalLocationsService';

    public const PLUGIN_SERVICE = 'pluginService';

    public const PRODUCT_SERVICE = 'productService';

    public const ROUTE_SERVICE = 'routeService';

    public const SESSION_SERVICE = 'sessionService';

    public const SETTINGS_SERVICE = 'settingsService';

    public const SITEMAP_SERVICE = 'sitemapService';

    public const TRACKER_SERVICE = 'trackerService';

    public const USER_SERVICE = 'userService';

    /**
     *
     * @var array
     */
    private static $storedValues = [
        self::ACCOUNT_SERVICE => null,
        self::AREA_SERVICE => null,
        self::BANNER_SERVICE => null,
        self::BASKET_SERVICE => null,
        self::BLOG_SERVICE => null,
        self::BRAND_SERVICE => null,
        self::CATEGORY_SERVICE => null,
        self::CONTACT_SERVICE => null,
        self::DATA_FEED_SERVICE => null,
        self::DISCOUNT_SERVICE => null,
        self::FORM_SERVICE => null,
        self::GEOLOCATION_SERVICE => null,
        self::KIMERA_SERVICE => null,
        self::LEGAL_TEXT_SERVICE => null,
        self::LMS_SERVICE => null,
        self::NEWS_SERVICE => null,
        self::ORDER_SERVICE => null,
        self::PAGES_SERVICE => null,
        self::PHYSICAL_LOCATIONS_SERVICE => null,
        self::PLUGIN_SERVICE => null,
        self::PRODUCT_SERVICE => null,
        self::ROUTE_SERVICE => null,
        self::SESSION_SERVICE => null,
        self::SETTINGS_SERVICE => null,
        self::SITEMAP_SERVICE => null,
        self::TRACKER_SERVICE => null,
        self::USER_SERVICE => null
    ];
}
