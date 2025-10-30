<?php

namespace FWK\Core\Form\Elements\AttributeTraits;

/**
 * This is the 'class' attribute trait.
 * This trait has been created to share common code between form elements classes,
 * in this case the declaration of the 'class' attribute and its set/get methods.
 *
 * @see AttributeClassTrait::setClass()
 * @see AttributeClassTrait::getClass()
 * 
 * @package FWK\Core\Form\Elements\AttributeTraits
 */
trait AttributeClassTrait {

    protected string $class = '';

    /**
     * This method sets the 'class' attribute with the given value and returns self.
     * 
     * @param string $class
     * 
     * @return self
     */
    public function setClass(string $class): self {
        $this->class = $class;
        return $this;
    }

    /**
     * This method returns the current value of the 'class' attribute.
     * 
     * @return string
     */
    public function getClass(): string {
        return $this->class;
    }
}