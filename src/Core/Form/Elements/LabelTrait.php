<?php

namespace FWK\Core\Form\Elements;

use FWK\Core\Theme\Theme;

/**
 * This is the 'label' trait.
 * This trait has been created to share common code between form element classes,
 * in this case the declaration of the 'label' tag and its set/get methods.
 *
 * @see LabelTrait::setLabelFor()
 * @see LabelTrait::getLabelFor()
 * 
 * @package FWK\Core\Form\Elements
 */
trait LabelTrait {

    protected string $label = '';

    /**
     * This method sets the 'label' tag with the given value and returns self.
     * 
     * @param string $label
     * 
     * @return self
     */
    public function setLabelFor(string $label): self {
        $this->label = $label;
        return $this;
    }

    /**
     * This method returns the label tag output.
     * 
     * @param bool $required
     * 
     * @return string
     */
    public function getLabelFor(bool $required = false): string {
        return strlen($this->label) ? ('<label for="' . $this->getId() . '" ' . $this->getLabelClassFromTc() . ' >' . $this->label . ($required ? REQUIRED_FIELD_HTML_FLAG : '') . '</label>') : '';
    }

    private function getLabelClassFromTc(): string {
        $class = '';
        if (is_null(Element::$elements)) {
            Element::$elements = Theme::getInstance()->getConfiguration()->getForms()->getElements();
        }
        $getElementType = 'get' . ucfirst(static::TYPE);

        if (self::TYPE === Option::TYPE) {
            $class = Element::$elements->getOption()->getLabelClass();
        } else if (self::TYPE === Textarea::TYPE) {
            $class = Element::$elements->getTextarea()->getLabelClass();
        } else if (self::TYPE === Select::TYPE) {
            $class = Element::$elements->getSelect()->getLabelClass();
        } else if (self::TYPE === MultiSelect::TYPE) {
            $class = Element::$elements->getSelect()->getLabelClass();
        } else if (self::TYPE === Button::TYPE) {
            $class = Element::$elements->getButton()->$getElementType()->getLabelClass();
        } else if (self::TYPE === Input::TYPE) {
            $class = Element::$elements->getInput()->$getElementType()->getLabelClass();
        }

        if (strlen($class)) {
            $class = 'class="' . $class . '"';
        }

        return $class;
    }
}
