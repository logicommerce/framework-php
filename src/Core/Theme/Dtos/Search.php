<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Core\Dtos\Element;

/**
 * This is the 'Search' class, a DTO class for the theme configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see Search::getProduct()
 * @see Search::getCategories()
 * @see Search::getNews()
 * @see Search::getPages()
 *
 * @see Element
 * @see ElementTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class Search extends Element {
    use ElementTrait;

    public const PRODUCTS = 'products';

    public const CATEGORIES = 'categories';

    public const NEWS = 'news';

    public const PAGES = 'pages';

    private ?SearchItem $products = null;

    private ?SearchItem $categories = null;

    private ?SearchItem $news = null;

    private ?SearchItem $pages = null;

    /**
     * This method returns the products search configuration.
     * 
     * @return SearchItem|NULL
     */
    public function getProducts(): ?SearchItem {
        return $this->products;
    }

    private function setProducts(array $products): void {
        $this->products = new SearchItem($products);
    }
    
    /**
     * This method returns the categories search configuration.
     *
     * @return SearchItem|NULL
     */
    public function getCategories(): ?SearchItem {
        return $this->categories;
    }
    
    private function setCategories(array $categories): void {
        $this->categories = new SearchItem($categories);
    }
    
    /**
     * This method returns the news search configuration.
     *
     * @return SearchItem|NULL
     */
    public function getNews(): ?SearchItem {
        return $this->news;
    }
    
    private function setNews(array $news): void {
        $this->news = new SearchItem($news);
    }
    
    /**
     * This method returns the pages search configuration.
     *
     * @return SearchItem|NULL
     */
    public function getPages(): ?SearchItem {
        return $this->pages;
    }
    
    private function setPages(array $pages): void {
        $this->pages = new SearchItem($pages);
    }
}
