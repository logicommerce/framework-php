<?php

namespace FWK\Core\Resources;

use Exception;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Theme\Dtos\Commerce;
use FWK\Core\Resources\Session\BasketGridProduct;
use FWK\Core\Resources\Session\SessionGeneralSettings;
use FWK\Core\Resources\Session\SessionShoppingList;
use FWK\Core\Theme\Dtos\CommerceLockedStock;
use SDK\Core\Resources\Connection;
use SDK\Core\Resources\RedisSessionHandler;
use SDK\Dtos\Common\Route;
use FWK\Enums\Services;
use SDK\Core\Resources\Cookie;
use SDK\Dtos\SessionAggregateData;
use FWK\Core\Theme\Theme;
use FWK\Services\LmsService;
use SDK\Core\Dtos\ElementCollection;
use SDK\Core\Enums\MethodType;
use SDK\Core\Resources\Date;
use SDK\Core\Resources\Redis;
use SDK\Dtos\Basket\Basket;
use SDK\Dtos\Basket\BasketLockedStockTimers;
use SDK\Dtos\Catalog\Product\ProductComparison;
use SDK\Dtos\PaymentValidationResponse;
use SDK\Dtos\User\User;
use SDK\Enums\AddressType;
use SDK\Enums\BasketRowType;
use SDK\Enums\LicenseType;
use SDK\Enums\ListRowReferenceType;
use SDK\Services\Parameters\Groups\Product\ProductsParametersGroup;

/**
 * The Session class manages all the sets and gets stored in the PHP $_SESSION.
 *
 * This class has been created to manage the gets and the sets against $_SESSION ensuring certain data coordination for example between the API session and the PHP session.
 * Developers should manage all the session interactions through this specific Session class instead of directly using the $_SESSION PHP structure.
 * At initialization, if the user does not yet have a session, then empty objects are assigned to the session instance and will be used later to save the session information such as: user, visualization theme, product comparator,...
 *
 * @see Session::getInstance()
 * @see Session::start()
 * @see Session::reset()
 * @see Session::isInit()
 * @see Session::setBasketToken()
 * @see Session::getBasketToken()
 * @see Session::initRoute()
 * @see Session::getDefaultTheme()
 * @see Session::getGeneralSettings()
 * @see Session::isInitLastVersion()
 * @see Session::addBatchRequests()
 * @see Session::setUser()
 * @see Session::getUser()
 * @see Session::getSalesAgentUser()
 * @see Session::setAggregateDataFromApiRequest()
 * @see Session::setAggregateData()
 * @see Session::getAggregateData()
 *
 * @package FWK\Core\Resources
 */
class Session {

    private const INIT = 'init';

    private const INIT_THEME = 'initTheme';

    private const BASKET_TOKEN = 'basketToken';

    public const SESSION_AGGREGATE_DATA = 'sessionAggregateData';

    public const SHOPPING_LIST = 'shoppingList';

    public const GENERAL_SETTINGS = 'generalSettings';

    public const AGGREGATE_DATA = 'aggregateData';

    public const PRODUCT_COMPARISON = 'productComparison';

    public const USER = 'user';

    public const SALES_AGENT_USER = 'salesAgentUser';

    public const LOCKED_STOCKS_AGGREGATE_DATA = 'lockedStocksAggregateData';

    public const BASKET = 'basket';

    public const BASKET_GRID_PRODUCTS = 'basketGridProducts';

    public const ORDERS = 'orders';

    public const SESSION_VERSION = FWK_RELEASE_VERSION . '-' . SDK_RELEASE_VERSION . '-' . PLUGINS_RELEASE_VERSION;

    public const VALUES = 'values';

    public const ROUTE_WARNING_ACCEPTED = 'routeWarningAccepted';

    public const UPDATED_AT = 'updatedAt';

    public const SESSION_TOKEN = 'folcsId';

    public const WARNING = "warning";

    public const NAVIGATION_HASH = 'navigationHash';

    public const V_CACHE = 'v-cache';

    public const ASSOCIATED_ACCOUNTS = 'associatedAccounts';

    public const USE_DELIVERY_PICKING = 'useDeliveryPicking';

    private static $instance = null;

    private static $resetBasket = false;

    protected ?string $basketToken = null;

    protected ?Theme $defaultTheme = null;

    protected ?Route $defaultRoute = null;

    protected ?SessionGeneralSettings  $generalSettings = null;

    protected array  $basketGridProducts = [];

    protected ?Basket  $basket = null;

    protected ?User  $user = null;

    protected ?User  $salesAgentUser = null;

    protected array  $lockedStocksAggregateData = [];

    protected array $orders = [];

    protected ?SessionAggregateData $aggregateData = null;

    protected ?SessionShoppingList $shoppingList = null;

    protected array $values = [];

    protected bool $routeWarningAccepted = false;

    protected ?Date $updatedAt = null;

    protected array $warnings = [];

    protected ?string $navigationHash = null;

    protected bool $useDeliveryPicking = false;

    final private function __construct() {
    }

    /**
     * This method returns the instance of the session.
     *
     * @return self
     */
    final public static function getInstance(): Session {
        if (self::$instance === null) {
            self::setInstance();
        }
        return self::$instance;
    }

    private static function setInstance(): void {
        foreach (Loader::LOCATIONS as $location) {
            $class = Loader::getClassFQN('Session', $location . 'Core\\Resources\\', '');
            if (class_exists($class)) {
                self::$instance = new $class();
                return;
            }
        }
    }

    protected function setSessionProperties() {
        if (Redis::isEnabled() && LcFWK::getUseCacheRedisSession()) {
            session_set_save_handler(new RedisSessionHandler());
        }
        session_set_cookie_params(LcFWK::getLifeTimeSession(), '/', '.' . Connection::getHost(), Connection::isHttps(), true);
        session_set_cookie_params(["SameSite" => "None"]);
    }

    protected function startWritableSession(): bool {
        if (session_status() === 1) {
            $this->setSessionProperties();
            session_start();
            header_remove("Set-Cookie");
            return true;
        }
        return false;
    }

    protected function startReadSession() {
        try {
            $this->setSessionProperties();
            session_start(['read_and_close'  => true]);
        } catch (Exception | \Error $e) {
            new CommerceException('Session info reset. ' . $e->getMessage(), CommerceException::SESSION_INFO_RESET);
            if (Redis::isEnabled() && LcFWK::getUseCacheRedisSession()) {
                Redis::delete('SESSION' . Redis::KEY_SEPARATOR . Cookie::get(self::SESSION_TOKEN));
            }
            session_start(['read_and_close'  => true]);
        }
    }

    protected function commitSession(bool $initSelfProperties = true) {
        session_commit();
        $this->startReadSession();
        if ($initSelfProperties) {
            $this->initSelfProperties();
        }
    }

    /**
     * This method starts the session and returns true if all is ok.
     *
     * @return bool
     */
    public function start(): bool {
        $onError = callMethod(function () {
            $this->startReadSession();
            if (!$this->isInit()) {
                $storeURL = Loader::service(Services::ROUTE)->getStoreURL();
                $homeRoute = $storeURL['route'];
                $this->startWritableSession();
                if ($homeRoute->getStatus() === 200) {
                    $_SESSION[self::INIT_THEME] = self::SESSION_VERSION;
                    $route = $homeRoute;
                } else {
                    $route = new Route(DEFAULT_ROUTE);
                }
                $this->setDefaultGeneralSettings($storeURL);
                Theme::reloadInstance($route);
                $this->startSessionEvent();
                $_SESSION[self::INIT] = self::SESSION_VERSION;
                $this->commitSession(false);
            }
            $this->initSelfProperties();
            if (self::$resetBasket) {
                $this->loginReset();
            }
            $this->setNavigationHash();
        }, null, 'Session start', [], true);
        return $onError;
    }

    /**
     * This method runs when start the session.
     *
     * @return void
     */
    protected function startSessionEvent(): void {
    }

    /**
     * This method runs when start the session.
     *
     * @return void
     */
    protected function initSelfProperties(): void {
        $this->basketToken = isset($_SESSION[self::BASKET_TOKEN]) ? $_SESSION[self::BASKET_TOKEN] : null;
        $this->generalSettings = $_SESSION[self::GENERAL_SETTINGS];
        $this->defaultTheme = $this->getDefaultTheme();
        $this->defaultRoute = $this->getDefaultRoute();
        $this->basket = $_SESSION[self::BASKET];
        $this->user = $_SESSION[self::USER];
        $this->salesAgentUser = $_SESSION[self::SALES_AGENT_USER];
        $this->lockedStocksAggregateData = $_SESSION[self::LOCKED_STOCKS_AGGREGATE_DATA];
        $this->orders = (isset($_SESSION[self::ORDERS]) ? $_SESSION[self::ORDERS] : []);
        $this->aggregateData = isset($_SESSION[self::AGGREGATE_DATA]) ? $_SESSION[self::AGGREGATE_DATA] :  new SessionAggregateData();
        $this->values = (isset($_SESSION[self::VALUES]) ? $_SESSION[self::VALUES] : []);
        $this->shoppingList = $_SESSION[self::SHOPPING_LIST];
        $this->basketGridProducts = (isset($_SESSION[self::BASKET_GRID_PRODUCTS]) ? $_SESSION[self::BASKET_GRID_PRODUCTS] : []);
        $this->routeWarningAccepted = (isset($_SESSION[self::ROUTE_WARNING_ACCEPTED]) ? $_SESSION[self::ROUTE_WARNING_ACCEPTED] : false);
        $this->updatedAt = (isset($_SESSION[self::UPDATED_AT]) ? $_SESSION[self::UPDATED_AT] : null);
        $this->warnings = (isset($_SESSION[self::WARNING]) ? $_SESSION[self::WARNING] : []);
        $this->navigationHash = isset($_SESSION[self::NAVIGATION_HASH]) ? $_SESSION[self::NAVIGATION_HASH] : null;
        $this->useDeliveryPicking = isset($_SESSION[self::USE_DELIVERY_PICKING]) ? $_SESSION[self::USE_DELIVERY_PICKING] : false;
    }

    /**
     * This method login reset values
     *
     * @return void
     */
    public function loginReset(?Basket $basket = null): void {
        $doCommit = $this->startWritableSession();
        $storeURL = Loader::service(Services::ROUTE)->getStoreURL();
        $this->setDefaultGeneralSettings($storeURL);
        if (is_null($basket)) {
            Loader::service(Services::BASKET)->getBasket();
        } else {
            $this->setBasket($basket);
        }
        if (Utils::isUserLoggedIn($this->getUser())) {
            $this->setAggregateDataFromApiRequest();
        } else {
            $this->setAggregateData();
        }
        $this->setAssociatedAccounts(false);
        if ($doCommit) {
            $this->commitSession();
        }
    }

    /**
     * This method resets the session.
     *
     * @return void
     */
    public function reset(): void {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $this->destroySession();
        }
        self::$instance = null;
    }

    /**
     * This method returns true if the session has been initialized and false otherwise.
     *
     * @return bool
     */
    public function isInit(): bool {
        $basketToken = isset($_SESSION[self::BASKET_TOKEN]) ? $_SESSION[self::BASKET_TOKEN] : null;
        if (!self::isInitLastVersion()) {
            if (Cookie::exist(self::BASKET_TOKEN) && Cookie::get(self::BASKET_TOKEN) != $basketToken) {
                self::$resetBasket = true;
            }
            return false;
        } elseif ((Cookie::exist(self::BASKET_TOKEN) && Cookie::get(self::BASKET_TOKEN) != $basketToken) || (!Cookie::exist(self::BASKET_TOKEN) && !is_null($basketToken))
        ) {
            self::$resetBasket = true;
            return false;
        }
        return (isset($_SESSION[self::INIT]) && isset($_SESSION[self::INIT_THEME]));
    }

    /**
     * This method returns true if the last version of the session has been initialized and false otherwise.
     *
     * @return bool
     */
    public function isInitLastVersion(): bool {
        return (isset($_SESSION[self::INIT]) && $_SESSION[self::INIT] === self::SESSION_VERSION)
            && (isset($_SESSION[self::INIT_THEME]) && $_SESSION[self::INIT_THEME] === self::SESSION_VERSION);
    }

    /**
     * This method sets the basket token.
     *
     * @return void
     */
    public function setBasketToken(): void {
        $doCommit = $this->startWritableSession();
        if (Cookie::exist(self::BASKET_TOKEN)) {
            $_SESSION[self::BASKET_TOKEN] = Cookie::get(self::BASKET_TOKEN);
        } else {
            $_SESSION[self::BASKET_TOKEN] = null;
        }
        if ($doCommit) {
            $this->commitSession();
        }
    }

    /**
     * This method sets the navigationHash.
     *
     * @return void
     */
    public function setNavigationHash(): void {
        $doCommit = $this->startWritableSession();
        if (Cookie::exist(self::V_CACHE)) {
            $this->navigationHash = Cookie::get(self::V_CACHE);
            $_SESSION[self::NAVIGATION_HASH] = $this->navigationHash;
        } else {
            $_SESSION[self::NAVIGATION_HASH] = null;
        }
        if ($doCommit) {
            $this->commitSession();
        }
    }

    /**
     * This method returns session navigation hash
     *
     * @return null|string
     *
     */
    public function getNavigationHash(): ?string {
        if (!is_null($this->navigationHash) && strlen($this->navigationHash) > 0) {
            return $this->navigationHash;
        }
        $navigationHash = Cookie::get(self::V_CACHE);
        return (is_null($navigationHash) ? null : $navigationHash);
    }

    /**
     * This method sets the basket warning.
     *
     * @return void
     */
    public function addWarning(array $errorCode): void {
        $doCommit = $this->startWritableSession();
        if (!empty($errorCode)) {
            $_SESSION[self::WARNING][] = $errorCode;
            $this->warnings[] = $errorCode;
        }
        if ($doCommit) {
            $this->commitSession();
        }
    }

    /**
     * This method returns the session warning.
     *
     * @return string
     */
    public function getWarnings(): ?array {
        return $this->warnings;
    }

    /**
     * This method clears the basket warning.
     *
     * @return void
     */

    public function clearWarning(): void {
        $doCommit = $this->startWritableSession();
        $_SESSION[self::WARNING] = null;
        $this->setBasketToken();
        if ($doCommit) {
            $this->commitSession();
        }
    }

    /**
     * This method returns the basket token stored in the session (null if it is not set).
     *
     * @return string
     */
    public function getBasketToken(): ?string {
        return $this->basketToken;
    }

    private function getGenerialSettingsFromStoreURL(array $storeURL): array {
        $route = $storeURL['route'];
        $auxGeneralSettings = [];
        $auxGeneralSettings[SessionGeneralSettings::LOCALE] = Utils::calculateLocale($route->getCountry(), $route->getLanguage());
        $auxGeneralSettings[SessionGeneralSettings::CURRENCY] = $route->getCurrency();
        $auxGeneralSettings[SessionGeneralSettings::COUNTRY] = $route->getCountry();
        $auxGeneralSettings[SessionGeneralSettings::LANGUAGE] = $route->getLanguage();
        $auxGeneralSettings[SessionGeneralSettings::DEFAULT_AVAILABLE_LANGUAGES] = $route->getAvailableLanguages();
        $auxGeneralSettings[SessionGeneralSettings::DEFAULT_ROUTE] = $route;
        $auxGeneralSettings[SessionGeneralSettings::DEFAULT_THEME] = Theme::getInstance();
        $auxGeneralSettings[SessionGeneralSettings::STORE_URL] = $storeURL['storeURL'];
        return $auxGeneralSettings;
    }

    /**
     * This method sets the session route variables (LOCALE, CURRENCY, COUNTRY, LANGUAGE, DEFAULT_THEME, GENERAL_SETTINGS) based on the given Route instance.
     *
     * @param Route $route
     *
     * @return void
     */
    public function initRoute(Route $route): void {
        Language::reloadInstance($route->getLanguage());
        if (
            $this->getGeneralSettings()->getCurrency() != $route->getCurrency()
            || $this->getGeneralSettings()->getCountry() != $route->getCountry()
            || $this->getGeneralSettings()->getLanguage() != $route->getLanguage()
        ) {
            $this->initRouteHome($route);
        }
    }

    public function initRouteHome(?Route $route = null): void {
        $storeURL = Loader::service(Services::ROUTE)->getStoreURL($route);
        if (is_null($storeURL['route']->getError())) {
            $doCommit = $this->startWritableSession();
            $_SESSION[self::GENERAL_SETTINGS] = new SessionGeneralSettings($this->getGenerialSettingsFromStoreURL($storeURL));
            $this->generalSettings = $_SESSION[self::GENERAL_SETTINGS];
            $this->setBasketToken();
            $this->setProductComparison(Loader::service(Services::PRODUCT)->getProductComparison());
            if ($doCommit) {
                $this->commitSession();
            }
            $this->initRoute($storeURL['route']);
        }
    }

    protected function setDefaultGeneralSettings(array $storeURL): void {
        $_SESSION[self::GENERAL_SETTINGS] = new SessionGeneralSettings($this->getGenerialSettingsFromStoreURL($storeURL));
        $_SESSION[self::USER] = new User();
        $_SESSION[self::SALES_AGENT_USER] = null;
        $_SESSION[self::LOCKED_STOCKS_AGGREGATE_DATA] = [];
        $_SESSION[self::BASKET] = new Basket();
        $_SESSION[self::ORDERS] = [];
        $_SESSION[self::PRODUCT_COMPARISON] = new ProductComparison();
        $_SESSION[self::AGGREGATE_DATA] = new SessionAggregateData();
        $_SESSION[self::SHOPPING_LIST] = new SessionShoppingList();
        $_SESSION[self::BASKET_GRID_PRODUCTS] = [];
        $this->setBasketToken();
    }

    /**
     * This method returns the session default Theme.
     *
     * @return Theme
     */
    public function getDefaultTheme(): Theme {
        return $this->generalSettings->getDefaultTheme();
    }

    /**
     * This method returns the session default Route.
     *
     * @return Route
     */
    public function getDefaultRoute(): Route {
        return $this->generalSettings->getDefaultRoute();
    }

    /**
     * This method returns the session general settings.
     *
     * @return SessionGeneralSettings
     */
    public function getGeneralSettings(): SessionGeneralSettings {
        return $this->generalSettings;
    }

    protected function destroySession(): void {
        session_unset();
        session_destroy();
    }

    /**
     * This method sets the given basket to the session.
     *
     * @param Basket $basket
     *
     * @return void
     */
    public function setBasket(?Basket $basket = null): void {
        if ($basket === null) {
            $basket = new Basket();
        }

        $oldBasket = $this->getBasket();
        $oldLanguage = $oldBasket->getBasketUser()?->getUser()?->getLanguageCode();
        $newLanguage = $basket->getBasketUser()?->getUser()?->getLanguageCode();

        $countryNavigationAssignament = $this->getDefaultTheme()->getConfiguration()->getCommerce()->getCountryNavigationAssignament();
        $oldCountry = $oldBasket->getCustomer()?->getInvoicingAddress()?->getLocation()?->getGeographicalZone()?->getCountryCode();
        $newCountry = $basket->getCustomer()?->getInvoicingAddress()?->getLocation()?->getGeographicalZone()?->getCountryCode();

        if ($countryNavigationAssignament == Commerce::COUNTRY_NAVIGATION_ASSIGNAMENT_SHIPPING && $basket->getBasketUser()?->getUser()?->getUseShippingAddress() === true) {
            $oldCountry = $oldBasket->getCustomer()?->getShippingAddress()?->getLocation()?->getGeographicalZone()?->getCountryCode();
            $newCountry = $basket->getCustomer()?->getShippingAddress()?->getLocation()?->getGeographicalZone()?->getCountryCode();
        }

        $sameUseShippingAddress = $oldBasket->getCustomer()?->isUseShippingAddress() == $basket->getCustomer()?->isUseShippingAddress();

        $doCommit = $this->startWritableSession();

        $oldUserId = $this->getUser()->getId();
        $newUser = new User(is_null($basket->getBasketUser()) ? [] : $basket->getBasketUser()->toArray());
        if ($oldUserId != $newUser->getId()) {
            $this->setAggregateDataFromApiRequest();
        } else {
            $this->setAggregateDataBasket($basket);
        }
        $this->setUser($newUser);

        $this->setLockedStocksAggregateData($basket);

        if (!$this->getDefaultTheme()->getConfiguration()->getCommerce()->isDisableShowAsGridProductOptions()) {
            $this->setBasketGridProducts($basket);
        }

        $_SESSION[self::BASKET] = $basket;
        $this->basket = $basket;
        $this->setBasketToken();
        $this->setNavigationHash();
        if ($doCommit) {
            $this->commitSession();
        }

        $initRouteHome = false;
        if (
            !$this->useDeliveryPicking &&
            $countryNavigationAssignament != Commerce::COUNTRY_NAVIGATION_ASSIGNAMENT_NONE
            && ($oldCountry != $newCountry || !$sameUseShippingAddress)
            && !is_null($newCountry)
        ) {
            $setCountry = $this->setCountry($newCountry);
            if (is_null($setCountry->getError())) {
                $initRouteHome = true;
            }
        }

        if ($newLanguage != $oldLanguage || $initRouteHome) {
            $this->initRouteHome();
        }

        $oldCurrency = $oldBasket->getBasketUser()?->getUser()?->getCurrencyCode();
        $newCurrency = $basket->getBasketUser()?->getUser()?->getCurrencyCode();

        if ($newCurrency != $oldCurrency || $initRouteHome) {
            $this->initRouteHome();
        }

        $this->setUpdatedAt($oldBasket, $basket);
    }

    public function getCountry() {
        $basket = $this->getBasket();
        $countryCode = $basket->getCustomer()?->getInvoicingAddress()?->getLocation()?->getGeographicalZone()?->getCountryCode();
        $countryNavigationAssignament = $this->getDefaultTheme()->getConfiguration()->getCommerce()->getCountryNavigationAssignament();
        if ($countryNavigationAssignament == Commerce::COUNTRY_NAVIGATION_ASSIGNAMENT_SHIPPING && $basket->getBasketUser()?->getUser()?->getUseShippingAddress() === true) {
            $countryCode = $basket->getCustomer()?->getShippingAddress()?->getLocation()?->getGeographicalZone()?->getCountryCode();
        }
        return $countryCode;
    }

    public function setCountry(?String $newCountryCode = null): ?Basket {
        if (is_null($newCountryCode)) {
            $newCountryCode = $this->getCountry();
        }
        return Loader::service(Services::SESSION)->setCountry($newCountryCode);
    }
    /**
     * This method returns the session Basket.
     *
     * @return Basket
     */
    public function getBasket(): Basket {
        return $this->basket;
    }

    /**
     * This method sets the given user to the session.
     *
     * @param User $user
     *
     * @return void
     */
    public function setUser(?User $user = null): void {
        if ($user === null) {
            $user = new User();
        }
        $doCommit = $this->startWritableSession();
        $_SESSION[self::USER] = $user;
        $this->user = $user;
        if (Utils::isSalesAgent($this)) {
            $this->salesAgentUser = $user;
            $_SESSION[self::SALES_AGENT_USER] = $this->salesAgentUser;
        }
        if (Utils::isUserLoggedIn($this->getUser()) && !$this->getShoppingList()->isInit()) {
            $this->updateShoppingList();
        }
        $this->setBasketToken();
        $this->setNavigationHash();
        if ($doCommit) {
            $this->commitSession();
        }
    }

    /**
     * This method sets the basket locked stock data to the session.
     *
     * @param Basket $basket
     *
     * @return void
     */
    public function setLockedStocksAggregateData(Basket $basket): void {
        if (!LmsService::hasLicense(LicenseType::STCBL)) {
            return;
        }
        $doCommit = $this->startWritableSession();
        /** @var SettingsService $settingsService */
        $settingsService = Loader::service(Services::SETTINGS);
        if ($settingsService->getBasketStockLockingSettings()->getActive()) {
            $_SESSION[self::LOCKED_STOCKS_AGGREGATE_DATA] = $basket->getLockedStockTimers();
            $this->lockedStocksAggregateData = $basket->getLockedStockTimers();
        } else {
            $_SESSION[self::LOCKED_STOCKS_AGGREGATE_DATA] = [];
            $this->lockedStocksAggregateData = [];
        }
        $this->setBasketToken();
        if ($doCommit) {
            $this->commitSession();
        }
    }

    /**
     * This method update Locked Stocks AggregateData to the session.
     *
     * @param Basket $basket
     *
     * @return void
     */
    public function updateLockedStocksAggregateData(BasketLockedStockTimers $newLockedStockTimers): void {
        if (!LmsService::hasLicense(LicenseType::STCBL)) {
            return;
        }
        $lockedStockTimers = $this->lockedStocksAggregateData;
        foreach ($lockedStockTimers as $key => $lockedStockTimer) {
            if ($lockedStockTimer->getUid() == $newLockedStockTimers->getUid()) {
                $lockedStockTimers[$key] = $newLockedStockTimers;
                break;
            }
        }
        $doCommit = $this->startWritableSession();
        $_SESSION[self::LOCKED_STOCKS_AGGREGATE_DATA] = $lockedStockTimers;
        $this->lockedStocksAggregateData = $lockedStockTimers;
        $this->setBasketToken();
        if ($doCommit) {
            $this->commitSession();
        }
    }

    /**
     * This method returns the session User.
     *
     * @return User
     */
    public function getUser(): User {
        return $this->user;
    }

    /**
     * This method returns the session Sales Agent User.
     *
     * @return null|User
     */
    public function getSalesAgentUser(): ?User {
        return $this->salesAgentUser;
    }

    /**
     * This method returns the session locked stocks aggregate data
     *
     * @return array
     */
    public function getLockedStocksAggregateData(): array {
        if (!LmsService::hasLicense(LicenseType::STCBL)) {
            return [];
        }
        return $this->lockedStocksAggregateData;
    }

    /**
     * This method sets the given ProductComparison to the session.
     *
     * @param ProductComparison $productComparison
     *
     * @return void
     */
    public function setProductComparison(ProductComparison $productComparison): void {
        $doCommit = $this->startWritableSession();
        $_SESSION[self::PRODUCT_COMPARISON] = $productComparison;
        $this->setBasketToken();
        if ($doCommit) {
            $this->commitSession();
        }
    }

    /**
     * This method returns the session ProductComparison.
     *
     * @return ProductComparison
     */
    public function getProductComparison(): ProductComparison {
        return $_SESSION[self::PRODUCT_COMPARISON];
    }

    /**
     * This method add a order to session.
     *
     * @param PaymentValidationResponse $pvr
     *
     * @return void
     */
    public function addOrder(PaymentValidationResponse $pvr): void {
        $doCommit = $this->startWritableSession();
        $_SESSION[self::ORDERS][$pvr->getId()] = $pvr;
        if ($doCommit) {
            $this->commitSession();
        }
    }

    /**
     * This method returns the session order, by given id.
     *
     * @param int $id     
     *
     * @return User
     */
    public function getOrder(int $id): ?PaymentValidationResponse {
        return $_SESSION[self::ORDERS][$id] ?? null;
    }

    /**
     * This method returns the session orders.
     *
     * @return array
     */
    public function getOrders(): array {
        return $this->orders;
    }

    /**
     * This method sets the aggregate data of the session generating a request of this data to the API to obtain an update of them and setting them to the session.
     *
     * @return void
     */
    public function setAggregateDataFromApiRequest(): void {
        if ($this->getBasketToken() !== null) {
            $this->setAggregateData(Loader::service(Services::SESSION)->getAggregateData());
        } else {
            $this->setAggregateData();
        }
    }

    /**
     * This method sets the given SessionAggregateData to the session.
     *
     * @param SessionAggregateData $sessionAggregateData
     *
     * @return void
     */
    public function setAggregateData(?SessionAggregateData $sessionAggregateData = null): void {
        $doCommit = $this->startWritableSession();
        if ($sessionAggregateData !== null) {
            $_SESSION[self::AGGREGATE_DATA] = $sessionAggregateData;
        } else {
            $_SESSION[self::AGGREGATE_DATA] = new SessionAggregateData(
                [
                    'basket' => ['products' => 0, 'gifts' => 0, 'totalProducts' => 0, 'bundles' => 0, 'total' => 0],
                    'wishlist' => ['items' => 0, 'itemIdList' => []],
                    'shoppingLists' => ['defaultOne' => ['products' => 0, 'productIdList' => [], 'bundles' => 0, 'bundleIdList' => []]],
                    'productComparison' => ['items' => 0, 'itemIdList' => []]
                ]
            );
        }
        $this->setBasketToken();
        if ($doCommit) {
            $this->commitSession();
        }
    }

    /**
     * This method sets Bastek items to the session's SessionAggregateData.
     *
     * @param Basket $basket
     *
     * @return void
     */
    public function setAggregateDataBasket(Basket $basket): void {
        $data = $this->getAggregateData()->toArray();
        $products = 0;
        $gifts = 0;
        $bundles = 0;
        $totalProducts = 0;
        foreach ($basket->getItems() as $item) {
            if ($item->getType() === BasketRowType::BUNDLE) {
                $bundles += $item->getQuantity();
                foreach ($item->getItems() as $bundleItem) {
                    $totalProducts += $bundleItem->getQuantity();
                }
            } elseif ($item->getType() === BasketRowType::GIFT) {
                $gifts += $item->getQuantity();
            } elseif (
                $item->getType() === BasketRowType::PRODUCT
                || $item->getType() === BasketRowType::LINKED
                || $item->getType() === BasketRowType::VOUCHER_PURCHASE
            ) {
                $products += $item->getQuantity();
            }
        }
        $totalProducts += $gifts + $products;
        $data['basket'] = [
            'products' => $products,
            'gifts' => $gifts,
            'bundles' => $bundles,
            'totalProducts' => $totalProducts,
            'total' => is_null($basket->getTotals()) ? 0 : $basket->getTotals()->getTotal()
        ];
        $this->setAggregateData(new SessionAggregateData($data));
    }

    /**
     * This method sets ShoppingLists items to the session's SessionAggregateData.
     *
     * @param Basket $basket
     *
     * @return void
     */
    public function setAggregateDataShoppingLists(int $id, string $type, string $action): void {
        if (!ListRowReferenceType::isValid($type)) {
            return;
        }
        $type = strtolower($type);
        $data = $this->getAggregateData()->toArray();
        if ($action === MethodType::POST) {
            $data['shoppingLists']['defaultOne'][$type . 'IdList'][] = $id;
        } elseif ($action === MethodType::DELETE) {
            $index = array_search($id, $data['shoppingLists']['defaultOne'][$type . 'IdList']);
            if ($index !== false && $index >= 0) {
                unset($data['shoppingLists']['defaultOne'][$type . 'IdList'][$index]);
            }
        }
        $data['shoppingLists']['defaultOne'][$type . 's'] = count($data['shoppingLists']['defaultOne'][$type . 'IdList']);
        $this->setAggregateData(new SessionAggregateData($data));
    }

    /**
     * This method sets ProductComparison items to the session's SessionAggregateData.
     *
     * @param Basket $basket
     *
     * @return void
     */
    public function setAggregateDataProductComparison(int $id, string $action): void {
        $data = $this->getAggregateData()->toArray();
        if ($action === MethodType::POST) {
            $data['productComparison']['itemIdList'][] = $id;
        } elseif ($action === MethodType::DELETE) {
            $index = array_search($id, $data['productComparison']['itemIdList']);
            if ($index !== false && $index >= 0) {
                unset($data['productComparison']['itemIdList'][$index]);
            }
        }
        $data['productComparison']['items'] = count($data['productComparison']['itemIdList']);
        $this->setAggregateData(new SessionAggregateData($data));
        $this->setProductComparison(Loader::service(Services::PRODUCT)->getProductComparison());
    }

    /**
     * This method returns the session aggregate data.
     *
     * @return SessionAggregateData
     */
    public function getAggregateData(): SessionAggregateData {
        return $this->aggregateData;
    }


    /**
     * This method returns the session shopping list.
     *
     * @return SessionShoppingList
     */
    public function getShoppingList(): SessionShoppingList {
        if (Utils::isSessionLoggedIn($this) && !$this->shoppingList->isInit()) {
            $this->updateShoppingList();
        }
        return $this->shoppingList;
    }

    /**
     * This method add session value.
     *
     * @param string $key
     * @param mixed $value
     * @param bool $override
     * @return void
     */
    final public function addValue(string $key, mixed $value, bool $override = true): void {
        if (isset($_SESSION[self::VALUES][$key]) && $override == false) {
            throw new CommerceException("The key '" . $key . "' is already exists.", CommerceException::SESSION_KEY_ALREADY_EXISTS);
        }
        $doCommit = $this->startWritableSession();
        $_SESSION[self::VALUES][$key] = $value;
        if ($doCommit) {
            $this->commitSession();
        }
    }

    /**
     * This method return session add value by given key.
     *
     * @param string $key
     * 
     * @return mixed
     * 
     */
    final public function getValue(string $key): mixed {
        if (isset($_SESSION[self::VALUES][$key])) {
            return $_SESSION[self::VALUES][$key];
        }
        return null;
    }

    /**
     * This method return session add values.
     *
     * @return array
     */
    final public function getValues() {
        return $this->values;
    }

    /**
     * This method resets the saved information about user shoppingLists.
     *
     * @return array
     */
    public function resetShoppingList(): void {
        $doCommit = $this->startWritableSession();
        $_SESSION[self::SHOPPING_LIST] = new SessionShoppingList();
        $this->setBasketToken();
        if ($doCommit) {
            $this->commitSession();
        }
    }

    /**
     * This method sets the given user to the session.
     *
     * @param User $user
     *
     * @return void
     */
    public function updateShoppingList(ElementCollection $shoppingLists = null): void {
        $doCommit = $this->startWritableSession();
        if (is_null($shoppingLists)) {
            $this->shoppingList->init();
        } else {
            $this->shoppingList->setShoppingLists($shoppingLists);
        }
        $_SESSION[self::SHOPPING_LIST] = $this->shoppingList;
        $this->setBasketToken();
        if ($doCommit) {
            $this->commitSession();
        }
    }

    /**
     * This method sets the basket products that must be show as grid
     *
     * @param Basket $basket
     *
     * @return void
     */
    protected function setBasketGridProducts(Basket $basket): void {
        $doCommit = $this->startWritableSession();

        $newProducts = [];
        foreach ($basket->getItems() as $item) {
            if ($item->getType() == BasketRowType::PRODUCT) {
                if (!array_key_exists($item->getId(), $this->basketGridProducts)) {
                    $newProducts[] = $item->getId();
                }
            }
        }

        if (!empty($newProducts)) {
            $productsParametersGroup = new ProductsParametersGroup();
            $productsParametersGroup->setIdList(implode(',', $newProducts));
            $productsParametersGroup->setPerPage(100);
            $newProducts = Loader::service(Services::PRODUCT)->getProducts($productsParametersGroup);
            foreach ($newProducts->getItems() as $newProduct) {
                $this->basketGridProducts[$newProduct->getId()] = new BasketGridProduct($newProduct);
            }
        }

        foreach ($this->basketGridProducts as $basketGridProduct)
            if (!empty($basketGridProduct->getCombinations()))
                $basketGridProduct->resetCombinations();


        foreach ($basket->getItems() as $item) {
            if ($item->getType() == BasketRowType::PRODUCT && !empty($this->basketGridProducts[$item->getId()]?->getCombinations())) {
                $this->basketGridProducts[$item->getId()]->updateCombination($item);
            }
        }

        foreach ($this->basketGridProducts as $basketGridProduct)
            if (!empty($basketGridProduct->getCombinations()))
                $basketGridProduct->setRows();

        $_SESSION[self::BASKET_GRID_PRODUCTS] = $this->basketGridProducts;
        $this->setBasketToken();
        if ($doCommit) {
            $this->commitSession();
        }
    }

    /**
     * This method returns the basket products that must be show as grid
     *
     * @return array
     */
    public function getBasketGridProducts(): array {
        return $this->basketGridProducts;
    }

    /**
     * This method accept route warning
     *
     * @return void
     */
    public function acceptRouteWarning(): void {
        $doCommit = $this->startWritableSession();
        $_SESSION[self::ROUTE_WARNING_ACCEPTED] = true;
        $this->routeWarningAccepted = $_SESSION[self::ROUTE_WARNING_ACCEPTED];
        if ($doCommit) {
            $this->commitSession();
        }
    }

    /**
     * This method returns route accepted
     *
     * @return bool
     * 
     */
    public function getRouteWarningAccepted(): bool {
        return $this->routeWarningAccepted;
    }

    /**
     * This method sets update at
     *
     * @param Basket $newBasket
     * @param Basket $oldBasket
     * 
     * @return void
     */
    public function setUpdatedAt(Basket $oldBasket, Basket $newBasket): void {
        $arrOldBasket = $oldBasket->toArray();
        unset($arrOldBasket['updatedAt']);
        $arrNewBasket = $newBasket->toArray();
        unset($arrNewBasket['updatedAt']);
        if (serialize($arrOldBasket) != serialize($arrNewBasket)) {
            // To avoid doble updateLockedStockTimer 
            if (LmsService::hasLicense(LicenseType::STCBL)) {
                /** @var BasketService $basketService */
                $basketService = Loader::service(Services::BASKET);
                $basketService->extendLockedStockTimer(
                    CommerceLockedStock::EXTEND_BY_BASKET_CHANGE,
                    $this->getDefaultTheme()->getConfiguration()->getCommerce()->getLockedStock()
                );
            }
            $doCommit = $this->startWritableSession();
            $_SESSION[self::UPDATED_AT] = $newBasket->getUpdatedAt();
            $this->updatedAt = $_SESSION[self::UPDATED_AT];
            if ($doCommit) {
                $this->commitSession();
            }
        }
    }

    /**
     * This method returns update at
     *
     * @return Date|NULL
     * 
     */
    public function getUpdatedAt(): ?Date {
        return $this->updatedAt;
    }

    /**
     * This method sets the associatedAccounts.
     * 
     * @param bool $associatedAccounts
     *
     * @return void
     */
    public function setAssociatedAccounts($associatedAccounts = true): void {
        $doCommit = $this->startWritableSession();
        $_SESSION[self::ASSOCIATED_ACCOUNTS] = $associatedAccounts;
        if ($doCommit) {
            $this->commitSession();
        }
    }

    /**
     * This method returns the associatedAccounts.
     *
     * @return bool
     *
     */
    public function getAssociatedAccounts(): ?bool {
        return $_SESSION[self::ASSOCIATED_ACCOUNTS];
    }

    /**
     * This method sets use delivery picking
     *
     * @return void
     */
    public function setUseDeliveryPicking(bool $useDeliveryPicking): void {
        $doCommit = $this->startWritableSession();
        $_SESSION[self::USE_DELIVERY_PICKING] = $useDeliveryPicking;
        $this->useDeliveryPicking = $_SESSION[self::USE_DELIVERY_PICKING];
        if ($doCommit) {
            $this->commitSession();
        }
    }

    /**
     * This method returns use delivery picking
     *
     * @return bool
     * 
     */
    public function getUseDeliveryPicking(): bool {
        return $this->useDeliveryPicking;
    }
}
