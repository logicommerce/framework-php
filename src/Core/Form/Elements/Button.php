<?php

namespace FWK\Core\Form\Elements;

use FWK\Core\Form\Elements\AttributeTraits\AttributeFormTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeMaxlengthTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeReadonlyTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributesEventsTraits;
use FWK\Core\Form\Elements\AttributeTraits\AttributeIdTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeDisabledTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributePlaceholderTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeAutofocusTrait;
use FWK\Core\FilterInput\FilterInput;
use FWK\Core\Form\Elements\AttributeTraits\AttributeValueTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeClassTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeDataTrait;

/**
 * This is the base Button class. This class represents the button element of a form.
 * This class extends Element (FWK\Core\Form\Elements\Element), see this class.
 *
 * @abstract
 * 
 * @see Button::setContentText()
 * @see Button::getContentText()
 * @see Button::outputElement()
 * 
 * @see Element
 *
 * @package FWK\Core\Form\Elements
 */
abstract class Button extends Element {

    use AttributesEventsTraits;
    use AttributeIdTrait;
    use AttributeClassTrait;
    use AttributeValueTrait;
    use AttributeAutofocusTrait;
    use AttributeDisabledTrait;
    use AttributeFormTrait;

    use AttributePlaceholderTrait;

    use AttributeMaxlengthTrait;
    use AttributeReadonlyTrait;

    use AttributeDataTrait;

    protected string $text = '';

    public const TYPE = 'button';

    /**
     * This method sets the text of the Button with the given value and returns self.
     * 
     * @param string $text
     * 
     * @return self
     */
    public function setContentText(string $text): self {
        $this->text = $text;
        return $this;
    }

    /**
     * This method returns the current text of the Button.
     * 
     * @return string
     */
    public function getContentText(): string {
        return $this->text;
    }

    /**
     * Constructor of the Button.
     * 
     * @param string $value
     * @param FilterInput $filterInput
     */
    public function __construct(string $value = '', FilterInput $filterInput = null) {
        $this->setValue($value);
        $this->setFilterInput($filterInput);
    }

    /**
     * 
     * 
     * 
     * @see \FWK\Core\Form\Elements\Element::outputElement()
     */
    public function outputElement(string $name = '', array $richFormList = []): string {
        if (!strLen($this->getId())) {
            $this->setId($name);
        }
        return '<button type="' . static::TYPE . '"' . $this->outputAttributes($name, $richFormList) . '>' . $this->getContentText() . '</button>';
    }
}
