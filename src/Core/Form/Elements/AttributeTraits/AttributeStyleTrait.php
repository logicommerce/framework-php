<?php

namespace FWK\Core\Form\Elements\AttributeTraits;

/**
 * This is the 'style' attribute trait.
 * This trait has been created to share common code between form elements classes,
 * in this case the declaration of the 'style' attribute and its set/get methods.
 *
 * @see AttributeStyleTrait::setStyle()
 * @see AttributeStyleTrait::getStyle()
 * 
 * @package FWK\Core\Form\Elements\AttributeTraits
 */
trait AttributeStyleTrait {

    protected string $style = '';

    /**
     * This method sets the 'style' attribute with the given value and returns self.
     * 
     * @param string $style
     * 
     * @return self
     */
    public function setStyle(string $style): self {
        $this->style = $style;
        return $this;
    }

    /**
     * This method returns the current value of the 'style' attribute.
     * 
     * @return string
     */
    public function getStyle(): string {
        return $this->style;
    }
}