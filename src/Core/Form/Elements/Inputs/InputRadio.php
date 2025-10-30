<?php

namespace FWK\Core\Form\Elements\Inputs;

use FWK\Core\Form\Elements\Input;
use FWK\Core\Form\Elements\AttributeTraits\AttributeRequiredTrait;
use FWK\Core\FilterInput\FilterInput;
use FWK\Core\Form\Elements\Element;
use FWK\Core\Theme\Theme;

/**
 * This is the InputRadio class. This class represents a form input of type 'radio'.
 * <br>This class extends Input (FWK\Core\Form\Elements\Input), see this class.
 *
 * @see InputRadio::outputElement()
 * 
 * @see Input
 *
 * @package FWK\Core\Form\Elements\Inputs
 */
class InputRadio extends Input {
    use AttributeRequiredTrait;

    public const TYPE = 'radio';

    private array $values = [];

    private string $checked = '';

    /**
     * Constructor of the InputRadio.
     * 
     * @param array $values To set the values of the radio input ($key => $text).
     * @param FilterInput $filterInput To set an specific FilterInput to the radio input. If null, then the constructor sets a default FilterInput.
     * @param string $checked To set the checked value of the radio input.
     */
    public function __construct(array $values, FilterInput $filterInput = null, string $checked = '') {
        $this->values = $values;
        $this->checked = $checked;
        if (is_null($filterInput)) {
            $this->setFilterInput(new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_AVAILABLE_VALUES => array_keys($values),
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false
            ]));
        } else {
            $this->setFilterInput($filterInput);
        }
    }

    /**
     * 
     * 
     * 
     * @see \FWK\Core\Form\Elements\Input::outputElement()
     * 
     * The "data" property must be an array key -> value, where the key, is the same that the property "values", and the value for each "data" row will
     * be the lc-data for each Radio button
     * 
     */
    public function outputElement(string $name = '', array $richFormList = []): string {
        $htmlResult = '';

        if (is_null(Element::$elements)) {
            Element::$elements = Theme::getInstance()->getConfiguration()->getForms()->getElements();
        }

        if (!strLen($this->getId())) {
            $this->setId($name);
        }
        $id = $this->getId();
        $mainData = $this->data;

        foreach ($this->values as $key => $text) {
            $this->setValue($key);
            $auxId = $id . ucfirst($key);
            $this->setId($auxId);
            if (isset($mainData[$key])) {
                $this->setData($mainData[$key]);
            } else {
                $this->setData(null);
            }
            $htmlResult .= '<input type="' . static::TYPE . '"' . $this->outputAttributes($name, $richFormList) . ' ' .  ($key == $this->checked ? 'checked' : '') . '>';
            $htmlResult .= '<label class="' . Element::$elements->getInput()->getRadio()->getLabelClass() . '" style="' . $this->getStyle() . '" for="' . $auxId . '">' . $text . '</label>';
        }
        return $this->getLabelFor($this->getRequired()) . $htmlResult;
    }
}
