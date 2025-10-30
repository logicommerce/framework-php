<?php

namespace FWK\Core\Form\Elements\AttributeTraits;

/**
 * This is the 'id' attribute trait.
 * This trait has been created to share common code between form elements classes,
 * in this case the declaration of the 'id' attribute and its set/get methods.
 *
 * @see AttributeIdTrait::setId()
 * @see AttributeIdTrait::getId()
 * 
 * @package FWK\Core\Form\Elements\AttributeTraits
 */
trait AttributeIdTrait {

    protected string $id = '';

    /**
     * This method sets the 'id' attribute with the given value and returns self.
     * 
     * @param string $id
     * 
     * @return self
     */
    public function setId(string $id): self {
        $this->id = $id;
        return $this;
    }
    
    /**
     * This method returns the current value of the 'id' attribute.
     * 
     * @return string
     */
    public function getId(): string {
        return $this->id;
    }
}