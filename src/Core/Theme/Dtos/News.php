<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Core\Dtos\Element;

/**
 * This is the 'News' class, a DTO class for the theme configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see News::getNewsList()
 *
 * @see Element
 * @see ElementTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class News extends Element {
    use ElementTrait;
    
    public const NEWS_LIST = 'newsList';
    
    private ?ItemList $newsList = null;
    
    /**
     * This method returns the newsList configuration.
     *
     * @return ItemList|NULL
     */
    public function getNewsList(): ?ItemList {
        return $this->newsList;
    }
    
    private function setNewsList(array $newsList): void {
        $this->newsList = new ItemList($newsList);
    }
    
}
