<?php

namespace FWK\Core\Form\Elements;

use FWK\Core\Form\Elements\AttributeTraits\AttributesEventsTraits;
use FWK\Core\Form\Elements\AttributeTraits\AttributeSizeTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeIdTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeClassTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeRequiredTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeDisabledTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeDataTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeAutocompleteTrait;
use FWK\Core\FilterInput\FilterInput;

/**
 * MultiSelect-style dropdown for single picks, with per-option image support.
 * Native <select><option> can't carry inline images; radios share `name` so the
 * browser handles single-selection and form submission natively.
 */
class SingleSelect extends Element {

    use AttributesEventsTraits;
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
    private array $images = [];

    public const TYPE = 'singleselect';

    public function __construct(array $options, ?FilterInput $filterInput = null, string $selected = '', array $images = []) {
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
                FilterInput::CONFIGURATION_ALLOW_EMPTY => true,
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

        $html = '<div ' . $this->outputAttributes($name, $richFormList) . '>';
        $html .= '<div class="singleselect-dropdown form-select" data-lc-function="singleSelectDropdown">';
        $html .= $this->getLabelFor($this->getRequired());
        $html .= '<div class="singleselect-dropdown-list-wrapper">';
        $html .= '<div class="singleselect-dropdown-list">';

        $selectedLabelText = '';
        $selectedLabelImage = '';
        $firstRadio = true;

        foreach ($this->options as $option) {
            $value = htmlspecialchars((string) $option->getValue());
            $isSelected = ((string) $option->getValue() === $this->selected);
            $checkedAttr = $isSelected ? 'checked' : '';
            $checkedClass = $isSelected ? 'checked form-check form-check-inline' : 'form-check form-check-inline';
            $image = $this->images[$option->getValue()] ?? '';
            $imageHtml = strlen($image) ? '<img class="singleselect-option-image" src="' . htmlspecialchars($image) . '" alt="" loading="lazy" onerror="this.style.display=\'none\';">' : '';
            $text = $option->getText();
            if ($isSelected) {
                $selectedLabelText = $text;
                $selectedLabelImage = $image;
            }
            $radioId = $this->getId() . '_' . $value;
            // Shared `name` → browser handles single-selection + form submit natively, no
            // hidden carrier needed. required/data-validation only on the first radio so the
            // library validates the group once.
            $requiredAttr = ($firstRadio && $this->getRequired()) ? ' required data-validation="required"' : '';
            $disabledAttr = $this->getDisabled() ? ' disabled' : '';
            $firstRadio = false;
            $html .= '<div class="' . $checkedClass . '">'
                . '<input class="form-check-input" type="radio" name="' . $escapedName . '" id="' . htmlspecialchars($radioId) . '" value="' . $value . '" ' . $checkedAttr . $requiredAttr . $disabledAttr . '>'
                . '<label class="form-check-label" for="' . htmlspecialchars($radioId) . '">' . $imageHtml . $text . '</label>'
                . '</div>';
        }

        $html .= '</div></div>';
        $selectedLabelImageHtml = strlen($selectedLabelImage) ? '<img class="singleselect-option-image" src="' . htmlspecialchars($selectedLabelImage) . '" alt="" loading="lazy" onerror="this.style.display=\'none\';">' : '';
        $html .= '<span class="singleselect-label">' . $selectedLabelImageHtml . '<span class="selected-text">' . htmlspecialchars($selectedLabelText) . '</span></span>';
        $html .= '</div></div>';
        return $html;
    }

    public function getOptions(): array {
        return $this->options;
    }
}
