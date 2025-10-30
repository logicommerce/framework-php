<?php

namespace FWK\Core\Form\Elements\AttributeTraits;

use FWK\Core\Form\Elements\Input;

/**
 * This is the 'autocomplete' attribute trait.
 * This trait has been created to share common code between form elements classes, 
 * in this case the declaration of the 'autocomplete' attribute and its set/get methods. 
 *
 * @see AttributeAutocompleteTrait::setAutocomplete()
 * @see AttributeAutocompleteTrait::getAutocomplete()
 * 
 * @package FWK\Core\Form\Elements\AttributeTraits
 */
trait AttributeAutocompleteTrait {

    protected string $autocomplete = Input::AUTOCOMPLETE_OFF;

    /**
     * This method sets the 'autocomplete' attribute with the given value and returns self.
     * 
     * @param string $autocomplete Valid values: Input::AUTOCOMPLETE_ON, Input::AUTOCOMPLETE_OFF
     * 
     * @return self
     */
    public function setAutocomplete(string $autocomplete): self {
        if($autocomplete === Input::AUTOCOMPLETE_ON || $autocomplete === Input::AUTOCOMPLETE_OFF){
            $this->autocomplete = $autocomplete;
        }
        return $this;
    }

    /**
     * This method returns the current value of the 'autocomplete' attribute.
     *
     * @return string
     */
    public function getAutocomplete(): string {
        return $this->autocomplete;
    }
}