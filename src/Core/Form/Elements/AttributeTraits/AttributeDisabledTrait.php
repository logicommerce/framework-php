<?php

namespace FWK\Core\Form\Elements\AttributeTraits;

/**
 * This is the 'disabled' attribute trait.
 * This trait has been created to share common code between form elements classes,
 * in this case the declaration of the 'disabled' attribute and its set/get methods.
 *
 * @see AttributeDisabledTrait::setDisabled()
 * @see AttributeDisabledTrait::getDisabled()
 * 
 * @package FWK\Core\Form\Elements\AttributeTraits
 */
trait AttributeDisabledTrait {

    protected bool $disabled = false;

    /**
     * This method sets the 'disabled' attribute with the given value and returns self.
     * 
     * @param bool $disabled
     * 
     * @return self
     */
    public function setDisabled(bool $disabled): self {
        $this->disabled = $disabled;
        return $this;
    }
    
    /**
     * This method returns the current value of the 'disabled' attribute.
     * 
     * @return bool
     */
    public function getDisabled(): bool {
        return $this->disabled;
    }
}