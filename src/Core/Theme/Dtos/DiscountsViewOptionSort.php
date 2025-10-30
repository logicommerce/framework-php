<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Traits\ElementTrait;

/**
 * This is the 'DiscountsViewOptionSort' class, a DTO class for the theme configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see ViewOptionTemplate::getAvailableTemplates()
 *
 * @see ViewOption
 * 
 * @uses ElementTrait
 *
 * @package FWK\Core\Theme\Dtos
 */

class DiscountsViewOptionSort extends ViewOption {
    use ElementTrait;

    public const SORT_ITEMS = 'Discounts';

    public const ITEMS = 'items';

    private ?DiscountsViewOptionSortItems $items = null;

    /**
     * This method returns the available items to show. 
     *
     * @return DiscountsViewOptionSortItems|NULL
     */
    public function getItems(): ?DiscountsViewOptionSortItems {
        return $this->items;
    }

    private function setItems(array $items): void {
        $this->items = new DiscountsViewOptionSortItems($items);
    }
}
