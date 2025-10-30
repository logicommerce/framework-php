<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Core\Dtos\Element;

/**
 * This is the 'FormElementButton' class, a DTO class for the form configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see FormElementButton::getButton()
 * @see FormElementButton::getReset()
 * @see FormElementButton::getSubmit()
 *
 * @see Element
 * 
 * @uses ElementTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class FormElementButton extends Element {
    use ElementTrait;

    public const BUTTON = 'button';

    public const RESET = 'reset';

    public const SUBMIT = 'submit';
    
    private ?FormElement $button = null;

    private ?FormElement $reset = null;

    private ?FormElement $submit = null;

    /**
     * This method returns the button element configuration
     * 
     * @return FormElement|NULL
     */
    public function getButton(): ?FormElement {
        return $this->button;
    }

    private function setButton(array $button): void {
        $this->button = new FormElement($button);
    }

    /**
     * This method returns the reset element configuration
     * 
     * @return FormElement|NULL
     */
    public function getReset(): ?FormElement {
        return $this->reset;
    }

    private function setReset(array $reset): void {
        $this->reset = new FormElement($reset);
    }

    /**
     * This method returns the submit element configuration
     * 
     * @return FormElement|NULL
     */
    public function getSubmit(): ?FormElement {
        return $this->submit;
    }

    private function setSubmit(array $submit): void {
        $this->submit = new FormElement($submit);
    }
}


