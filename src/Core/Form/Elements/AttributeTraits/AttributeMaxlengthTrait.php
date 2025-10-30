<?php

namespace FWK\Core\Form\Elements\AttributeTraits;

/**
 * This is the 'maxLength' attribute trait.
 * This trait has been created to share common code between form elements classes,
 * in this case the declaration of the 'maxLength' attribute and its set/get methods.
 *
 * @see AttributeMaxlengthTrait::setMaxlength()
 * @see AttributeMaxlengthTrait::getMaxlength()
 * 
 * @package FWK\Core\Form\Elements\AttributeTraits
 */
trait AttributeMaxlengthTrait {

    protected int $maxlength = -1;

    /**
     * This method sets the 'maxLength' attribute with the given value and returns self.
     * 
     * @param int $maxlength
     * 
     * @return self
     */
    public function setMaxlength(int $maxlength): self {
        $this->maxlength = $maxlength;
        return $this;
    }
    
    /**
     * This method returns the current value of the 'maxLength' attribute.
     * 
     * @return int
     */
    public function getMaxlength(): int {
        return $this->maxlength;
    }
    
}