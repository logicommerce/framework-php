<?php

namespace FWK\Core\Form\Elements\AttributeTraits;

/**
 * This is the 'regex' attribute trait.
 * This trait has been created to share common code between form elements classes,
 * in this case the declaration of the 'regex' attribute and its set/get methods.
 *
 * @see AttributeRegexTrait::setRegex()
 * @see AttributeRegexTrait::getRegex()
 * 
 * @package FWK\Core\Form\Elements\AttributeTraits
 */
trait AttributeRegexTrait {

    protected string $regex = '';
    
    /**
     * This method sets the 'regex' attribute with the given regex and returns self.
     * 
     * @param string $regex
     * 
     * @return self
     */
    public function setRegex(string $regex): self {
        $this->regex = $regex;
        return $this;
    }
    
    /**
     * This method returns the current regex of the 'regex' attribute.
     * 
     * @return string
     */
    public function getRegex(): string {
        return $this->regex;
    }
}