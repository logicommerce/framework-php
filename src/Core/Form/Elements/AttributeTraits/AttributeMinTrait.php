<?php

namespace FWK\Core\Form\Elements\AttributeTraits;

/**
 * This is the 'min' attribute trait.
 * This trait has been created to share common code between form elements classes,
 * in this case the declaration of the 'min' attribute and its set/get methods.
 *
 * @see AttributeMinTrait::setMin()
 * @see AttributeMinTrait::getMin()
 * 
 * @package FWK\Core\Form\Elements\AttributeTraits
 */
trait AttributeMinTrait {

    protected int $min = -1;

    /**
     * This method sets the 'min' attribute with the given value and returns self.
     * 
     * @param int $min
     * 
     * @return self
     */
    public function setMin(int $min): self {
        $this->min = $min;
        return $this;
    }

    /**
     * This method returns the current value of the 'min' attribute.
     * 
     * @return int
     */
    public function getMin(): int {
        return $this->min;
    }
}