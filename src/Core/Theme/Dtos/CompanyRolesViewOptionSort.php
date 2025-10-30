<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Traits\ElementTrait;

/**
 * This is the 'CompanyRolesViewOptionSort' class, a DTO class for the theme configuration data.
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
class CompanyRolesViewOptionSort extends ViewOption {
    use ElementTrait;
    public const SORT_ITEMS = 'CompanyRoles';
    public const ITEMS = 'items';
    private ?CompanyRolesViewOptionSortItems $items = null;
    /**
     * This method returns the available items to show. 
     *
     * @return CompanyRolesViewOptionSortItems|NULL
     */
    public function getItems(): ?CompanyRolesViewOptionSortItems {
        return $this->items;
    }
    private function setItems(array $items): void {
        $this->items = new CompanyRolesViewOptionSortItems($items);
    }
}
