<?php

namespace FWK\Core\Form\Elements;

use FWK\Core\Form\Elements\AttributeTraits\AttributesEventsTraits;
use FWK\Core\Form\Elements\AttributeTraits\AttributeMultipleTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeSizeTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeIdTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeClassTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeRequiredTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeDisabledTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeDataTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeAutocompleteTrait;
use FWK\Core\FilterInput\FilterInput;
use FWK\Core\Resources\Language;
use FWK\Enums\LanguageLabels;

/**
 * MultiSelect
 * This is the MultiSelect class.
 */
class MultiSelect extends Element {

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
    private array $selected = [];

    public const TYPE = 'multiselect';

    public function __construct(array $options, FilterInput $filterInput = null, array $selected = []) {
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

    public function outputElement(string $name = '', array $richFormList = []): string {
        if (!strLen($this->getId())) {
            $this->setId($name);
        }

        $html = $this->getLabelFor($this->getRequired());

        $html .= '<div ' . $this->outputAttributes($name, $richFormList) . '>';

        $html .= '<input type="hidden" id="multiSelectValue" name="multiSelectValue" value="' . implode(',', $this->selected) . '">';

        $html .= '<div class="multiselect-dropdown form-select" data-lc-function="multiSelectDropdown">';
        $html .= '<div class="multiselect-dropdown-list-wrapper">';
        $html .= '<div class="multiselect-dropdown-list">';

        foreach ($this->options as $option) {
            $value = htmlspecialchars($option->getValue());
            $isChecked = in_array($option->getValue(), $this->selected) ? 'checked' : '';
            $checkedClass = $isChecked ? 'checked form-check form-check-inline' : 'form-check form-check-inline';
            $html .= "<div class=\"{$checkedClass}\"><input class=\"form-check-input\" type=\"checkbox\" value=\"{$value}\" {$isChecked}><label class=\"form-check-label\">{$option->getText()}</label></div>";
        }

        $html .= '</div></div>';
        $html .= '<span class="multiselect-label">' . Language::getInstance()->getLabelValue(LanguageLabels::SELECTED_LABEL) . ' <span class="selected">(' . count($this->selected) . ')</span></span>';
        $html .= '</div></div>';
        return $html;
    }

    public function getOptions(): array {
        return $this->options;
    }
}
