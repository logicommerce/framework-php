<?php

namespace FWK\Core\Form\Elements\AttributeTraits;

/**
 * This is the 'autofocus' attribute trait.
 * This trait has been created to share common code between form elements classes,
 * in this case the declaration of the 'autofocus' attribute and its set/get methods.
 *
 * @see AttributeAutofocusTrait::setAutofocus()
 * @see AttributeAutofocusTrait::getAutofocus()
 * 
 * @package FWK\Core\Form\Elements\AttributeTraits
 */
trait AttributeAutofocusTrait {

    protected bool $autofocus = false;

    /**
     * This method sets the 'autofocus' attribute with the given value and returns self.
     * 
     * @param bool $autofocus
     * 
     * @return self
     */
    public function setAutofocus(bool $autofocus): self {
        $this->autofocus = $autofocus;
        return $this;
    }

    /**
     * This method returns the current value of the 'autofocus' attribute.
     * 
     * @return bool
     */
    public function getAutofocus(): bool {
        return $this->autofocus;
    }
}