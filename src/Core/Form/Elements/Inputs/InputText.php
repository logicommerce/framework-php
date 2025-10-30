<?php

namespace FWK\Core\Form\Elements\Inputs;

use FWK\Core\FilterInput\FilterInput;
use FWK\Core\Form\Elements\Input;
use FWK\Core\Form\Elements\AttributeTraits\AttributeSizeTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributePatternTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributePlaceholderTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeRequiredTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeAutocompleteTrait;

/**
 * This is the InputText class. This class represents a form input of type 'text'.
 * <br>This class extends Input (FWK\Core\Form\Elements\Input), see this class.
 *
 * @see Input
 *
 * @package FWK\Core\Form\Elements\Inputs
 */
class InputText extends Input {

    public const TYPE = 'text';

    use AttributeSizeTrait;
    use AttributePatternTrait;
    use AttributePlaceholderTrait;
    use AttributeRequiredTrait;
    use AttributeAutocompleteTrait;

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
