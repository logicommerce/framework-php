<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Core\Dtos\Element;

/**
 * This is the 'SearchItem' class, a DTO class for the theme configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see SearchItem::isActived()
 * @see SearchItem::getList()
 *
 * @see Element
 * @see ElementTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class SearchItem extends Element {
    use ElementTrait;

    public const ACTIVED = 'actived';
    
    public const LIST = 'list';
    
    private bool $actived = false;
    
    private ?ItemList $list = null;

    /**
     * This method returns true if the search item is activated, false otherwise.
     * 
     * @return bool
     */
    public function isActived(): bool {
        return $this->actived;
    }

    private function setActived(bool $actived): void {
        $this->actived = $actived;
    }
    
    private function setRequestParameyters(array $requestParameters): void {
        $this->requestParameters = $requestParameters;
    }
    
    /**
     * This method returns the ItemList with the configuration to be applied to list the items.
     * 
     * @return ItemList|NULL
     * 
     * @see ItemList
     */
    public function getList():?ItemList {
        return $this->list;
    }
    
    private function setList(array $list): void {
        $this->list = new ItemList($list);
    }
    
}
