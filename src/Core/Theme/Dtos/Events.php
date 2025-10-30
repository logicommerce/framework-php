<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Core\Dtos\Element;

/**
 * This is the 'Events' class, a DTO class for the theme configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see Events::getSetup()
 *
 * @see Element
 * @see ElementTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class Events extends Element {
    use ElementTrait;

    private array $setup = [];

    /**
     * This method returns the events setup.
     * 
     * @return EventsSetup[]
     */
    public function getSetup(): array {
        return $this->setup;
    }

    private function setSetup(array $setup): void {
        $setupItems = [];
        foreach ($setup as $key => $value) {
            $setupItems[$key] = new EventsSetup($value);
        }
        $this->setup = $setupItems;
    }
}
