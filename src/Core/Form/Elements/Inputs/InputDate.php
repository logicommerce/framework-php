<?php

namespace FWK\Core\Form\Elements\Inputs;

use FWK\Core\FilterInput\FilterInput;
use FWK\Core\Form\Elements\AttributeTraits\AttributePatternTrait;
use FWK\Core\Form\Elements\Input;
use FWK\Core\Form\Elements\AttributeTraits\AttributeRequiredTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeStepTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeMinTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeMaxTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeAutocompleteTrait;

/**
 * This is the InputDate class. This class represents a form input of type 'date'.
 * <br>This class extends Input (FWK\Core\Form\Elements\Input), see this class.
 *
 * @see Input
 *
 * @package FWK\Core\Form\Elements\Inputs
 */
class InputDate extends Input {

    public const TYPE = 'date';

    use AttributePatternTrait;
    use AttributeRequiredTrait;
    use AttributeStepTrait;
    use AttributeAutocompleteTrait;
    use AttributeMinTrait;
    use AttributeMaxTrait;

    /**
     * This method returns the FilterInput of the Element.
     * 
     * @return FilterInput|NULL
     */
    public function getFilterInput(): ?FilterInput {
        return new FilterInput(
            array_merge(
                parent::getFilterInput()->getConfigurationFilter(),
                [FilterInput::CONFIGURATION_ALLOW_EMPTY => !$this->getRequired()]
            )
        );
    }
}
