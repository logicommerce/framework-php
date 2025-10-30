<?php

namespace FWK\Core\Form\Elements\AttributeTraits;

/**
 * This is the 'required' attribute trait.
 * This trait has been created to share common code between form elements classes,
 * in this case the declaration of the 'required' attribute and its set/get methods.
 *
 * @see AttributeRequiredTrait::setRequired()
 * @see AttributeRequiredTrait::setRequired()
 * 
 * @package FWK\Core\Form\Elements\AttributeTraits
 */
trait AttributeRequiredTrait {

    protected bool $required = false;

    /**
     * This method sets the 'required' attribute with the given value and returns self.
     * 
     * @param bool $required
     * 
     * @return self
     */
    public function setRequired(bool $required): self {
        $this->required = $required;
        return $this;
    }

    /**
     * This method returns the current value of the 'required' attribute.
     * 
     * @return bool
     */
    public function getRequired(): bool {
        return $this->required;
    }
}