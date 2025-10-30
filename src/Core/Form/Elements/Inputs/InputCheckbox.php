<?php

namespace FWK\Core\Form\Elements\Inputs;

use FWK\Core\Form\Elements\Input;
use FWK\Core\Form\Elements\AttributeTraits\AttributeRequiredTrait;
use FWK\Core\FilterInput\FilterInput;

/**
 * This is the InputCheckbox class. This class represents a form input of type 'checkbox'.
 * <br>This class extends Input (FWK\Core\Form\Elements\Input), see this class.
 *
 * @see InputCheckbox::setChecked()
 * @see InputCheckbox::getChecked()
 * 
 * @see Input
 *
 * @package FWK\Core\Form\Elements\Inputs
 */
class InputCheckbox extends Input {

    use AttributeRequiredTrait;

    public const TYPE = 'checkbox';

    protected bool $checked = false;

    /**
     * Constructor of the InputCheckbox. 
     * 
     * @param string $value To set the checkbox input value.
     * @param FilterInput $filterInput To set an specific FilterInput to the checkbox input. If null, then the constructor sets a default FilterInput.
     */
    public function __construct(string $value = '', FilterInput $filterInput = null) {
        $this->value = $value;
        if (is_null($filterInput)) {
            $defaultFilter = new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false
            ]);
        } else {
            $defaultFilter = $filterInput;
        }
        parent::__construct($value, $defaultFilter);
    }

    /**
     * This method sets the given value to the checkbox input.
     * 
     * @param bool $checked
     * 
     * @return self
     */
    public function setChecked(bool $checked): self {
        $this->checked = $checked;
        return $this;
    }

    /**
     * This method returns the current value of the checkbox input.
     * 
     * @return bool
     */
    public function getChecked(): bool {
        return $this->checked;
    }
}
