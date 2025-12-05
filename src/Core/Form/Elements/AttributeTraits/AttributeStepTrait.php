<?php

namespace FWK\Core\Form\Elements\AttributeTraits;

/**
 * This is the 'step' attribute trait.
 * This trait has been created to share common code between form elements classes,
 * in this case the declaration of the 'step' attribute and its set/get methods.
 *
 * @see AttributeStepTrait::setStep()
 * @see AttributeStepTrait::getStep()
 * 
 * @package FWK\Core\Form\Elements\AttributeTraits
 */
trait AttributeStepTrait {

    protected string|int $step = 0;

    /**
     * This method sets the 'step' attribute with the given value and returns self.
     * 
     * @param string|int $step
     * 
     * @return self
     */
    public function setStep(string|int $step): self {
        $this->step = $step;
        return $this;
    }

    /**
     * This method returns the current value of the 'step' attribute.
     * 
     * @return string|int
     */
    public function getStep(): string|int {
        return $this->step;
    }
}
