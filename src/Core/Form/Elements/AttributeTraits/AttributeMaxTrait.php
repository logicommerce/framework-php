<?php

namespace FWK\Core\Form\Elements\AttributeTraits;

/**
 * This is the 'max' attribute trait.
 * This trait has been created to share common code between form elements classes,
 * in this case the declaration of the 'max' attribute and its set/get methods.
 *
 * @see AttributeMaxTrait::setMax()
 * @see AttributeMaxTrait::getMax()
 * 
 * @package FWK\Core\Form\Elements\AttributeTraits
 */
trait AttributeMaxTrait {

    protected int $max = -1;

    /**
     * This method sets the 'max' attribute with the given value and returns self.
     * 
     * @param int $max
     * 
     * @return self
     */
    public function setMax(int $max): self {
        $this->max = $max;
        return $this;
    }

    /**
     * This method returns the current value of the 'max' attribute.
     * 
     * @return int
     */
    public function getMax(): int {
        return $this->max;
    }
}