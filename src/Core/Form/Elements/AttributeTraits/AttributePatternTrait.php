<?php

namespace FWK\Core\Form\Elements\AttributeTraits;

/**
 * This is the 'pattern' attribute trait.
 * This trait has been created to share common code between form elements classes,
 * in this case the declaration of the 'pattern' attribute and its set/get methods.
 *
 * @see AttributePatternTrait::setPattern()
 * @see AttributePatternTrait::getPattern()
 * 
 * @package FWK\Core\Form\Elements\AttributeTraits
 */
trait AttributePatternTrait {

    protected string $pattern = '';

    /**
     * This method sets the 'pattern' attribute with the given value and returns self.
     * 
     * @param string $pattern
     * 
     * @return self
     */
    public function setPattern(string $pattern): self {
        $this->pattern = $pattern;
        return $this;
    }

    /**
     * This method returns the current value of the 'pattern' attribute.
     * 
     * @return string
     */
    public function getPattern(): string {
        return $this->pattern;
    }
}