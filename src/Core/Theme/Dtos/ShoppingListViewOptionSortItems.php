<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Element;
use SDK\Core\Dtos\Traits\ElementTrait;

/**
 * This is the 'ShoppingListViewOptionSortItems' class, a DTO class for the theme configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see ViewOption::getAvailableTemplates()
 *
 * @see Element
 * 
 * @uses ElementTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class ShoppingListViewOptionSortItems extends Element {
    use ElementTrait;

    public const NAME = 'name';

    public const PRIORITY = 'priority';

    public const ADDEDDATE = 'addeddate';

    private ?ViewOptionSortItem $name = null;

    private ?ViewOptionSortItem $priority = null;

    private ?ViewOptionSortItem $addeddate = null;

    /**
     * This method returns name sort configuration.
     *
     * @return ViewOptionSortItem|NULL
     */
    public function getName(): ?ViewOptionSortItem {
        return $this->name;
    }

    private function setName(array $name): void {
        $this->name = new ViewOptionSortItem($name);
    }

    /**
     * This method returns priority sort configuration.
     *
     * @return ViewOptionSortItem|NULL
     */
    public function getPriority(): ?ViewOptionSortItem {
        return $this->priority;
    }

    private function setPriority(array $priority): void {
        $this->priority = new ViewOptionSortItem($priority);
    }

    /**
     * This method returns addeddate sort configuration.
     *
     * @return ViewOptionSortItem|NULL
     */
    public function getAddeddate(): ?ViewOptionSortItem {
        return $this->addeddate;
    }

    private function setAddeddate(array $addeddate): void {
        $this->addeddate = new ViewOptionSortItem($addeddate);
    }
}
