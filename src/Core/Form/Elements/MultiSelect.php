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
    private array $images = [];

    public const TYPE = 'multiselect';

    public function __construct(array $options, ?FilterInput $filterInput = null, array $selected = [], array $images = []) {
        $this->options = $options;
        $this->selected = $selected;
        $this->images = $images;

        $optionValues = [];
        foreach ($this->options as $option) {
            $optionValues[] = $option->getValue();
        }

        if (is_null($filterInput)) {
            $this->setFilterInput(new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_AVAILABLE_VALUES => array_merge([''], $optionValues),
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_ALLOW_EMPTY => true
            ]));
        } else {
            $this->setFilterInput($filterInput);
        }
    }

    public function outputElement(string $name = '', array $richFormList = []): string {
        if (!strLen($this->getId())) {
            $this->setId($name);
        }

        $escapedName = htmlspecialchars($name);

        $html = $this->getLabelFor($this->getRequired());

        $html .= '<div ' . $this->outputAttributes($name, $richFormList) . '>';


        $disabledAttr = $this->getDisabled() ? ' disabled' : '';

        $html .= '<div class="multiselect-dropdown form-select" data-lc-function="multiSelectDropdown">';
        $html .= '<div class="multiselect-dropdown-list-wrapper">';
        $html .= '<div class="multiselect-dropdown-list">';

        foreach ($this->options as $option) {
            $value = htmlspecialchars((string) $option->getValue());
            $isChecked = in_array($option->getValue(), $this->selected) ? 'checked' : '';
            $checkedClass = $isChecked ? 'checked form-check form-check-inline' : 'form-check form-check-inline';
            $image = $this->images[$option->getValue()] ?? '';
            $imageHtml = strlen($image) ? '<img class="multiselect-option-image" src="' . htmlspecialchars($image) . '" alt="" loading="lazy" onerror="this.style.display=\'none\';">' : '';
            $checkboxId = $this->getId() . '_' . $value;
            $html .= '<div class="' . $checkedClass . '">'
                . '<input class="form-check-input" type="checkbox" id="' . htmlspecialchars($checkboxId) . '" value="' . $value . '" ' . $isChecked . $disabledAttr . '>'
                . '<label class="form-check-label" for="' . htmlspecialchars($checkboxId) . '">' . $imageHtml . $option->getText() . '</label>'
                . '</div>';
        }

        $html .= '</div></div>';
        $html .= '<span class="multiselect-label">' . Language::getInstance()->getLabelValue(LanguageLabels::SELECTED_LABEL) . ' <span class="selected">(' . count($this->selected) . ')</span></span>';
        $html .= '</div>';

        // Visually hidden text carrier placed AFTER the dropdown so the browser's native
        // "Please fill out this field" tooltip anchors beneath the visible field, not above it.
        // LC._syncMultiselectPick keeps its comma-joined value in sync with the checkboxes.
        // Not type="hidden" and width/height > 0 via .visually-hidden → jQuery :visible is true →
        // jquery-form-validator picks up the required rule natively.
        $requiredAttr = $this->getRequired() ? ' required data-validation="required"' : '';
        $html .= '<input type="text" class="visually-hidden" tabindex="-1" autocomplete="off" name="' . $escapedName . '" value="' . htmlspecialchars(implode(',', $this->selected)) . '"' . $requiredAttr . $disabledAttr . '>';

        $html .= '</div>';
        return $html;
    }

    public function getOptions(): array {
        return $this->options;
    }
}
