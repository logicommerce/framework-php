<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Traits\ElementTrait;

/**
 * This is the 'ViewOptionSort' class, a DTO class for the theme configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see ViewOptionTemplate::getAvailableTemplates()
 *
 * @see ElementTrait
 *
 * @package FWK\Core\Theme\Dtos
 */

class ViewOptionSort extends ViewOption {
    use ElementTrait;

    public const SORT_ITEMS = 'sortItems';

    public const ITEMS = 'items';

    private ?ViewOptionSortItems $items = null;

    /**
     * This method returns the available items to show. 
     *
     * @return ViewOptionSortItems|NULL
     */
    public function getItems(): ?ViewOptionSortItems {
        return $this->items;
    }

    private function setItems(array $items): void {
        $this->items = new ViewOptionSortItems($items);
    }
}
