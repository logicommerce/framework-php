<?php

namespace FWK\Core\Form\Elements;

use FWK\Core\Form\Elements\AttributeTraits\AttributeFormTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeMaxlengthTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeReadonlyTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributesEventsTraits;
use FWK\Core\Form\Elements\AttributeTraits\AttributeIdTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeRequiredTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeDisabledTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributePlaceholderTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeAutofocusTrait;
use FWK\Core\FilterInput\FilterInput;
use FWK\Core\Form\Elements\AttributeTraits\AttributeValueTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeClassTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeRegexTrait;

/**
 * This is the Textarea class.
 * This class represents a 'textarea' element.
 * <br>This class extends Element (FWK\Core\Form\Elements\Element), see this class.
 *
 * @see Textarea::setContentText()
 * @see Textarea::getContentText()
 * @see Textarea::setCol()
 * @see Textarea::getCol()
 * @see Textarea::setDirname()
 * @see Textarea::getDirname()
 * @see Textarea::setRows()
 * @see Textarea::getRows()
 * @see Textarea::setWrap()
 * @see Textarea::getWrap()
 * @see Textarea::outputElement()
 * 
 * @see Element
 *
 * @package FWK\Core\Form\Elements
 */
class Textarea extends Element {

    public const WRAP_SOFT = 'soft';

    public const WRAP_HARD = 'hard';

    public const TYPE = 'textarea';

    use AttributesEventsTraits;
    use AttributeIdTrait;
    use AttributeClassTrait;

    use AttributeValueTrait;
    use AttributeRequiredTrait;
    use AttributeDisabledTrait;
    use AttributePlaceholderTrait;
    use AttributeAutofocusTrait;
    use AttributeMaxlengthTrait;
    use AttributeReadonlyTrait;
    use AttributeFormTrait;
    use AttributeRegexTrait;

    use LabelTrait;

    protected int $col = -1;

    protected string $dirname = '';

    protected int $rows = -1;

    protected string $wrap = '';

    protected string $text = '';

    /**
     * Constructor. It creates the Textarea with the given value.
     *
     * @param string $value To set the value of the textarea.
     * @param FilterInput $filterInput To set an specific FilterInput. If null, then the constructor sets a default FilterInput.
     */
    public function __construct(string $value = '', FilterInput $filterInput = null) {
        $this->setValue($value);
        $this->setContentText($value);
        if (is_null($filterInput)) {
            $this->setFilterInput(new FilterInput([
                FilterInput::CONFIGURATION_NO_FILTER => false
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
        $required = false;
        $getRequired = 'getRequired';
        if (method_exists(static::class, $getRequired)) {
            $required = static::$getRequired();
        }
        return $this->getLabelFor($required) . '<textarea' . $this->outputAttributes($name, $richFormList) . '>' . $this->getContentText() . '</textarea>';
    }

    /**
     * This method returns the FilterInput of the Element.
     * 
     * @return FilterInput|NULL
     */
    public function getFilterInput(): ?FilterInput {
        return new FilterInput(
            array_merge(
                parent::getFilterInput()->getConfigurationFilter(),
                [FilterInput::CONFIGURATION_ALLOW_EMPTY => !$this->getRequired()]
            )
        );
    }

    /**
     * This method sets the 'text' of the textarea.
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
     * This method returns the 'text' of the textarea.
     * 
     * @return string
     */
    public function getContentText(): string {
        return $this->text;
    }

    /**
     * This method sets the 'cols' textarea attribute.
     * 
     * @param int $col
     * 
     * @return self
     */
    public function setCol(int $col): self {
        $this->col = $col;
        return $this;
    }

    /**
     * This method returns the 'cols' textarea attribute.
     * 
     * @return int
     */
    public function getCol(): int {
        return $this->col;
    }

    /**
     * This method sets the 'dirname' textarea attribute.
     * 
     * @param string $dirname
     * 
     * @return self
     */
    public function setDirname(string $dirname): self {
        $this->dirname = $dirname;
        return $this;
    }

    /**
     * This method returns the 'dirname' textarea attribute.
     * 
     * @return string
     */
    public function getDirname(): string {
        return $this->dirname;
    }

    /**
     * This method sets the 'rows' textarea attribute.
     * 
     * @param int $rows
     * 
     * @return self
     */
    public function setRows(int $rows): self {
        $this->rows = $rows;
        return $this;
    }

    /**
     * This method returns the 'rows' textarea attribute.
     * 
     * @return int
     */
    public function getRows(): int {
        return $this->rows;
    }

    /**
     * This method sets the 'wrap' textarea attribute.
     * Possible values:
     * <ul>
     * <li>Form::WRAP_SOFT</li>
     * <li>Form::WRAP_HARD</li>
     * </ul>
     * 
     * @param string $wrap
     * 
     * @return self
     */
    public function setWrap(string $wrap): self {
        if ($wrap === self::WRAP_SOFT || $wrap === self::WRAP_HARD) {
            $this->wrap = $wrap;
        }
        $this->wrap = $wrap;
        return $this;
    }

    /**
     * This method returns the 'wrap' textarea attribute.
     * 
     * @return string
     */
    public function getWrap(): string {
        return $this->wrap;
    }
}
