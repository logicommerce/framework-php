<?php

namespace FWK\Core\Form\Elements\AttributeTraits;

/**
 * This is the 'size' attribute trait.
 * This trait has been created to share common code between form elements classes,
 * in this case the declaration of the 'size' attribute and its set/get methods.
 *
 * @see AttributeSizeTrait::setSize()
 * @see AttributeSizeTrait::getSize()
 * 
 * @package FWK\Core\Form\Elements\AttributeTraits
 */
trait AttributeSizeTrait {

    protected int $size = -1;
    
    /**
     * This method sets the 'size' attribute with the given value and returns self.
     * 
     * @param int $size
     * 
     * @return self
     */
    public function setSize(int $size): self {
        $this->size = $size;
        return $this;
    }

    /**
     * This method returns the current value of the 'size' attribute.
     * 
     * @return int
     */
    public function getSize(): int {
        return $this->size;
    }
}