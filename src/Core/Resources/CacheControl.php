<?php

namespace FWK\Core\Resources;

use FWK\Enums\RouteType;
use FWK\Enums\RouteTypes\InternalAccount;
use FWK\Enums\RouteTypes\InternalBanner;
use FWK\Enums\RouteTypes\InternalBasket;
use FWK\Enums\RouteTypes\InternalGeolocation;
use FWK\Enums\RouteTypes\InternalPage;
use FWK\Enums\RouteTypes\InternalProduct;
use FWK\Enums\RouteTypes\InternalUser;
use FWK\Enums\RouteTypes\InternalUtil;
use FWK\Enums\RouteTypes\InternalCheckout;
use FWK\Core\Exceptions\CommerceException;
use FWK\Enums\RouteTypes\InternalResources;
use FWK\Enums\RouteTypes\InternalBlog;
use FWK\Enums\RouteTypes\InternalPhysicalLocation;
use FWK\Enums\RouteTypes\InternalProductComparison;

/**
 * This is the CacheControl class.
 * This class has the logic to set de cache control.
 * <br>This class is instantiable and is automatically overridden just by extending this class from the site layer: Core\\Resources\\
 *
 * @see CacheControl::getInstance()
 * @see CacheControl::setCacheHeaders()
 * @see CacheControl::getRouteTypesNoStorables()
 * @see CacheControl::getRouteTypesStorables()
 *
 * @package FWK\Core
 */
class CacheControl {

    private static $instance = null;

    private static string $routeType = RouteType::NOT_FOUND;

    public static ?string $cacheHash = null;

    final private function __construct(string $routeType) {
        self::$routeType = $routeType;
    }

    /**
     * This method returns the instance of the CacheControl.
     *
     * @internal Singleton instance.
     *          
     * @param string $routeType.
     *            Default value: RouteType::NOT_FOUND
     *            
     * @return self
     */
    final public static function getInstance(string $routeType = RouteType::NOT_FOUND): ?CacheControl {
        if (self::$instance === null) {
            self::setInstance($routeType);
        }
        return self::$instance;
    }

    private static function setInstance(string $routeType): void {
        foreach (Loader::LOCATIONS as $location) {
            $class = Loader::getClassFQN('CacheControl', $location . 'Core\\Resources\\', '');
            if (class_exists($class)) {
                self::$instance = new $class($routeType);
                return;
            }
        }
    }

    /**
     * This method unsets the CacheControl instance.
     */
    final public static function resetInstance(): void {
        self::$instance = null;
    }

    /**
     * This method reloads the CacheControl instance
     *
     * @internal It unsets the current instance and generates a new one invoking the CacheControl::resetInstance() and CacheControl::getInstance() methods.
     *          
     * @see Language::resetInstance()
     * @see Language::getInstance()
     *
     * @param string $routeType
     *
     * @return CacheControl
     */
    final public static function reloadInstance(string $routeType): CacheControl {
        self::resetInstance();
        return self::getInstance($routeType);
    }

    /**
     * Set initial cacheHash value.
     *
     * @param string $value
     *
     * @return void
     */
    final public static function setInitialCacheHash(?string $value): void {
        self::$cacheHash = $value;
    }

    /**
     * This method returns if the request is cacheable. The parameter cacheHash 
     *
     * @param NULL|string $cacheHash
     * 
     * @return bool
     *
     * @throws CommerceException
     */
    final public function isCacheable(?string $cacheHash = null): bool {
        if (in_array(self::$routeType, $this->getRouteTypesNoStorables()) || LcFWK::getMaintenance()) {
            return false;
        } elseif (in_array(self::$routeType, $this->getRouteTypesStorables())) {
            if (is_null($cacheHash) || is_null(self::$cacheHash) || $cacheHash == self::$cacheHash) {
                return true;
            }
            return false;
        } else {
            throw new CommerceException('Route type: ' . self::$routeType . ' is not defined in CacheControl::getRouteTypesNoStorable or CacheControl::getRouteTypesStorable', CommerceException::CACHE_CONTROL_UNDEFINED_ROUTE_TYPE);
        }
    }

    /**
     * This method returns the routeTypes that can't be stored in cache.
     *
     * @return array
     */
    protected function getRouteTypesNoStorables(): array {
        return [
            InternalAccount::ACCOUNT_REGISTERED_USER_MOVE,
            InternalAccount::SAVE_COMPANY_DIVISION,
            InternalAccount::APPROVE_ACCOUNT_REGISTERED_USER,
            InternalAccount::CREATE_ACCOUNT_REGISTERED_USER,
            InternalAccount::DELETE_ACCOUNT_REGISTERED_USER,
            InternalAccount::DELETE_ACCOUNT,
            InternalAccount::LOGIN_SIMULATION,
            InternalAccount::LOGOUT_SIMULATION,
            InternalAccount::MOVE_ACCOUNT,
            InternalAccount::MOVE_ACCOUNT_REGISTERED_USER,
            InternalAccount::ORDERS_APPROVAL_DECISION,
            InternalAccount::SALES_AGENT_CUSTOMER_ORDERS,
            InternalAccount::SEARCH_ACCOUNT_REGISTERED_USER,
            InternalAccount::SET_COMPANY_DIVISION,
            InternalAccount::UPDATE_ACCOUNT_REGISTERED_USER,
            InternalAccount::UPDATE_REGISTERED_USER,
            InternalAccount::USED_ACCOUNT,
            InternalAccount::USED_ACCOUNT_SWITCH,
            InternalAccount::SAVE_COMPANY_ROLE,
            InternalAccount::DELETE_COMPANY_ROLE,
            InternalAccount::UPDATE_ACCOUNT,
            InternalBanner::DONE_CLICK,
            InternalBasket::ADD_BUNDLE,
            InternalBasket::ADD_LINKED,
            InternalBasket::ADD_PRODUCT,
            InternalBasket::ADD_PRODUCTS,
            InternalBasket::ADD_GIFT,
            InternalBasket::ADD_VOUCHER,
            InternalBasket::DELETE_SAVE_FOR_LATER_ROW,
            InternalBasket::DELETE_ROW,
            InternalBasket::DELETE_ROWS,
            InternalBasket::DELETE_VOUCHER,
            InternalBasket::LOCKED_STOCK,
            InternalBasket::LOCKED_STOCK_RENEW,
            InternalBasket::MINI_BASKET,
            InternalBasket::REDEEM_REWARD_POINTS,
            InternalBasket::SAVE_FOR_LATER_ROW,
            InternalBasket::SET_DELIVERY,
            InternalBasket::SET_PAYMENT_SYSTEM,
            InternalBasket::TRANSFER_TO_BASKET_SAVE_FOR_LATER_ROW,
            InternalBlog::ADD_COMMENT,
            InternalBlog::CATEGORY_SUBSCRIBE,
            InternalBlog::POST_SUBSCRIBE,
            InternalBlog::SUBSCRIBE,
            InternalCheckout::ADD_CUSTOMER,
            InternalCheckout::CLEAR_BASKET,
            InternalCheckout::CONTINUE_SHOPPING,
            InternalCheckout::EXPRESS_CHECKOUT,
            InternalCheckout::NEXT_STEP,
            InternalCheckout::OSC_BASKET,
            InternalCheckout::OSC_BUTTONS,
            InternalCheckout::OSC_DISCOUNTS,
            InternalCheckout::OSC_LINKEDS,
            InternalCheckout::OSC_LOCKED_STOCKS,
            InternalCheckout::OSC_SELECTABLE_GIFTS,
            InternalCheckout::OSC_PAYMENTS,
            InternalCheckout::OSC_RECALCULATE,
            InternalCheckout::OSC_REWARD_POINTS,
            InternalCheckout::OSC_SAVE_FOR_LATER,
            InternalCheckout::OSC_SHIPPINGS,
            InternalCheckout::RECALCULATE_BASKET,
            InternalCheckout::SELECT_ADDRESS_BOOK,
            InternalCheckout::PICKING_DELIVERY_POINTS,
            InternalCheckout::SET_PICKUP_POINT_PROVIDERS,
            InternalProductComparison::ADD_COMPARISON_PRODUCT,
            InternalProductComparison::DELETE_COMPARISON_PRODUCT,
            InternalProductComparison::PRODUCT_COMPARISON_PREVIEW,
            InternalPage::SEND_CONTACT,
            InternalPage::SPONSOR_SHIP,
            InternalProduct::ADD_COMMENT,
            InternalProduct::SET_CONTACT,
            InternalProduct::SET_RECOMMEND,
            InternalProduct::SUBSCRIBE_STOCK,
            InternalResources::ACCEPT_ROUTE_WARNING,
            InternalResources::ASYNC_JS,
            InternalResources::CUSTOMIZE_JS,
            InternalResources::ORDER_PDF,
            InternalResources::PHP_COMMERCE_CLEAN_CACHE,
            InternalResources::PLUGIN_EXECUTE,
            InternalResources::PLUGIN_ROUTE,
            InternalResources::SEND_DATA,
            InternalResources::SEND_MAIL,
            InternalResources::SET_NAVIGATION_COUNTRY,
            InternalResources::GET_SESSION,
            InternalUser::ADD_USER_FAST_REGISTER,
            InternalUser::ADD_USER,
            InternalUser::ADD_WISHLIST_PRODUCT,
            InternalUser::DELETE_ACCOUNT,
            InternalUser::DELETE_ADDRESS_BOOK,
            InternalUser::DELETE_PAYMENT_CARD,
            InternalUser::DELETE_STOCK_ALERT,
            InternalUser::DELETE_WISHLIST_PRODUCT,
            InternalUser::DELETE_SHOPPING_LIST,
            InternalUser::DELETE_SHOPPING_LIST_ROWS,
            InternalUser::DELIVERY_NOTE_PDF,
            InternalUser::DELIVERY_NOTE,
            InternalUser::EXISTS,
            InternalUser::INVOICE_PDF,
            InternalUser::INVOICE,
            InternalUser::LOGIN_SIMULATION,
            InternalUser::LOGIN,
            InternalUser::LOGOUT_SIMULATION,
            InternalUser::LOGOUT,
            InternalUser::LOST_PASSWORD,
            InternalUser::NEW_PASSWORD,
            InternalUser::NEWSLETTER,
            InternalUser::RECOVERY_BASKET,
            InternalUser::RETURN_REQUEST,
            InternalUser::RETURN_TRACING,
            InternalUser::RETURN,
            InternalUser::RMA_CORRECTIVE_INVOICE_PDF,
            InternalUser::RMA_CORRECTIVE_INVOICE,
            InternalUser::RMA_PDF,
            InternalUser::RMA_RETURNS_PDF,
            InternalUser::RMA_RETURNS,
            InternalUser::RMA,
            InternalUser::SALES_AGENT_CUSTOMER_ORDERS,
            InternalUser::SALES_AGENT_CUSTOMERS,
            InternalUser::SALES_AGENT_SALES,
            InternalUser::SEND_WISHLIST,
            InternalUser::SEND_SHOPPING_LIST_ROWS,
            InternalUser::SET_ADDRESS_BOOK,
            InternalUser::SET_CURRENCY,
            InternalUser::UPDATE_PASSWORD,
            InternalUser::UNSUBSCRIBE_SUBSCRIPTION,
            InternalUser::SET_SHOPPING_LIST,
            InternalUser::SET_SHOPPING_LIST_ROW,
            InternalUser::ORDER_SHIPMENTS,
            InternalUser::VERIFY_RESEND,
            InternalUtil::DEMO,
            InternalUtil::HEALTHCHECK,
            InternalUtil::PREVIEWDOCUMENTTEMPLATE,
            RouteType::ACCOUNT_CREATE,
            RouteType::ACCOUNT_VERIFY,
            RouteType::ACCOUNT_DELETE_CONFIRM,
            RouteType::ACCOUNT_WELCOME,
            RouteType::ACCOUNT,
            RouteType::ACCOUNT_ID,
            RouteType::ACCOUNT_DELETE,
            RouteType::ACCOUNT_ADDRESSES,
            RouteType::ACCOUNT_ADDRESS_CREATE,
            RouteType::ACCOUNT_ADDRESS,
            RouteType::ACCOUNT_REGISTERED_USERS,
            RouteType::ACCOUNT_REGISTERED_USER_CREATE,
            RouteType::ACCOUNT_REGISTERED_USER,
            RouteType::ACCOUNT_REGISTERED_USER_APPROVE,
            RouteType::ACCOUNT_ORDERS,
            RouteType::ACCOUNT_ORDER,
            RouteType::ACCOUNT_RMAS,
            RouteType::ACCOUNT_COMPANY_STRUCTURE,
            RouteType::ACCOUNT_COMPANY_ROLES,
            RouteType::ACCOUNT_COMPANY_ROLE,
            RouteType::ACCOUNT_COMPLETE,
            RouteType::ACCOUNT_REWARD_POINTS,
            RouteType::ACCOUNT_VOUCHER_CODES,
            RouteType::ACCOUNT_REGISTERED_USER_PAYMENT_CARDS,
            RouteType::ACCOUNT_REGISTERED_USER_SHOPPING_LISTS,
            RouteType::ACCOUNT_REGISTERED_USER_STOCK_ALERTS,
            RouteType::ACCOUNT_REGISTERED_USER_SUBSCRIPTIONS,
            RouteType::APP,
            RouteType::BANNER,
            RouteType::BASKET_RECOVERY,
            RouteType::BASKET,
            RouteType::CHANGE_PASSWORD_ANONYMOUS,
            RouteType::CHECKOUT_ASYNC_ORDER,
            RouteType::CHECKOUT_BASKET,
            RouteType::CHECKOUT_CONFIRM_ORDER,
            RouteType::CHECKOUT_CREATE_ACCOUNT,
            RouteType::CHECKOUT_CUSTOMER_NEW_REGISTER,
            RouteType::CHECKOUT_CUSTOMER,
            RouteType::CHECKOUT_DENIED_ORDER,
            RouteType::CHECKOUT_END_ORDER,
            RouteType::CHECKOUT_GUEST,
            RouteType::CHECKOUT_PAYMENT_AND_SHIPPING,
            RouteType::CHECKOUT,
            RouteType::CLOSE_COMMERCE,
            RouteType::PRODUCT_COMPARISON,
            RouteType::DEMO,
            RouteType::DISCOUNT,
            RouteType::ERROR,
            RouteType::EXPRESS_CHECKOUT_CANCEL,
            RouteType::EXPRESS_CHECKOUT_RETURN,
            RouteType::HEALTHCHECK,
            RouteType::NOT_FOUND,
            RouteType::REGISTERED_USER,
            RouteType::REGISTERED_USER_CHANGE_PASSWORD,
            RouteType::REGISTERED_USER_SALES_AGENT,
            RouteType::REGISTERED_USER_SALES_AGENT_SALES,
            RouteType::REGISTERED_USER_SALES_AGENT_CUSTOMERS,
            RouteType::REGISTERED_USER_LOST_PASSWORD,
            RouteType::REGISTERED_USER_OAUTH,
            RouteType::REGISTERED_USER_OAUTH_CALLBACK,
            RouteType::SUBSCRIPTION_UNSUBSCRIBE,
            RouteType::SUBSCRIPTION_VERIFY,
            RouteType::USED_ACCOUNT_SWITCH,
            RouteType::USER_ADDRESS_BOOK_ADD,
            RouteType::USER_ADDRESS_BOOK_EDIT,
            RouteType::USER_ADDRESS_BOOK,
            RouteType::USER_CHANGE_PASSWORD,
            RouteType::USER_COMPLETE_ACCOUNT,
            RouteType::USER_CREATE_ACCOUNT,
            RouteType::USER_DELETE_ACCOUNT,
            RouteType::USER_DELETE_NEWSLETTER,
            RouteType::USER_LOST_PASSWORD,
            RouteType::USER_OAUTH,
            RouteType::USER_OAUTH_CALLBACK,
            RouteType::USER_OAUTH_CALLBACK_PATH,
            RouteType::USER_ORDER,
            RouteType::USER_ORDERS,
            RouteType::USER_PAYMENT_CARDS,
            RouteType::USER_POLICIES,
            RouteType::USER_RECOMMENDED_BASKETS,
            RouteType::USER_REWARD_POINTS,
            RouteType::USER_RMAS,
            RouteType::USER_SALES_AGENT,
            RouteType::USER_SALES_AGENT_CUSTOMERS,
            RouteType::USER_SALES_AGENT_SALES,
            RouteType::USER_SHOPPING_LISTS,
            RouteType::USER_SPONSORSHIP,
            RouteType::USER_STOCK_ALERTS,
            RouteType::USER_SUBSCRIPTIONS,
            RouteType::USER_USER_WELCOME,
            RouteType::USER_VERIFY_ACCOUNT,
            RouteType::USER_VOUCHER_CODES,
            RouteType::USER_WISHLIST,
            RouteType::USER,
            RouteType::WEBHOOK,
            RouteType::WEBHOOK_PATH
        ];
    }

    /**
     * This method returns the routeTypes that can be stored in cache.
     *
     * @return array
     */
    protected function getRouteTypesStorables(): array {
        return [
            InternalAccount::GET_REGISTERED_USER_EXISTS,
            InternalBasket::ATTACHMENT,
            InternalGeolocation::GET_COUNTRIES,
            InternalGeolocation::GET_LOCATIONS_LOCALITIES,
            InternalGeolocation::GET_LOCATIONS_PATH,
            InternalGeolocation::GET_LOCATIONS,
            InternalPage::PRIVACY_POLICY,
            InternalPage::SEND_MAIL,
            InternalPage::TERMS_OF_USE,
            InternalPhysicalLocation::CITIES,
            InternalPhysicalLocation::STATES,
            InternalProduct::GET_BUNDLE_COMBINATION_DATA,
            InternalProduct::GET_PRODUCT_COMBINATION_DATA,
            InternalProduct::DISCOUNTS,
            InternalResources::ENVIRONMENT_JS,
            InternalResources::RELATED_ITEMS,
            InternalUser::LOCATIONS_PATH,
            RouteType::AREA,
            RouteType::BANNER,
            RouteType::BLOG_ARCHIVE,
            RouteType::BLOG_BLOGGER,
            RouteType::BLOG_BLOGGERS,
            RouteType::BLOG_CATEGORY_UNSUBSCRIBE,
            RouteType::BLOG_CATEGORY,
            RouteType::BLOG_HOME,
            RouteType::BLOG_POST_UNSUBSCRIBE,
            RouteType::BLOG_POST,
            RouteType::BLOG_RSS,
            RouteType::BLOG_TAG_CLOUD,
            RouteType::BLOG_TAG,
            RouteType::BLOG_UNSUBSCRIBE,
            RouteType::BLOG,
            RouteType::BRAND,
            RouteType::BRANDS,
            RouteType::CATEGORY,
            RouteType::CONTACT,
            RouteType::DISCOUNTS,
            RouteType::FEATURED_PRODUCTS,
            RouteType::FEED,
            RouteType::GEOLOCATION,
            RouteType::HOME,
            RouteType::NEWS_LIST,
            RouteType::NEWS,
            RouteType::LOGIN_REQUIRED,
            RouteType::OFFERS,
            RouteType::ORDER,
            RouteType::PAGE_CUSTOM,
            RouteType::PAGE_MODULE,
            RouteType::PAGE_NEWSLETTER,
            RouteType::PAGE_SITEMAP,
            RouteType::PAGE_SUBPAGES,
            RouteType::PAGE,
            RouteType::PHYSICAL_LOCATION_CITIES,
            RouteType::PHYSICAL_LOCATION_COUNTRIES,
            RouteType::PHYSICAL_LOCATION_MAP,
            RouteType::PHYSICAL_LOCATION_STATES,
            RouteType::PHYSICAL_LOCATION_STORES,
            RouteType::PHYSICAL_LOCATION,
            RouteType::PREHOME,
            RouteType::PRIVACY_POLICY,
            RouteType::PRODUCT,
            RouteType::SEARCH,
            RouteType::SITEMAP,
            RouteType::TERMS_OF_USE,
        ];
    }
}
