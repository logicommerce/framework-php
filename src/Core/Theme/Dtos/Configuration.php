<?php

namespace FWK\Core\Theme\Dtos;

use FWK\Core\Resources\Loader;
use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Core\Dtos\Element;

/**
 * This is the 'Configuration' class, a DTO class for the theme configuration data.
 * The configuration items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see Configuration::getAccount()
 * @see Configuration::getBasket()
 * @see Configuration::getBlog()
 * @see Configuration::getBrand()
 * @see Configuration::getCategory()
 * @see Configuration::getCommerce()
 * @see Configuration::getDiscounts()
 * @see Configuration::getEvents()
 * @see Configuration::getFeatured()
 * @see Configuration::getForms()
 * @see Configuration::getNews()
 * @see Configuration::getOffers()
 * @see Configuration::getPages()
 * @see Configuration::getSearch()
 * @see Configuration::getUser()
 *
 * @see Element
 * @see ElementTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class Configuration extends Element {
    use ElementTrait;

    public const ACCOUNT = 'account';

    public const SEARCH = 'search';

    public const CATEGORY = 'category';

    public const BRAND = 'brand';

    public const OFFERS = 'offers';

    public const FEATURED = 'featured';

    public const NEWS = 'news';

    public const FORMS = 'forms';

    public const EVENTS = 'events';

    public const PAGES = 'pages';

    public const BLOG = 'blog';

    public const USER = 'user';

    public const BASKET = 'basket';

    public const COMMERCE = 'commerce';

    public const DATA_VALIDATORS = 'dataValidators';

    public const WISHLIST = 'wishlist';

    public const SALES_AGENT_CUSTOMERS = 'salesAgentCustomers';

    public const SHOPPING_LIST = 'shoppingList';

    public const ORDER_LIST = 'orderList';

    public const DISCOUNTS = 'discounts';

    private ?Search $search = null;

    private ?Category $category = null;

    private ?Brand $brand = null;

    private ?Offers $offers = null;

    private ?Featured $featured = null;

    private ?News $news = null;

    private ?Forms $forms = null;

    private ?Events $events = null;

    private ?Pages $pages = null;

    private ?Blog $blog = null;

    private ?User $user = null;

    private ?Basket $basket = null;

    private ?Commerce $commerce = null;

    private ?DataValidators $dataValidators = null;

    private ?Wishlist $wishlist = null;

    private ?ShoppingList $shoppingList = null;

    private ?OrderList $orderList = null;

    private ?Discounts $discounts = null;

    private ?Account $account = null;

    private ?SalesAgentCustomers $salesAgentCustomers = null;

    /**
     * This method returns the 'search' configuration.
     *
     * @return Search|NULL
     *
     * @see Search
     */
    public function getSearch(): ?Search {
        return $this->search;
    }

    private function setSearch(array $search): void {
        $this->search = new Search($search);
    }

    /**
     * This method returns the 'category' configuration.
     *
     * @return Category|NULL
     *
     * @see Category
     */
    public function getCategory(): ?Category {
        return $this->category;
    }

    private function setCategory(array $category): void {
        $this->category = new Category($category);
    }

    /**
     * This method returns the 'brand' configuration.
     *
     * @return Brand|NULL
     *
     * @see Brand
     */
    public function getBrand(): ?Brand {
        return $this->brand;
    }

    private function setBrand(array $brand): void {
        $this->brand = new Brand($brand);
    }

    /**
     * This method returns the 'offers' configuration.
     *
     * @return Offers|NULL
     *
     * @see Offers
     */
    public function getOffers(): ?Offers {
        return $this->offers;
    }

    private function setOffers(array $offers): void {
        $this->offers = new Offers($offers);
    }

    /**
     * This method returns the 'featured' configuration.
     *
     * @return Featured|NULL
     *
     * @see Featured
     */
    public function getFeatured(): ?Featured {
        return $this->featured;
    }

    private function setFeatured(array $featured): void {
        $this->featured = new Featured($featured);
    }

    /**
     * This method returns the 'news' configuration.
     *
     * @return News|NULL
     *
     * @see News
     */
    public function getNews(): ?News {
        return $this->news;
    }

    private function setNews(array $news): void {
        $this->news = new News($news);
    }

    /**
     * This method returns the 'forms' configuration.
     *
     * @return Forms|NULL
     *
     * @see Forms
     */
    public function getForms(): ?Forms {
        return $this->forms;
    }

    private function setForms(array $forms): void {
        $this->forms = new Forms($forms);
    }

    /**
     * This method returns the 'events' configuration.
     *
     * @return Events|NULL
     *
     * @see Events
     */
    public function getEvents(): ?Events {
        return $this->events;
    }

    private function setEvents(array $events): void {
        $this->events = new Events($events);
    }

    /**
     * This method returns the 'pages' configuration.
     *
     * @return Pages|NULL
     *
     * @see Pages
     */
    public function getPages(): ?Pages {
        return $this->pages;
    }

    private function setPages(array $pages): void {
        $this->pages = new Pages($pages);
    }

    /**
     * This method returns the 'blog' configuration.
     *
     * @return Blog|NULL
     *
     * @see Blog
     */
    public function getBlog(): ?Blog {
        return $this->blog;
    }

    private function setBlog(array $blog): void {
        $this->blog = new Blog($blog);
    }

    /**
     * This method returns the 'user' configuration.
     *
     * @return User|NULL
     *
     * @see User
     */
    public function getUser(): ?User {
        return $this->user;
    }

    private function setUser(array $user): void {
        $this->user = new User($user);
    }

    /**
     * This method returns the 'basket' configuration.
     *
     * @return Basket|NULL
     *
     * @see Basket
     */
    public function getBasket(): ?Basket {
        return $this->basket;
    }

    private function setBasket(array $basket): void {
        $this->basket = new Basket($basket);
    }

    /**
     * This method returns the 'commerce' configuration.
     *
     * @return Commerce|NULL
     *
     * @see Commerce
     */
    public function getCommerce(): ?Commerce {
        return $this->commerce;
    }

    private function setCommerce(array $commerce): void {
        foreach (Loader::LOCATIONS as $location) {
            $class = Loader::getClassFQN('Commerce', $location . 'Core\\Theme\\Dtos\\', '');
            if (class_exists($class)) {
                $this->commerce = new $class($commerce);
                return;
            }
        }
    }

    /**
     * This method returns the 'dataValidators' configuration.
     *
     * @return DataValidators|NULL
     *
     * @see DataValidators
     */
    public function getDataValidators(): ?DataValidators {
        return $this->dataValidators;
    }

    private function setDataValidators(array $dataValidators): void {
        $this->dataValidators = new DataValidators($dataValidators);
    }

    /**
     * This method returns the 'wishlist' configuration.
     *
     * @return Wishlist|NULL
     *
     * @see Wishlist
     * @deprecated
     */
    public function getWishlist(): ?Wishlist {
        // trigger_error("The function 'getWishlist' will be deprecated soon. you must use 'getShoppingList'", E_USER_NOTICE);
        return $this->wishlist;
    }

    private function setWishlist(array $wishlist): void {
        // trigger_error("The function 'setWishlist' will be deprecated soon. you must use 'setShoppingList'", E_USER_NOTICE);
        $this->wishlist = new Wishlist($wishlist);
    }

    /**
     * This method returns the 'shoppingList' configuration.
     *
     * @return ShoppingList|NULL
     *
     * @see ShoppingList
     */
    public function getShoppingList(): ?ShoppingList {
        return $this->shoppingList;
    }

    private function setShoppingList(array $shoppingList): void {
        $this->shoppingList = new ShoppingList($shoppingList);
    }

    /**
     * This method returns the 'orderList' configuration.
     *
     * @return OrderList|NULL
     *
     * @see OrderList
     */
    public function getOrderList(): ?OrderList {
        return $this->orderList;
    }

    private function setOrderList(array $orderList): void {
        $this->orderList = new OrderList($orderList);
    }

    /**
     * This method returns the 'discounts' configuration.
     *
     * @return Discounts|NULL
     *
     * @see Discounts
     */
    public function getDiscounts(): ?Discounts {
        return $this->discounts;
    }

    private function setDiscounts(array $discounts): void {
        $this->discounts = new Discounts($discounts);
    }

    /**
     * This method returns the 'account' configuration.
     *
     * @return Account|NULL
     *
     * @see Account
     */
    public function getAccount(): ?Account {
        return $this->account;
    }
    private function setAccount(array $account): void {
        $this->account = new Account($account);
    }

    /**
     * This method returns the 'salesAgentCustomers' configuration.
     *
     * @return SalesAgentCustomers|NULL
     *
     * @see SalesAgentCustomers
     */
    public function getSalesAgentCustomers(): ?SalesAgentCustomers {
        return $this->salesAgentCustomers;
    }

    private function setSalesAgentCustomers(array $salesAgentCustomers): void {
        $this->salesAgentCustomers = new SalesAgentCustomers($salesAgentCustomers);
    }
}
