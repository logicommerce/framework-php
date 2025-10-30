<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Element;
use SDK\Core\Dtos\Traits\ElementTrait;

/**
 * This is the 'DiscountsViewOptionSortItems' class, a DTO class for the theme configuration data.
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
class DiscountsViewOptionSortItems extends Element {
    use ElementTrait;

    public const PRIORITY = 'priority';

    public const NAME = 'name';

    public const DISPLAYPRIORITY = 'displayPriority';

    public const ACTIVATIONDATE = 'activationDate';

    public const EXPIRATIONDATE = 'expirationDate';

    private ?ViewOptionSortItem $priority = null;

    private ?ViewOptionSortItem $name = null;

    private ?ViewOptionSortItem $displayPriority = null;

    private ?ViewOptionSortItem $activationDate = null;

    private ?ViewOptionSortItem $expirationDate = null;

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
     * This method returns displayPriority sort configuration.
     *
     * @return ViewOptionSortItem|NULL
     */
    public function getDisplayPriority(): ?ViewOptionSortItem {
        return $this->displayPriority;
    }

    private function setDisplayPriority(array $displayPriority): void {
        $this->displayPriority = new ViewOptionSortItem($displayPriority);
    }

    /**
     * This method returns activationDate sort configuration.
     *
     * @return ViewOptionSortItem|NULL
     */
    public function getActivationDate(): ?ViewOptionSortItem {
        return $this->activationDate;
    }

    private function setActivationDate(array $activationDate): void {
        $this->activationDate = new ViewOptionSortItem($activationDate);
    }

    /**
     * This method returns expirationDate sort configuration.
     *
     * @return ViewOptionSortItem|NULL
     */
    public function getExpirationDate(): ?ViewOptionSortItem {
        return $this->expirationDate;
    }

    private function setExpirationDate(array $expirationDate): void {
        $this->expirationDate = new ViewOptionSortItem($expirationDate);
    }
}
