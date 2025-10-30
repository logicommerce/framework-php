<?php

namespace FWK\Core\Form\Elements;

use FWK\Core\Form\Elements\AttributeTraits\AttributesEventsTraits;
use FWK\Core\Form\Elements\AttributeTraits\AttributeClassTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeIdTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeDisabledTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeMaxlengthTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeValueTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeDataTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeAutofocusTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeReadonlyTrait;
use FWK\Core\FilterInput\FilterInput;
use FWK\Core\Form\Elements\AttributeTraits\AttributeMinlengthTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeRegexTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeStyleTrait;

/**
 * This is the generic Input class. 
 * This class represents a generic 'input' element of a form.
 * <br>This class extends Element (FWK\Core\Form\Elements\Element), see this class.
 *
 * @abstract
 *
 * @see Input::outputElement()
 * 
 * @see Element
 * 
 * @package FWK\Core\Form\Elements
 */
abstract class Input extends Element {

    use AttributesEventsTraits;

    use AttributeIdTrait;

    use AttributeDisabledTrait;

    use AttributeClassTrait;

    use AttributeValueTrait;

    use AttributeDataTrait;

    use AttributeAutofocusTrait;

    use AttributeMaxlengthTrait;

    use AttributeMinlengthTrait;

    use AttributeReadonlyTrait;

    use AttributeRegexTrait;

    use LabelTrait;

    use AttributeStyleTrait;

    public const AUTOCOMPLETE_ON = 'on';

    public const AUTOCOMPLETE_OFF = 'off';

    public const TYPE = 'input';

    /**
     * Constructor of the Input.
     * 
     * @param string $value Value of the Input.
     * @param FilterInput $filterInput Filter of the input.
     */
    public function __construct(string $value = '', FilterInput $filterInput = null) {
        $this->setValue($value);
        if (is_null($filterInput)) {
            $this->setFilterInput(new FilterInput([
                FilterInput::CONFIGURATION_NO_FILTER => false
            ]));
        } else {
            $this->setFilterInput($filterInput);
        }
    }

    /**
     * This method returns the html output tag of the Input with the name given by parameter.
     * 
     * 
     * 
     * @see \FWK\Core\Form\Elements\Element::outputElement()
     */
    public function outputElement(string $name = '', array $richFormList = []): string {
        if (!strLen($this->getId())) {
            $this->setId($name);
        }
        $required = false;
        $getRequired = 'getRequired';
        if (method_exists(static::class, $getRequired)) {
            $required = static::$getRequired();
        }
        return $this->getLabelFor($required) . '<input type="' . static::TYPE . '"' . $this->outputAttributes($name, $richFormList) . '>';
    }
}
