<?php

namespace FWK\Core\Resources;

use FWK\Enums\RouteType;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Theme\Theme;
use FWK\Enums\Parameters;
use FWK\Enums\RouteTypes\InternalAccount;
use FWK\Enums\RouteTypes\InternalUtil;
use FWK\Enums\RouteTypes\InternalUser;
use FWK\Enums\RouteTypes\InternalResources;
use FWK\Enums\RouteTypes\InternalProduct;
use FWK\Enums\RouteTypes\InternalPage;
use FWK\Enums\RouteTypes\InternalGeolocation;
use FWK\Enums\RouteTypes\InternalCheckout;
use FWK\Enums\RouteTypes\InternalBlog;
use FWK\Enums\RouteTypes\InternalBasket;
use FWK\Enums\RouteTypes\InternalBanner;
use FWK\Enums\RouteTypes\InternalPhysicalLocation;
use FWK\Enums\RouteTypes\InternalProductComparison;
use SDK\Enums\AccountKey;

/**
 * This is the RoutePaths class.
 *
 * @abstract
 *
 * @see SDKRouteType
 *
 * @package FWK\Core\Resources
 */
class RoutePaths {

    private static $instance = null;

    private static string $urlPrefix = '';

    private static string $urlPrefixInternal = '';

    private const USER_REDIRECTS = [
        RouteType::USER => RouteType::ACCOUNT,
        RouteType::USER_ADDRESS_BOOK => RouteType::ACCOUNT_ADDRESSES,
        RouteType::USER_ADDRESS_BOOK_ADD => RouteType::ACCOUNT_ADDRESS_CREATE,
        RouteType::USER_ADDRESS_BOOK_EDIT => RouteType::ACCOUNT_ADDRESS,
        RouteType::USER_CHANGE_PASSWORD => RouteType::REGISTERED_USER_CHANGE_PASSWORD,
        RouteType::USER_COMPLETE_ACCOUNT => RouteType::ACCOUNT_COMPLETE,
        RouteType::USER_CREATE_ACCOUNT => RouteType::ACCOUNT_CREATE,
        RouteType::USER_DELETE_ACCOUNT => RouteType::ACCOUNT_DELETE,
        RouteType::USER_LOST_PASSWORD => RouteType::REGISTERED_USER_LOST_PASSWORD,
        RouteType::USER_OAUTH => RouteType::REGISTERED_USER_OAUTH,
        RouteType::USER_OAUTH_CALLBACK => RouteType::REGISTERED_USER_OAUTH_CALLBACK,
        RouteType::USER_ORDER => RouteType::ACCOUNT_ORDER,
        RouteType::USER_ORDERS => RouteType::ACCOUNT_ORDERS,
        RouteType::USER_PAYMENT_CARDS => RouteType::ACCOUNT_REGISTERED_USER_PAYMENT_CARDS,
        RouteType::USER_REWARD_POINTS => RouteType::ACCOUNT_REWARD_POINTS,
        RouteType::USER_RMAS => RouteType::ACCOUNT_RMAS,
        RouteType::USER_SALES_AGENT => RouteType::REGISTERED_USER_SALES_AGENT,
        RouteType::USER_SALES_AGENT_CUSTOMERS => RouteType::REGISTERED_USER_SALES_AGENT_CUSTOMERS,
        RouteType::USER_SALES_AGENT_SALES => RouteType::REGISTERED_USER_SALES_AGENT_SALES,
        RouteType::USER_SHOPPING_LISTS => RouteType::ACCOUNT_REGISTERED_USER_SHOPPING_LISTS,
        RouteType::USER_STOCK_ALERTS => RouteType::ACCOUNT_REGISTERED_USER_STOCK_ALERTS,
        RouteType::USER_SUBSCRIPTIONS => RouteType::ACCOUNT_REGISTERED_USER_SUBSCRIPTIONS,
        RouteType::USER_USER_WELCOME => RouteType::ACCOUNT_WELCOME,
        RouteType::USER_VERIFY_ACCOUNT => RouteType::ACCOUNT_VERIFY,
        RouteType::USER_VOUCHER_CODES => RouteType::ACCOUNT_VOUCHER_CODES,
        InternalUser::SALES_AGENT_CUSTOMERS => RouteType::REGISTERED_USER_SALES_AGENT_SALES,
        InternalUser::SALES_AGENT_SALES => RouteType::REGISTERED_USER_SALES_AGENT_CUSTOMERS,
    ];

    final private function __construct() {
        self::$urlPrefix = Utils::interceptURL(Session::getInstance()->getGeneralSettings()->getDefaultRoute()->getUrlPrefix());
        self::$urlPrefixInternal = self::$urlPrefix;
        $path = parse_url(self::$urlPrefix, PHP_URL_PATH);
        if (!is_null($path)) {
            self::$urlPrefixInternal = str_replace($path, '', self::$urlPrefix);
        }
    }

    /**
     * This method returns the instance of the RoutePaths.
     *
     * @internal Singleton instance.
     *          
     * @return self
     */
    final public static function getInstance(): RoutePaths {
        if (self::$instance === null) {
            self::setInstance();
        }
        return self::$instance;
    }

    private static function setInstance(): void {
        foreach (Loader::LOCATIONS as $location) {
            $class = Loader::getClassFQN('RoutePaths', $location . 'Core\\Resources\\', '');
            if (class_exists($class)) {
                self::$instance = new $class();
                return;
            }
        }
    }

    /**
     * This method reloads the RoutePaths instance 
     * 
     * @internal
     * Reload RoutePaths instance
     * 
     * @return RoutePaths
     */
    final public static function reloadInstance(): RoutePaths {
        self::setInstance();
        return self::getInstance();
    }

    private static function userRedirect(string $routeType): string {
        $userRedirects = self::USER_REDIRECTS;
        $useAccount = Theme::getInstance()->getConfiguration()->getAccount()->usedAccountPath();

        if ($useAccount && isset($userRedirects[$routeType])) {
            return $userRedirects[$routeType];
        }

        return $routeType;
    }

    /**
     * Returns the url path for the given route type
     * 
     * @param string $routeType
     */
    final public static function getPath(string $routeType): string {
        self::getInstance();
        $routeType = self::userRedirect($routeType);
        if (array_key_exists($routeType, self::getRouteTypePaths())) {
            $pattern = "/^((ACCOUNT|BANNER|BASKET|BLOG|CHECKOUT|PRODUCT_COMPARISON|GEOLOCATION|PAGE|PHYSICAL_LOCATION|PRODUCT|RESOURCES|USER|UTIL)_INTERNAL_.*)|UTIL_HEALTH_CHECK/";
            $path = self::$instance->getRouteTypePaths()[$routeType];
            if (preg_match($pattern, $routeType) === 0) {
                $path = self::$urlPrefix . $path;
            } else {
                $path = self::$urlPrefixInternal . $path;
            }

            return $path;
        } else {
            throw new CommerceException('Route type: ' . $routeType . ' is not defined in RoutePaths::getRouteTypePaths', CommerceException::ROUTE_PATHS_UNDEFINED_ROUTE_TYPE);
        }
    }

    /**
     * Returns an array with the list of route types and url paths
     * 
     * @param string $routeType
     */
    final public static function getRouteTypePaths(): array {
        self::getInstance();
        return self::$instance->getArrayRouteTypePaths();
    }

    protected static function getArrayRouteTypePaths(): array {
        return [
            InternalAccount::ACCOUNT_REGISTERED_USER_MOVE => '/' . INTERNAL_PREFIX . '/account/account_registered_user_move',
            InternalAccount::APPROVE_ACCOUNT_REGISTERED_USER => '/' . INTERNAL_PREFIX . '/account/approve_account_registered_user',
            InternalAccount::CREATE_ACCOUNT_REGISTERED_USER => '/' . INTERNAL_PREFIX . '/account/create_account_registered_user',
            InternalAccount::DELETE_ACCOUNT => '/' . INTERNAL_PREFIX . '/account/delete_account',
            InternalAccount::DELETE_ACCOUNT_REGISTERED_USER => '/' . INTERNAL_PREFIX . '/account/delete_account_registered_user',
            InternalAccount::DELETE_COMPANY_ROLE => '/' . INTERNAL_PREFIX . '/account/delete_company_role',
            InternalAccount::GET_REGISTERED_USER_EXISTS => '/' . INTERNAL_PREFIX . '/account/get_registered_user_exists',
            InternalAccount::LOGIN_SIMULATION => '/' . INTERNAL_PREFIX . '/account/login_simulation',
            InternalAccount::LOGOUT_SIMULATION => '/' . INTERNAL_PREFIX . '/account/logout_simulation',
            InternalAccount::MOVE_ACCOUNT_REGISTERED_USER => '/' . INTERNAL_PREFIX . '/account/move_account_registered_user',
            InternalAccount::ORDERS_APPROVAL_DECISION => '/' . INTERNAL_PREFIX . '/account/orders_approval_decision',
            InternalAccount::SAVE_COMPANY_DIVISION => '/' . INTERNAL_PREFIX . '/account/save_company_division',
            InternalAccount::SAVE_COMPANY_ROLE => '/' . INTERNAL_PREFIX . '/account/save_company_role',
            InternalAccount::SALES_AGENT_CUSTOMER_ORDERS => '/' . INTERNAL_PREFIX . '/account/sales_agent_customer_orders',
            InternalAccount::SEARCH_ACCOUNT_REGISTERED_USER => '/' . INTERNAL_PREFIX . '/account/search_account_registered_user',
            InternalAccount::SET_COMPANY_DIVISION => '/' . INTERNAL_PREFIX . '/account/set_company_division',
            InternalAccount::UPDATE_ACCOUNT => '/' . INTERNAL_PREFIX . '/account/update_account',
            InternalAccount::UPDATE_ACCOUNT_REGISTERED_USER => '/' . INTERNAL_PREFIX . '/account/update_account_registered_user',
            InternalAccount::UPDATE_REGISTERED_USER => '/' . INTERNAL_PREFIX . '/account/update_registered_user',
            InternalAccount::USED_ACCOUNT => '/' . INTERNAL_PREFIX . '/account/used_account',
            InternalBanner::DONE_CLICK => '/' . INTERNAL_PREFIX . '/banner/done_click',
            InternalBasket::ADD_PRODUCT => '/' . INTERNAL_PREFIX . '/basket/add_product',
            InternalBasket::ADD_PRODUCTS => '/' . INTERNAL_PREFIX . '/basket/add_products',
            InternalBasket::ADD_BUNDLE => '/' . INTERNAL_PREFIX . '/basket/add_bundle',
            InternalBasket::ADD_LINKED => '/' . INTERNAL_PREFIX . '/basket/add_linked',
            InternalBasket::ADD_GIFT => '/' . INTERNAL_PREFIX . '/basket/add_gift',
            InternalBasket::ADD_VOUCHER => '/' . INTERNAL_PREFIX . '/basket/add_voucher',
            InternalBasket::ATTACHMENT => '/' . INTERNAL_PREFIX . '/basket/attachment',
            InternalBasket::DELETE_SAVE_FOR_LATER_ROW => '/' . INTERNAL_PREFIX . '/basket/delete_save_for_later_row',
            InternalBasket::DELETE_ROW => '/' . INTERNAL_PREFIX . '/basket/delete_row',
            InternalBasket::DELETE_ROWS => '/' . INTERNAL_PREFIX . '/basket/delete_rows',
            InternalBasket::DELETE_VOUCHER => '/' . INTERNAL_PREFIX . '/basket/delete_voucher',
            InternalBasket::LOCKED_STOCK => '/' . INTERNAL_PREFIX . '/basket/locked_stock',
            InternalBasket::LOCKED_STOCK_RENEW => '/' . INTERNAL_PREFIX . '/basket/locked_stock_renew',
            InternalBasket::MINI_BASKET => '/' . INTERNAL_PREFIX . '/basket/mini_basket',
            InternalBasket::REDEEM_REWARD_POINTS => '/' . INTERNAL_PREFIX . '/basket/redeem_reward_points',
            InternalBasket::SAVE_FOR_LATER_ROW => '/' . INTERNAL_PREFIX . '/basket/save_for_later_row',
            InternalBasket::SET_DELIVERY => '/' . INTERNAL_PREFIX . '/basket/set_delivery',
            InternalBasket::SET_PAYMENT_SYSTEM => '/' . INTERNAL_PREFIX . '/basket/set_payment_system',
            InternalBasket::TRANSFER_TO_BASKET_SAVE_FOR_LATER_ROW => '/' . INTERNAL_PREFIX . '/basket/transfer_to_basket_save_for_later_row',
            InternalBlog::ADD_COMMENT => '/' . INTERNAL_PREFIX . '/blog/add_comment',
            InternalBlog::CATEGORY_SUBSCRIBE => '/' . INTERNAL_PREFIX . '/blog/category_subscribe',
            InternalBlog::POST_SUBSCRIBE => '/' . INTERNAL_PREFIX . '/blog/post_subscribe',
            InternalBlog::SUBSCRIBE => '/' . INTERNAL_PREFIX . '/blog/subscribe',
            InternalCheckout::ADD_CUSTOMER => '/' . INTERNAL_PREFIX . '/checkout/add_customer',
            InternalCheckout::CLEAR_BASKET => '/' . INTERNAL_PREFIX . '/checkout/clear_basket',
            InternalCheckout::CONTINUE_SHOPPING => '/' . INTERNAL_PREFIX . '/checkout/continue_shopping',
            InternalCheckout::EXPRESS_CHECKOUT => '/' . INTERNAL_PREFIX . '/checkout/express_checkout',
            InternalCheckout::NEXT_STEP => '/' . INTERNAL_PREFIX . '/checkout/next_step',
            InternalCheckout::RECALCULATE_BASKET => '/' . INTERNAL_PREFIX . '/checkout/recalculate_basket',
            InternalCheckout::SELECT_ADDRESS_BOOK => '/' . INTERNAL_PREFIX . '/checkout/select_address_book',
            InternalCheckout::OSC_BASKET => '/' . INTERNAL_PREFIX . '/checkout/osc_basket',
            InternalCheckout::OSC_BUTTONS => '/' . INTERNAL_PREFIX . '/checkout/osc_buttons',
            InternalCheckout::OSC_DISCOUNTS => '/' . INTERNAL_PREFIX . '/checkout/osc_discounts',
            InternalCheckout::OSC_LINKEDS => '/' . INTERNAL_PREFIX . '/checkout/osc_linkeds',
            InternalCheckout::OSC_LOCKED_STOCKS => '/' . INTERNAL_PREFIX . '/checkout/osc_locked_stocks',
            InternalCheckout::OSC_PAYMENTS => '/' . INTERNAL_PREFIX . '/checkout/osc_payments',
            InternalCheckout::OSC_RECALCULATE => '/' . INTERNAL_PREFIX . '/checkout/osc_recalculate',
            InternalCheckout::OSC_REWARD_POINTS => '/' . INTERNAL_PREFIX . '/checkout/osc_reward_points',
            InternalCheckout::OSC_SAVE_FOR_LATER => '/' . INTERNAL_PREFIX . '/checkout/osc_save_for_later',
            InternalCheckout::OSC_SELECTABLE_GIFTS => '/' . INTERNAL_PREFIX . '/checkout/osc_selectable_gifts',
            InternalCheckout::OSC_SHIPPINGS => '/' . INTERNAL_PREFIX . '/checkout/osc_shippings',
            InternalCheckout::PICKING_DELIVERY_POINTS => '/' . INTERNAL_PREFIX . '/checkout/picking_delivery_points',
            InternalCheckout::SET_PICKUP_POINT_PROVIDERS => '/' . INTERNAL_PREFIX . '/checkout/set_pickup_point_providers',
            InternalProductComparison::ADD_COMPARISON_PRODUCT => '/' . INTERNAL_PREFIX . '/productComparison/add_comparison_product',
            InternalProductComparison::DELETE_COMPARISON_PRODUCT => '/' . INTERNAL_PREFIX . '/productComparison/delete_comparison_product',
            InternalProductComparison::PRODUCT_COMPARISON_PREVIEW => '/' . INTERNAL_PREFIX . '/productComparison/product_comparison_preview',
            InternalGeolocation::GET_COUNTRIES => '/' . INTERNAL_PREFIX . '/geolocation/get_countries',
            InternalGeolocation::GET_LOCATIONS => '/' . INTERNAL_PREFIX . '/geolocation/get_locations',
            InternalGeolocation::GET_LOCATIONS_LOCALITIES => '/' . INTERNAL_PREFIX . '/geolocation/get_locations_localities',
            InternalGeolocation::GET_LOCATIONS_PATH => '/' . INTERNAL_PREFIX . '/geolocation/get_locations_path',
            InternalPage::SEND_CONTACT => '/' . INTERNAL_PREFIX . '/page/send_contact',
            InternalPage::SPONSOR_SHIP => '/' . INTERNAL_PREFIX . '/page/sponsor_ship',
            InternalPage::PRIVACY_POLICY => '/' . INTERNAL_PREFIX . '/page/privacy_policy',
            InternalPage::SEND_MAIL => '/' . INTERNAL_PREFIX . '/page/send_mail',
            InternalPage::TERMS_OF_USE => '/' . INTERNAL_PREFIX . '/page/terms_of_use',
            InternalPhysicalLocation::CITIES => '/' . INTERNAL_PREFIX . '/physicalLocation/cities',
            InternalPhysicalLocation::STATES => '/' . INTERNAL_PREFIX . '/physicalLocation/states',
            InternalProduct::ADD_COMMENT => '/' . INTERNAL_PREFIX . '/product/add_comment',
            InternalProduct::GET_BUNDLE_COMBINATION_DATA => '/' . INTERNAL_PREFIX . '/product/get_bundle_combination_data',
            InternalProduct::GET_PRODUCT_COMBINATION_DATA => '/' . INTERNAL_PREFIX . '/product/get_product_combination_data',
            InternalProduct::SET_CONTACT => '/' . INTERNAL_PREFIX . '/product/set_contact',
            InternalProduct::SET_RECOMMEND => '/' . INTERNAL_PREFIX . '/product/set_recommend',
            InternalProduct::SUBSCRIBE_STOCK => '/' . INTERNAL_PREFIX . '/product/subscribe_stock',
            InternalProduct::DISCOUNTS => '/' . INTERNAL_PREFIX . '/product/discounts',
            InternalResources::ACCEPT_ROUTE_WARNING => '/' . INTERNAL_PREFIX . '/resources/accept_route_warning',
            InternalResources::ASYNC_JS => '/' . INTERNAL_PREFIX . '/resources/async_js',
            InternalResources::CUSTOMIZE_JS => '/' . INTERNAL_PREFIX . '/resources/customize_js',
            InternalResources::ENVIRONMENT_JS => '/' . INTERNAL_PREFIX . '/resources/environment_js',
            InternalResources::GET_SESSION => '/' . INTERNAL_PREFIX . '/resources/get_session',
            InternalResources::ORDER_PDF => '/' . INTERNAL_PREFIX . '/resources/order_pdf',
            InternalResources::PHP_COMMERCE_CLEAN_CACHE => '/' . INTERNAL_PREFIX . '/resources/php_commerce_clean_cache',
            InternalResources::PLUGIN_EXECUTE => '/' . INTERNAL_PREFIX . '/resources/plugin_execute',
            InternalResources::RELATED_ITEMS => '/' . INTERNAL_PREFIX . '/resources/related_items',
            InternalResources::SEND_DATA => '/' . INTERNAL_PREFIX . '/resources/send_data',
            InternalResources::SEND_MAIL => '/' . INTERNAL_PREFIX . '/resources/send_mail',
            InternalResources::SET_NAVIGATION_COUNTRY => '/' . INTERNAL_PREFIX . '/resources/set_navigation_country',
            InternalUser::ADD_USER => '/' . INTERNAL_PREFIX . '/user/add_user',
            InternalUser::ADD_USER_FAST_REGISTER => '/' . INTERNAL_PREFIX . '/user/add_user_fast_register',
            InternalUser::ADD_WISHLIST_PRODUCT => '/' . INTERNAL_PREFIX . '/user/add_wishlist_product',
            InternalUser::DELETE_ACCOUNT => '/' . INTERNAL_PREFIX . '/user/delete_account',
            InternalUser::DELETE_ADDRESS_BOOK => '/' . INTERNAL_PREFIX . '/user/delete_address_book',
            InternalUser::DELETE_PAYMENT_CARD => '/' . INTERNAL_PREFIX . '/user/delete_payment_card',
            InternalUser::DELETE_STOCK_ALERT => '/' . INTERNAL_PREFIX . '/user/delete_stock_alert',
            InternalUser::DELETE_WISHLIST_PRODUCT => '/' . INTERNAL_PREFIX . '/user/delete_wishlist_product',
            InternalUser::DELETE_SHOPPING_LIST => '/' . INTERNAL_PREFIX . '/user/delete_shopping_list',
            InternalUser::DELETE_SHOPPING_LIST_ROWS => '/' . INTERNAL_PREFIX . '/user/delete_shopping_list_rows',
            InternalUser::DELIVERY_NOTE => '/' . INTERNAL_PREFIX . '/user/delivery_note',
            InternalUser::DELIVERY_NOTE_PDF => '/' . INTERNAL_PREFIX . '/user/delivery_note_pdf',
            InternalUser::EXISTS => '/' . INTERNAL_PREFIX . '/user/exists',
            InternalUser::INVOICE => '/' . INTERNAL_PREFIX . '/user/invoice',
            InternalUser::INVOICE_PDF => '/' . INTERNAL_PREFIX . '/user/invoice_pdf',
            InternalUser::LOCATIONS_PATH => '/' . INTERNAL_PREFIX . '/user/locations_path',
            InternalUser::LOGIN => '/' . INTERNAL_PREFIX . '/user/login',
            InternalUser::LOGIN_SIMULATION => '/' . INTERNAL_PREFIX . '/user/login_simulation',
            InternalUser::LOGOUT => '/' . INTERNAL_PREFIX . '/user/logout',
            InternalUser::LOGOUT_SIMULATION => '/' . INTERNAL_PREFIX . '/user/logout_simulation',
            InternalUser::LOST_PASSWORD => '/' . INTERNAL_PREFIX . '/user/lost_password',
            InternalUser::NEW_PASSWORD => '/' . INTERNAL_PREFIX . '/user/new_password',
            InternalUser::NEWSLETTER => '/' . INTERNAL_PREFIX . '/user/newsletter',
            InternalUser::RECOVERY_BASKET => '/' . INTERNAL_PREFIX . '/user/recovery_basket',
            InternalUser::RETURN => '/' . INTERNAL_PREFIX . '/user/return',
            InternalUser::RETURN_REQUEST => '/' . INTERNAL_PREFIX . '/user/return_request',
            InternalUser::RETURN_TRACING => '/' . INTERNAL_PREFIX . '/user/return_tracing',
            InternalUser::RETURN_TRACING_FORM => '/' . INTERNAL_PREFIX . '/user/return_tracing_form',
            InternalUser::RMA => '/' . INTERNAL_PREFIX . '/user/rma',
            InternalUser::RMA_PDF => '/' . INTERNAL_PREFIX . '/user/rma_pdf',
            InternalUser::RMA_RETURNS => '/' . INTERNAL_PREFIX . '/user/rma_returns',
            InternalUser::RMA_RETURNS_PDF => '/' . INTERNAL_PREFIX . '/user/rma_returns_pdf',
            InternalUser::RMA_CORRECTIVE_INVOICE => '/' . INTERNAL_PREFIX . '/user/rma_corrective_invoice',
            InternalUser::RMA_CORRECTIVE_INVOICE_PDF => '/' . INTERNAL_PREFIX . '/user/rma_corrective_invoice_pdf',
            InternalUser::SALES_AGENT_CUSTOMERS => '/' . INTERNAL_PREFIX . '/user/sales_agent_customers',
            InternalUser::SALES_AGENT_CUSTOMER_ORDERS => '/' . INTERNAL_PREFIX . '/user/sales_agent_customer_orders',
            InternalUser::SALES_AGENT_SALES => '/' . INTERNAL_PREFIX . '/user/sales_agent_sales',
            InternalUser::SEND_WISHLIST => '/' . INTERNAL_PREFIX . '/user/send_wishlist',
            InternalUser::SEND_SHOPPING_LIST_ROWS => '/' . INTERNAL_PREFIX . '/user/send_shopping_list_rows',
            InternalUser::SET_ADDRESS_BOOK => '/' . INTERNAL_PREFIX . '/user/set_address_book',
            InternalUser::SET_CURRENCY => '/' . INTERNAL_PREFIX . '/user/set_currency',
            InternalUser::SET_SHOPPING_LIST => '/' . INTERNAL_PREFIX . '/user/set_shopping_list',
            InternalUser::SET_SHOPPING_LIST_ROW => '/' . INTERNAL_PREFIX . '/user/set_shopping_list_row',
            InternalUser::UNSUBSCRIBE_SUBSCRIPTION => '/' . INTERNAL_PREFIX . '/user/unsubscribe_subscription',
            InternalUser::UPDATE_PASSWORD => '/' . INTERNAL_PREFIX . '/user/update_password',
            InternalUser::ORDER_SHIPMENTS => '/' . INTERNAL_PREFIX . '/user/order_shipments',
            InternalUser::VERIFY_RESEND => '/' . INTERNAL_PREFIX . '/user/verify_resend',
            InternalUtil::DEMO => '/' . INTERNAL_PREFIX . '/util/demo',
            InternalUtil::HEALTHCHECK => '/healthcheck.php',
            InternalUtil::PREVIEWDOCUMENTTEMPLATE => '/' . INTERNAL_PREFIX . '/util/previewDocumentTemplate',
            RouteType::ACCOUNT => '/accounts/' . AccountKey::USED,
            RouteType::ACCOUNT_ADDRESSES => '/accounts/' . AccountKey::USED . '/addresses',
            RouteType::ACCOUNT_ADDRESS => '/accounts/{' . Parameters::ACCOUNT_ID . '}/addresses/{' . Parameters::ADDRESS_ID . '}',
            RouteType::ACCOUNT_ADDRESS_CREATE => '/accounts/' . AccountKey::USED . '/addresses/create',
            RouteType::ACCOUNT_COMPANY_ROLE => '/accounts/' . AccountKey::USED . '/company/roles/{' . Parameters::ROLE_ID . '}',
            RouteType::ACCOUNT_COMPANY_ROLES => '/accounts/' . AccountKey::USED . '/company/roles',
            RouteType::ACCOUNT_COMPANY_STRUCTURE => '/accounts/' . AccountKey::USED . '/companyStructure',
            RouteType::ACCOUNT_COMPLETE => '/accounts/' . AccountKey::USED . '/complete',
            RouteType::ACCOUNT_CREATE => '/accounts/create',
            RouteType::ACCOUNT_DELETE => '/accounts/' . AccountKey::USED . '/delete',
            RouteType::ACCOUNT_DELETE_CONFIRM => '/accounts/delete/confirm',
            RouteType::ACCOUNT_ID => '/accounts/', // '/accounts/{id}'
            RouteType::ACCOUNT_ORDER => '/accounts/orders/', // /accounts/orders/{id}
            RouteType::ACCOUNT_ORDERS => '/accounts/' . AccountKey::USED . '/orders',
            RouteType::ACCOUNT_REGISTERED_USER => '/accounts/{' . Parameters::ACCOUNT_ID . '}/registeredUsers/{' . Parameters::REGISTERED_USER_ID . '}',
            RouteType::ACCOUNT_REGISTERED_USER_APPROVE => '/accounts/{' . Parameters::ACCOUNT_ID . '}/registeredUsers/{' . Parameters::REGISTERED_USER_ID . '}/approve',
            RouteType::ACCOUNT_REGISTERED_USER_CREATE => '/accounts/' . AccountKey::USED . '/registeredUsers/create',
            RouteType::ACCOUNT_REGISTERED_USER_PAYMENT_CARDS => '/accounts/' . AccountKey::USED . '/registeredUsers/me/paymentCards',
            RouteType::ACCOUNT_REGISTERED_USER_SHOPPING_LISTS => '/accounts/' . AccountKey::USED . '/registeredUsers/me/shoppingLists',
            RouteType::ACCOUNT_REGISTERED_USER_STOCK_ALERTS => '/accounts/' . AccountKey::USED . '/registeredUsers/me/stockAlerts',
            RouteType::ACCOUNT_REGISTERED_USER_SUBSCRIPTIONS => '/accounts/' . AccountKey::USED . '/registeredUsers/me/subscriptions',
            RouteType::ACCOUNT_REGISTERED_USERS => '/accounts/' . AccountKey::USED . '/registeredUsers',
            RouteType::ACCOUNT_RMAS => '/accounts/' . AccountKey::USED . '/rmas',
            RouteType::ACCOUNT_REWARD_POINTS => '/accounts/' . AccountKey::USED . '/rewardPoints',
            RouteType::ACCOUNT_VERIFY => '/accounts/verify',
            RouteType::ACCOUNT_VOUCHER_CODES => '/accounts/' . AccountKey::USED . '/voucherCodes',
            RouteType::ACCOUNT_WELCOME => '/accounts/welcome',
            RouteType::AREA => '/areas',
            RouteType::BASKET => '/basket',
            RouteType::BASKET_RECOVERY => '/basket/recovery',
            RouteType::BLOG_ARCHIVE => '/blog/archive',
            RouteType::BLOG_BLOGGER => '/blog/bloggers/',
            RouteType::BLOG_BLOGGERS => '/blog/bloggers',
            RouteType::BLOG_CATEGORY => '/blog/categories',
            RouteType::BLOG_CATEGORY_UNSUBSCRIBE => '/blog/categories/unsubscribe',
            RouteType::BLOG_HOME => '/blog',
            RouteType::BLOG_POST => '/blog/posts',
            RouteType::BLOG_POST_UNSUBSCRIBE => '/blog/posts/unsubscribe',
            RouteType::BLOG_RSS => '/blog/rss',
            RouteType::BLOG_TAG => '/blog/tags/', // '/blog/tags/{id}'
            RouteType::BLOG_UNSUBSCRIBE => '/blog/unsubscribe',
            RouteType::BRAND => '/brands/', // ''/brands/{id}'
            RouteType::BRANDS => '/brands',
            RouteType::DISCOUNTS => '/discounts',
            RouteType::CATEGORY => '/categories/', // '/categories/{id}'
            RouteType::CHANGE_PASSWORD_ANONYMOUS => '/changePassword',
            RouteType::CHECKOUT => '/checkout',
            RouteType::CHECKOUT_ASYNC_ORDER => '/checkout/asyncOrder',
            RouteType::CHECKOUT_BASKET => '/checkout/basket',
            RouteType::CHECKOUT_CONFIRM_ORDER => '/checkout/confirmOrder',
            RouteType::CHECKOUT_CREATE_ACCOUNT => '/checkout/createAccount',
            RouteType::CHECKOUT_CUSTOMER => '/checkout/customer',
            RouteType::CHECKOUT_CUSTOMER_NEW_REGISTER => '/checkout/newCustomer',
            RouteType::CHECKOUT_DENIED_ORDER => '/checkout/deniedOrder',
            RouteType::CHECKOUT_END_ORDER => '/checkout/endOrder',
            RouteType::CHECKOUT_GUEST => '/checkout/guest',
            RouteType::CHECKOUT_PAYMENT_AND_SHIPPING => '/checkout/paymentAndShipping',
            RouteType::PRODUCT_COMPARISON => '/productComparison',
            RouteType::CONTACT => '/contact',
            RouteType::DISCOUNT => '/discounts/', // '/discounts/{id}'
            RouteType::EXPRESS_CHECKOUT_CANCEL => '/expressCheckout/cancel',
            RouteType::EXPRESS_CHECKOUT_RETURN => '/expressCheckout/return',
            RouteType::FEATURED_PRODUCTS => '/products/featuredProducts',
            RouteType::FEED => '/feed',
            RouteType::HOME => '/',
            RouteType::NEWS => '/news/', // '/news/{id}'
            RouteType::NEWS_LIST => '/news',
            RouteType::NOT_FOUND => '',
            RouteType::OFFERS => '/products/offers',
            RouteType::ORDER => '/orders/', // '/orders/{id}'
            RouteType::PAGE => '/pages/', // '/pages/{id}'
            RouteType::PAGE_CUSTOM => '/pages/', // '/pages/{id}'
            RouteType::PAGE_MODULE => '/pages/', // '/pages/{id}'
            RouteType::PAGE_SITEMAP => '/pages/',     // '/pages/{id}' 
            RouteType::PAGE_NEWSLETTER => '/pages/', // '/pages/{id}'
            RouteType::PAGE_SUBPAGES => '/pages/', // '/pages/{id}'
            RouteType::PHYSICAL_LOCATION => '/physicalLocation',
            RouteType::PHYSICAL_LOCATION_CITIES => '/physicalLocation/cities',
            RouteType::PHYSICAL_LOCATION_COUNTRIES => '/physicalLocation/countries',
            RouteType::PHYSICAL_LOCATION_MAP => '/physicalLocation/map',
            RouteType::PHYSICAL_LOCATION_STATES => '/physicalLocation/states',
            RouteType::PHYSICAL_LOCATION_STORES => '/physicalLocation/stores',
            RouteType::PREHOME => '/',
            RouteType::PRIVACY_POLICY => '/privacyPolicy',
            RouteType::PRODUCT => '/products/', // '/products/{id}'
            RouteType::REGISTERED_USER => '/registeredUsers/me',
            RouteType::REGISTERED_USER_CHANGE_PASSWORD => '/registeredUsers/me/changePassword',
            RouteType::REGISTERED_USER_SALES_AGENT => '/registeredUsers/me/salesAgent',
            RouteType::REGISTERED_USER_SALES_AGENT_CUSTOMERS => '/registeredUsers/me/salesAgent/customers',
            RouteType::REGISTERED_USER_SALES_AGENT_SALES => '/registeredUsers/me/salesAgent/sales',
            RouteType::REGISTERED_USER_LOST_PASSWORD => '/registeredUsers/lostPassword',
            RouteType::REGISTERED_USER_OAUTH => '/registeredUsers/oauth',
            RouteType::REGISTERED_USER_OAUTH_CALLBACK => '/registeredUsers/oauth/callback',
            RouteType::SEARCH => '/search',
            RouteType::SITEMAP => '/sitemap.xml',
            RouteType::SUBSCRIPTION_UNSUBSCRIBE => '/subscription/unsubscribe',
            RouteType::SUBSCRIPTION_VERIFY => '/subscription/verify',
            RouteType::TERMS_OF_USE => '/termsOfUse',
            RouteType::USED_ACCOUNT_SWITCH => '/usedAccount/switch',
            RouteType::USER => '/user',
            RouteType::USER_ADDRESS_BOOK => '/user/addressBook',
            RouteType::USER_ADDRESS_BOOK_ADD => '/user/addressBook/add?type=',
            RouteType::USER_ADDRESS_BOOK_EDIT => '/user/addressBook/edit/', // /user/addressBook/edit/{id}
            RouteType::USER_CHANGE_PASSWORD => '/user/changePassword',
            RouteType::USER_COMPLETE_ACCOUNT => '/user/account/complete',
            RouteType::USER_CREATE_ACCOUNT => '/user/account/create',
            RouteType::USER_DELETE_ACCOUNT => '/user/account/delete',
            RouteType::USER_DELETE_NEWSLETTER => '/user/newsletter/delete',
            RouteType::USER_LOST_PASSWORD => '/user/lostPassword',
            RouteType::USER_OAUTH => '/user/oauth',
            RouteType::USER_OAUTH_CALLBACK => '/user/oauth/callback',
            RouteType::USER_OAUTH_CALLBACK_PATH => '/user/oauth/callback/', // '/user/oauth/callback/{plguinModule}',
            RouteType::USER_ORDER => '/user/orders/', // '/user/orders/{id}',
            RouteType::USER_ORDERS => '/user/orders',
            RouteType::USER_PAYMENT_CARDS => '/user/paymentCards',
            RouteType::USER_POLICIES => '/user/policies',
            RouteType::USER_RECOMMENDED_BASKETS => '/user/recommendBaskets',
            RouteType::USER_REWARD_POINTS => '/user/rewardPoints',
            RouteType::USER_RMAS => '/user/rmas',
            RouteType::USER_SALES_AGENT => '/user/salesAgent',
            RouteType::USER_SALES_AGENT_CUSTOMERS => '/user/salesAgent/customers',
            RouteType::USER_SALES_AGENT_SALES => '/user/salesAgent/sales',
            RouteType::USER_SHOPPING_LISTS => '/user/shoppingLists',
            RouteType::USER_STOCK_ALERTS => '/user/stockAlerts',
            RouteType::USER_SUBSCRIPTIONS => '/user/subscriptions',
            RouteType::USER_USER_WELCOME => '/user/welcome',
            RouteType::USER_VERIFY_ACCOUNT => '/user/account/verify',
            RouteType::USER_VOUCHER_CODES => '/user/voucherCodes',
            RouteType::USER_WISHLIST => '/user/wishlist',
            RouteType::WEBHOOK => '/webhook',
            RouteType::WEBHOOK_PATH => '/webhook/',
        ];
    }
}
