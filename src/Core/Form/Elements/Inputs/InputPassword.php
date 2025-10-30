<?php

namespace FWK\Core\Form\Elements\Inputs;

use FWK\Core\Form\Elements\Input;
use FWK\Core\Form\Elements\AttributeTraits\AttributeSizeTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributePatternTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributePlaceholderTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeRequiredTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeAutocompleteTrait;
use FWK\Core\FilterInput\FilterInput;

/**
 * This is the InputPassword class. This class represents a form input of type 'password'.
 * <br>This class extends Input (FWK\Core\Form\Elements\Input), see this class.
 *
 * @see Input
 *
 * @package FWK\Core\Form\Elements\Inputs
 */
class InputPassword extends Input {

    public const TYPE = 'password';

    use AttributeSizeTrait;
    use AttributePatternTrait;
    use AttributePlaceholderTrait;
    use AttributeRequiredTrait;
    use AttributeAutocompleteTrait;

    /**
     * Constructor of InputPassword.
     *
     * @param string $value To set the password input value.
     * @param FilterInput $filterInput To set an specific FilterInput to the password input. If null, then the constructor sets a default FilterInput.
     */
    public function __construct(string $value = '', FilterInput $filterInput = null) {
        $this->value = $value;
        if(is_null($filterInput)) {
            $defaultFilter = new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false
            ]);
        }
        else {
            $defaultFilter = $filterInput;
        }
        parent::__construct($value, $defaultFilter);
    }
}