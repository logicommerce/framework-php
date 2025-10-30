<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Element;
use SDK\Core\Dtos\Traits\ElementTrait;

/**
 * This is the 'FormItem' class, a DTO class for the theme configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see FormField::getIncluded()
 * @see FormField::getRequired()
 * @see FormField::getPriority()
 * @see FormField::getRegex()
 * @see FormField::getKeyName()
 * @see FormField::setKeyName()
 *
 * @see Element
 * @see ElementTrait
 *
 * @package FWK\Core\Theme\Dtos
 */

class FormField extends Element {
    use ElementTrait;
    
    public const INCLUDED = 'included';

    public const REQUIRED = 'required';
    
    public const PRIORITY = 'priority';

    public const REGEX = 'regex';
    
    protected bool $included = false;

    protected bool $required = false;
    
    protected int $priority = 0;

    protected string $regex = '';

    protected string $keyName = '';

    /**
     * This method returns if the field is included. 
     *
     * @return bool
     */
    public function getIncluded(): bool {
        return $this->included;
    }

    /**
     * This method returns the field priority.
     *
     * @return int
     */
    public function getPriority(): int {
        return $this->priority;
    }
    
    /**
     * This method returns if the field is required. 
     *
     * @return bool
     */
    public function getRequired(): bool {
        return $this->required;
    }

    /**
     * This method returns the field regex.
     *
     * @return string
     */
    public function getRegex(): string {
        return $this->regex;
    }

    /**
     * This method returns the field keyName.
     *
     * @return string
     */
    public function getKeyName(): string {
        return $this->keyName;
    }

    /**
     * This method returns the field keyName.
     */
    public function setKeyName(string $keyName): void {
        $this->keyName = $keyName;
    }
}
