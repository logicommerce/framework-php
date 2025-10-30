<?php

namespace FWK\Core\Form\Elements\Inputs;

use FWK\Core\FilterInput\FilterInput;
use FWK\Core\Form\Elements\AttributeTraits\AttributeAutocompleteTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeRequiredTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeStepTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeMinTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeMaxTrait;
use FWK\Core\Form\Elements\Input;

/**
 * This is the InputDatetimeLocal class. This class represents a form input of type 'datetime-local'.
 * <br>This class extends Input (FWK\Core\Form\Elements\Input), see this class.
 *
 * @see Input
 *
 * @package FWK\Core\Form\Elements\Inputs
 */
class InputDatetimeLocal extends Input {

    public const TYPE = 'datetime-local';

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
