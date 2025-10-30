<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Element;
use SDK\Core\Dtos\Traits\ElementTrait;

/**
 * This is the 'NewsViewOptionSortItems' class, a DTO class for the theme configuration data.
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
class NewsViewOptionSortItems extends Element {
    use ElementTrait;

    public const PRIORITY = 'priority';

    public const PUBLICATIONDATE = 'publicationdate';

    private ?ViewOptionSortItem $priority = null;

    private ?ViewOptionSortItem $publicationdate = null;

    /**
     * This method returns publicationdate sort configuration.
     *
     * @return ViewOptionSortItem|NULL
     */
    public function getPublicationdate(): ?ViewOptionSortItem {
        return $this->publicationdate;
    }

    private function setPublicationdate(array $publicationdate): void {
        $this->publicationdate = new ViewOptionSortItem($publicationdate);
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
}
