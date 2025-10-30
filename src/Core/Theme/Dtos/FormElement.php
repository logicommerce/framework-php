<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Core\Dtos\Element;

/**
 * This is the 'FormElement' class, a DTO class for the form configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see FormElement::getElementClass()
 * @see FormElement::getLabelClass()
 *
 * @see Element
 * 
 * @uses ElementTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class FormElement extends Element {
    use ElementTrait;

    public const ELEMENT_CLASS = 'elementClass';

    public const LABEL_CLASS = 'labelClass';
    
    private string $elementClass = '';

    private string $labelClass = '';

    /**
     * This method returns the element class value.
     * 
     * @return string
     */
    public function getElementClass(): string {
        return $this->elementClass;
    }

    /**
     * This method returns the label class value.
     * 
     * @return string
     */
    public function getLabelClass(): string {
        return $this->labelClass;
    }

}


