<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Traits\ElementTrait;

/**
 * This is the 'Discounts' class, a DTO class for the theme configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see Discounts::getDiscountsList()
 *
 * @see Element
 * @see ElementTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class Discounts extends ProductList {
    use ElementTrait;

    public const DISCOUNTS_LIST = 'discountsList';

    private ?ItemList $discountsList = null;

    /**
     * This method returns the discountsList configuration.
     *
     * @return ItemList|NULL
     */
    public function getDiscountsList(): ?ItemList {
        return $this->discountsList;
    }

    private function setDiscountsList(array $discountsList): void {
        $this->discountsList = new ItemList($discountsList);
    }
}
