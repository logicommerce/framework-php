<?php

namespace FWK\Core\Form\Elements;

use FWK\Core\Form\Elements\AttributeTraits\AttributesEventsTraits;
use FWK\Core\Form\Elements\AttributeTraits\AttributeMultipleTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeSizeTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeIdTrait;
use FWK\Core\FilterInput\FilterInput;
use FWK\Core\Form\Elements\AttributeTraits\AttributeAutocompleteTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeClassTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeDataTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeDisabledTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeRequiredTrait;

/**
 * This is the Select class.
 * This class represents a 'select' form element.
 * <br>This class extends Element (FWK\Core\Form\Elements\Element), see this class.
 *
 * @see Select::outputElement()
 * 
 * @see Element
 *
 * @package FWK\Core\Form\Elements
 */
class Select extends Element {

    use AttributesEventsTraits;

    use AttributeMultipleTrait;

    use AttributeSizeTrait;

    use AttributeIdTrait;

    use AttributeClassTrait;

    use AttributeRequiredTrait;

    use AttributeDisabledTrait;

    use AttributeDataTrait;

    use AttributeAutocompleteTrait;

    use LabelTrait;

    private array $options = [];

    private string $selected;

    public const TYPE = 'select';

    /**
     * Constructor.
     * It creates the Select with the given options.
     *
     * @param array $options
     *            Options of the select.
     * @param string $selected
     *            Key of the option to set as selected by default.
     * @param FilterInput $filterInput
     *            To set an specific FilterInput. If null, then the constructor sets a default FilterInput.
     */
    public function __construct(array $options, FilterInput $filterInput = null, string $selected = '') {
        $this->options = $options;
        $this->selected = $selected;
        $optionValues = [];
        foreach ($this->options as $option) {
            $optionValues[] = $option->getValue();
        }
        if (is_null($filterInput)) {
            $this->setFilterInput(new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_AVAILABLE_VALUES => $optionValues,
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false
            ]));
        } else {
            $this->setFilterInput($filterInput);
        }
    }

    /**
     *
     * @see \FWK\Core\Form\Elements\Element::outputElement()
     */
    public function outputElement(string $name = '', array $richFormList = []): string {
        if (!strLen($this->getId())) {
            $this->setId($name);
        }
        $htmlResult = $this->getLabelFor($this->getRequired());
        $htmlResult .= '<select' . $this->outputAttributes($name, $richFormList) . '>';
        foreach ($this->options as $option) {
            $htmlResult .= $option->outputElement('', $richFormList, $this->selected);
        }
        $htmlResult .= '</select>';

        return $htmlResult;
    }
}
