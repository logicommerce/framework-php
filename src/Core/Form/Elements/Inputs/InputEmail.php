<?php

namespace FWK\Core\Form\Elements\Inputs;

use FWK\Core\Form\Elements\Input;
use FWK\Core\Form\Elements\AttributeTraits\AttributeSizeTrait;
use FWK\Core\FilterInput\FilterInput;
use FWK\Core\Form\Elements\AttributeTraits\AttributePatternTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributePlaceholderTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeRequiredTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeMultipleTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeAutocompleteTrait;

/**
 * This is the InputEmail class. This class represents a form input of type 'email'.
 * <br>This class extends Input (FWK\Core\Form\Elements\Input), see this class.
 *
 * @see Input
 *
 * @package FWK\Core\Form\Elements\Inputs
 */
class InputEmail extends Input {

    public const TYPE = 'email';

    use AttributeSizeTrait;
    use AttributePatternTrait;
    use AttributePlaceholderTrait;
    use AttributeRequiredTrait;
    use AttributeMultipleTrait;
    use AttributeAutocompleteTrait;

    /**
     * Constructor of the InputEmail. 
     * 
     * @param string $value To set the email input value.
     * @param FilterInput $filterInput To set an specific FilterInput to the email input. If null, then the constructor sets a default FilterInput.
     */
    public function __construct(string $value = '', FilterInput $filterInput = null) {
        $this->value = $value;
        if (is_null($filterInput)) {
            $defaultFilter = new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_EMAIL
            ]);
        } else {
            $defaultFilter = $filterInput;
        }
        $this->setMaxlength(255);
        parent::__construct($value, $defaultFilter);
    }
}
