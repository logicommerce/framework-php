<?php

namespace FWK\Core\Form\Elements;

use FWK\Core\Form\Elements\AttributeTraits\AttributesEventsTraits;
use FWK\Core\Form\Elements\AttributeTraits\AttributeValueTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeSelectedTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeDataTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeDisabledTrait;

/**
 * This is the Option class.
 * This class represents an 'option' form element.
 * <br>This class extends Element (FWK\Core\Form\Elements\Element), see this class.
 *
 * @see Option::outputElement()
 * 
 * @see Element
 *
 * @package FWK\Core\Form\Elements
 */
class Option extends Element {

    use AttributesEventsTraits;

    use AttributeValueTrait;

    use AttributeSelectedTrait;

    use AttributeDataTrait;

    use AttributeDisabledTrait;

    public const TYPE = 'option';

    private string $text = '';

    /**
     * Constructor. It creates the Option with the given text.
     * 
     * @param string $text Text for the option.
     */
    public function __construct(string $text) {
        $this->text = $text;
    }

    public function getText(): string {
        return $this->text;
    }

    /**
     * 
     * 
     * 
     * @see \FWK\Core\Form\Elements\Element::outputElement()
     */
    public function outputElement(string $name = '', array $richFormList = [], string $selected = ''): string {
        return '<option' . $this->outputAttributes($name, $richFormList) . ' ' .  ($this->getValue() == $selected ? 'selected' : '') .  '>' . $this->text  . '</option>';
    }
}
