<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Core\Dtos\Element;

/**
 * This is the 'Events' class, a DTO class for the theme configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see Events::getEvent()
 *
 * @see Element
 * @see ElementTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class EventsSetup extends Element {
    use ElementTrait;

    private string $event = '';

    private string $method = '';

    private int $rate = 0;

    /**
     * This method returns the event configuration.
     * 
     * @return string
     */
    public function getEvent(): string {
        return $this->event;
    }

    /**
     * This method returns the method configuration.
     * 
     * @return string
     */
    public function getMethod(): string {
        return $this->method;
    }

    /**
     * This method returns the rate configuration.
     * 
     * @return int
     */
    public function getRate(): int {
        return $this->rate;
    }
}
