<?php

namespace FWK\Core\Form\Elements;

use FWK\Core\Form\Elements\AttributeTraits\AttributeClassTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeIdTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeValueTrait;

/**
 * This is the generic Separator class. 
 * This class represents a generic 'separator' element of a form.
 * <br>This class extends Element (FWK\Core\Form\Elements\Element), see this class.
 *
 * @abstract
 *
 * @see Separator::outputElement()
 * 
 * @see Element
 * 
 * @package FWK\Core\Form\Elements
 */
class Separator extends Element {

    use AttributeClassTrait;

    use AttributeValueTrait;

    use AttributeIdTrait;

    use LabelTrait;

    public const TYPE = 'separator';

    /**
     * Constructor of the Separator.
     * 
     * @param string $value Value of the Separator.
     */
    public function __construct(string $value = '') {
        $this->setValue($value);
    }

    /**
     * This method returns the html output tag of the Separator with the name given by parameter.
     * 
     * 
     * 
     * @see \FWK\Core\Form\Elements\Element::outputElement()
     */
    public function outputElement(string $name = '', array $richFormList = []): string {
        if (!strLen($this->getId())) {
            $this->setId($name);
        }
        return $this->getLabelFor() . '<separator type="' . static::TYPE . '"' . $this->outputAttributes($name, $richFormList) . '>';
    }
}
