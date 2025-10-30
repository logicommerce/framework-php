<?php

namespace FWK\Core\Form\Elements\AttributeTraits;

/**
 * This is the 'placeholder' attribute trait.
 * This trait has been created to share common code between form elements classes,
 * in this case the declaration of the 'placeholder' attribute and its set/get methods.
 *
 * @see AttributePlaceholderTrait::setPlaceholder()
 * @see AttributePlaceholderTrait::getPlaceholder()
 * 
 * @package FWK\Core\Form\Elements\AttributeTraits
 */
trait AttributePlaceholderTrait {

    protected string $placeholder = '';

    /**
     * This method sets the 'placeholder' attribute with the given value and returns self.
     * 
     * @param string $placeholder
     * 
     * @return self
     */
    public function setPlaceholder(string $placeholder): self {
        $this->placeholder = $placeholder;
        return $this;
    }

    /**
     * This method returns the current value of the 'placeholder' attribute.
     * 
     * @return string
     */
    public function getPlaceholder(): string {
        return $this->placeholder;
    }
}