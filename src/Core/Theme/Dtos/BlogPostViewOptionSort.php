<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Traits\ElementTrait;

/**
 * This is the 'BlogPostViewOptionSort' class, a DTO class for the theme configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see ViewOptionTemplate::getAvailableTemplates()
 *
 * @see ViewOption
 * 
 * @see ElementTrait
 *
 * @package FWK\Core\Theme\Dtos
 */

class BlogPostViewOptionSort extends ViewOption {
    use ElementTrait;

    public const SORT_ITEMS = 'BlogPost';

    public const ITEMS = 'items';

    private ?BlogPostViewOptionSortItems $items = null;

    /**
     * This method returns the available items to show. 
     *
     * @return BlogPostViewOptionSortItems|NULL
     */
    public function getItems(): ?BlogPostViewOptionSortItems {
        return $this->items;
    }

    private function setItems(array $items): void {
        $this->items = new BlogPostViewOptionSortItems($items);
    }
}
