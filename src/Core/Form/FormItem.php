<?php

namespace FWK\Core\Form;

use FWK\Core\FilterInput\FilterInput;
use FWK\Core\Form\Elements\Element;

/**
 * This is the FormItem class. This class represents an item of a form (encapsulating its name, filter and element).
 *
 * @see FormItem::getFilterInput()
 * @see FormItem::getElement()
 * @see FormItem::getName()
 * 
 * @package FWK\Core\Form
 */
class FormItem {

    private $name = '';

    private $filterInput = null;

    private $formElement = null;

    /**
     * Constructor of the FormItem.
     *
     * @param string $name
     * @param Element $formElement
     * @param FilterInput $filterInput
     */
    public function __construct(string $name, Element $formElement, FilterInput $filterInput = null) {
        $this->name = $name;
        $this->formElement = $formElement;
        if (is_null($filterInput)) {
            $this->filterInput = $this->formElement->getFilterInput();
        } else {
            $this->filterInput = $filterInput;
        }
    }

    /**
     * This method returns the FilterInput of the FormItem.
     *
     * @return FilterInput|NULL
     */
    public function getFilterInput(): ?FilterInput {
        return $this->filterInput;
    }

    /**
     * This method returns the Element of the FormItem.
     *
     * @return Element|NULL
     */
    public function getElement(): ?Element {
        return $this->formElement;
    }

    /**
     * This method returns the name of the FormItem.
     *
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }
}
