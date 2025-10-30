<?php

namespace FWK\Core\Form\Elements\AttributeTraits;

/**
 * This is the 'value' attribute trait.
 * This trait has been created to share common code between form elements classes,
 * in this case the declaration of the 'value' attribute and its set/get methods.
 *
 * @see AttributeValueTrait::setValue()
 * @see AttributeValueTrait::getValue()
 * 
 * @package FWK\Core\Form\Elements\AttributeTraits
 */
trait AttributeValueTrait {

    protected string $value = '';
    
    /**
     * This method sets the 'value' attribute with the given value and returns self.
     * 
     * @param string $value
     * 
     * @return self
     */
    public function setValue(string $value): self {
        $this->value = $value;
        return $this;
    }
    
    /**
     * This method returns the current value of the 'value' attribute.
     * 
     * @return string
     */
    public function getValue(): string {
        return $this->value;
    }
}