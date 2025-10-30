<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Core\Dtos\Element;

/**
 * This is the 'FormElements' class, a DTO class for the form configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see FormElements::getButton()
 * @see FormElements::getInput()
 * @see FormElements::getOption()
 * @see FormElements::getSelect()
 * @see FormElements::getTextarea()
 *
 * @see Element
 * @see ElementTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class FormElements extends Element {
    use ElementTrait;

    public const BUTTON = 'button';

    public const INPUT = 'input';
    
    public const OPTION = 'option';
    
    public const SELECT = 'select';
    
    public const TEXTAREA = 'textarea';

    private ?FormElementButton $button = null;

    private ?FormElementInput $input = null;
    
    private ?FormElement $option = null;

    private ?FormElement $select = null;

    private ?FormElement $textarea = null;

    /**
     * This method returns the button element configuration
     * 
     * @return FormElementButton|NULL
     */
    public function getButton(): ?FormElementButton {
        return $this->button;
    }

    private function setButton(array $button): void {
        $this->button = new FormElementButton($button);
    }

    /**
     * This method returns the input element configuration
     * 
     * @return FormElementInput|NULL
     */
    public function getInput(): ?FormElementInput {
        return $this->input;
    }

    private function setInput(array $input): void {
        $this->input = new FormElementInput($input);
    }

    /**
     * This method returns the option element configuration
     * 
     * @return FormElement|NULL
     */
    public function getOption(): ?FormElement {
        return $this->option;
    }

    private function setOption(array $option): void {
        $this->option = new FormElement($option);
    }

    /**
     * This method returns the select element configuration
     * 
     * @return FormElement|NULL
     */
    public function getSelect(): ?FormElement {
        return $this->select;
    }

    private function setSelect(array $select): void {
        $this->select = new FormElement($select);
    }

    /**
     * This method returns the textarea element configuration
     * 
     * @return FormElement|NULL
     */
    public function getTextarea(): ?FormElement {
        return $this->textarea;
    }

    private function setTextarea(array $textarea): void {
        $this->textarea = new FormElement($textarea);
    }

}


