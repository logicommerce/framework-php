<?php

namespace FWK\Core\Form\Elements\AttributeTraits;

/**
 * This is the 'selected' attribute trait.
 * This trait has been created to share common code between form elements classes,
 * in this case the declaration of the 'selected' attribute and its set/get methods.
 *
 * @see AttributeSelectedTrait::setSelected()
 * @see AttributeSelectedTrait::getSelected()
 * 
 * @package FWK\Core\Form\Elements\AttributeTraits
 */
trait AttributeSelectedTrait {

    protected bool $selected = false;

    /**
     * This method sets the 'selected' attribute with the given value and returns self.
     * 
     * @param bool $selected
     * 
     * @return self
     */
    public function setSelected(bool $selected): self {
        $this->selected = $selected;
        return $this;
    }

    /**
     * This method returns the current value of the 'selected' attribute.
     * 
     * @return bool
     */
    public function getSelected(): bool {
        return $this->selected;
    }
}