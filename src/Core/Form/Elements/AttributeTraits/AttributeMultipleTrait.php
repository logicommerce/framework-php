<?php

namespace FWK\Core\Form\Elements\AttributeTraits;

/**
 * This is the 'multiple' attribute trait.
 * This trait has been created to share common code between form elements classes,
 * in this case the declaration of the 'multiple' attribute and its set/get methods.
 *
 * @see AttributeMultipleTrait::setMultiple()
 * @see AttributeMultipleTrait::getMultiple()
 * 
 * @package FWK\Core\Form\Elements\AttributeTraits
 */
trait AttributeMultipleTrait {

    protected bool $multiple = false;

    /**
     * This method sets the 'multiple' attribute with the given value and returns self.
     * 
     * @param bool $multiple
     * 
     * @return self
     */
    public function setMultiple(bool $multiple): self {
        $this->multiple = $multiple;
        return $this;
    }

    /**
     * This method returns the current value of the 'multiple' attribute.
     * 
     * @return bool
     */
    public function getMultiple(): bool {
        return $this->multiple;
    }
}