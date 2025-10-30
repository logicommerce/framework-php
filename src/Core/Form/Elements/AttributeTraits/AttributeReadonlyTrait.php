<?php

namespace FWK\Core\Form\Elements\AttributeTraits;

/**
 * This is the 'readonly' attribute trait.
 * This trait has been created to share common code between form elements classes,
 * in this case the declaration of the 'readonly' attribute and its set/get methods.
 *
 * @see AttributeReadonlyTrait::setReadonly()
 * @see AttributeReadonlyTrait::getReadonly()
 * 
 * @package FWK\Core\Form\Elements\AttributeTraits
 */
trait AttributeReadonlyTrait {

    protected bool $readonly = false;

    /**
     * This method sets the 'readonly' attribute with the given value and returns self.
     * 
     * @param bool $readonly
     * 
     * @return self
     */
    public function setReadonly(bool $readonly): self {
        $this->readonly = $readonly;
        return $this;
    }
    
    /**
     * This method returns the current value of the 'readonly' attribute.
     * 
     * @return bool
     */
    public function getReadonly(): bool {
        return $this->readonly;
    }
}