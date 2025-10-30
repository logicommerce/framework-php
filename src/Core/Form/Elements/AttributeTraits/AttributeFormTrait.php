<?php

namespace FWK\Core\Form\Elements\AttributeTraits;

/**
 * This is the 'form' attribute trait.
 * This trait has been created to share common code between form elements classes,
 * in this case the declaration of the 'form' attribute and its set/get methods.
 *
 * @see AttributeFormTrait::setForm()
 * @see AttributeFormTrait::getForm()
 * 
 * @package FWK\Core\Form\Elements\AttributeTraits
 */
trait AttributeFormTrait {

    protected string $form = '';

    /**
     * This method sets the 'form' attribute with the given value and returns self.
     * 
     * @param string $form
     * 
     * @return self
     */
    public function setForm(string $form): self {
        $this->form = $form;
        return $this;
    }
    
    /**
     * This method returns the current value of the 'form' attribute.
     * 
     * @return string
     */
    public function getForm(): string {
        return $this->form;
    }
}