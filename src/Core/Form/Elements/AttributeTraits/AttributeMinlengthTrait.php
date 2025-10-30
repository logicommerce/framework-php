<?php

namespace FWK\Core\Form\Elements\AttributeTraits;

/**
 * This is the 'minLength' attribute trait.
 * This trait has been created to share common code between form elements classes,
 * in this case the declaration of the 'minLength' attribute and its set/get methods.
 *
 * @see AttributeMinlengthTrait::setMinlength()
 * @see AttributeMinlengthTrait::getMinlength()
 * 
 * @package FWK\Core\Form\Elements\AttributeTraits
 */
trait AttributeMinlengthTrait {

    protected int $minlength = -1;

    /**
     * This method sets the 'minLength' attribute with the given value and returns self.
     * 
     * @param int $minlength
     * 
     * @return self
     */
    public function setMinlength(int $minlength): self {
        $this->minlength = $minlength;
        return $this;
    }
    
    /**
     * This method returns the current value of the 'minLength' attribute.
     * 
     * @return int
     */
    public function getMinlength(): int {
        return $this->minlength;
    }
    
}