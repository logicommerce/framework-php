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
class TableMultiSelect extends Element {
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
    private array $availableChecks = [];

	public const TYPE = 'tablemultiselect';

	public function __construct(array $options, array $availableChecks) {
        $this->options = $options;
        $this->availableChecks = $availableChecks;
    }

	public function outputElement(string $name = '', array $richFormList = []): string {
        if (!strLen($this->getId())) {
            $this->setId($name);
        }

        $html = '<div><table class="companyRolesTable"><thead><tr><th class="companyRoleFirstColumn"></th>';
        foreach ($this->availableChecks as $check) {
            $html .= '<th>' . $check . '</th>';
        }
        $html .= '</tr></thead><tbody>';
        
        foreach ($this->options as $option) {
            $attrs = $option->getAttributeWildcard();
            $arrow = (strpos($attrs, 'data-id=') !== false)
                ? '<span class="expand-arrow expanded"></span>'
                : '<span class="arrow-spacer"></span>';

            $html .= '<tr ' . $attrs . '>';
            $html .= '<td class="companyRoleItemName">' . $arrow . $option->getLabelFor() . '</td>';

            foreach ($this->availableChecks as $check) {
                $html .= '<td class="checkbox-cell">';
                foreach ($option->getOptions() as $subOption) {
                    if ($subOption->getValue() == $check) {
                        $checkedAttr  = $subOption->getChecked() ? ' checked' : '';
                        $disabledAttr = $subOption->getDisabled() ? ' disabled' : '';

                        $html .= '<input id="' . $subOption->getId() . '" 
                                        name="' . $subOption->getId() . '" 
                                        value="1" 
                                        type="checkbox"' . $checkedAttr . $disabledAttr . '>';

                        // Si está disabled y marcado, añadir hidden extra para que se envíe en el POST
                        if ($subOption->getDisabled() && $subOption->getChecked()) {
                            $html .= '<input type="hidden" name="' . $subOption->getId() . '" value="1">';
                        }

                        break;
                    }
                }
                $html .= '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</tbody></table></div>';

        return $html;
    }
}