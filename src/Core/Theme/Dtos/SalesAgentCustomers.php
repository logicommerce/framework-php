<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Element;
use SDK\Core\Dtos\Traits\ElementTrait;

/**
 * This is the 'SalesAgentCustomers' class, a DTO class for the theme configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends FWK\Core\Theme\Dtos\ProductList, see this class.
 *
 * @see Element
 * @see ElementTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class SalesAgentCustomers extends Element {
    use ElementTrait;

    public const ROWS_LIST = 'rowsList';

    private ?ItemList $rowsList = null;

    /**
     * This method returns the rowsList configuration.
     *
     * @return ItemList|NULL
     */
    public function getRowsList(): ?ItemList {
        return $this->rowsList;
    }

    private function setRowsList(array $rowsList): void {
        $this->rowsList = new ItemList($rowsList);
    }
}
