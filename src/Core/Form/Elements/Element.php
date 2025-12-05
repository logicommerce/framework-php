<?php

namespace FWK\Core\Form\Elements;

use FWK\Core\FilterInput\FilterInput;
use FWK\Core\Resources\Loader;
use FWK\Core\Theme\Theme;

/**
 * This is the Element class. This class represents an element of a form.
 *
 * @see Element::getFilterInput()
 * @see Element::setFilterInput()
 * @see Element::setAttributeWildcard()
 * @see Element::getAttributeWildcard()
 * 
 * @package FWK\Core\Form\Elements
 */
abstract class Element {

    protected static $elements = null;

    protected static ?string $classRichFormFactory = null;

    protected static array $elementsClass = [];

    /**
     * This method returns the html output tag of the Element with the name given by parameter.
     * 
     * @param string $name
     * @param array $richFormList. Only recognizes RichFormItems enum elements
     * 
     * @return string
     */
    abstract public function outputElement(string $name = '', array $richFormList = []): string;

    private ?FilterInput $filterInput = null;

    public const TYPE = '';

    protected string $attributeWildcard = '';

    /**
     * This method returns the type of the Element.
     *
     * @return string
     */
    public function getType(): string {
        return static::TYPE;
    }

    /**
     * This method returns the FilterInput of the Element.
     * 
     * @return FilterInput|NULL
     */
    public function getFilterInput(): ?FilterInput {
        return $this->filterInput;
    }

    /**
     * This method sets the FilterInput of the Element and returns self.
     * 
     * @param FilterInput $filterInput
     * 
     * @return self
     */
    public function setFilterInput(?FilterInput $filterInput = null): self {
        $this->filterInput = $filterInput;
        return $this;
    }

    /**
     * This method sets the 'wildcard' attribute with the given value and returns self.
     * 
     * @param string $attributeWildcard
     * 
     * @return self
     */
    public function setAttributeWildcard(string $attributeWildcard): self {
        $this->attributeWildcard = $attributeWildcard;
        return $this;
    }

    /**
     * This method returns the current value of the 'wildcard' attribute.
     * 
     * @return string
     */
    public function getAttributeWildcard(): string {
        return $this->attributeWildcard;
    }

    /**
     * This method outputs the attributes (html) of the Element.
     * 
     * @param string $name
     * @param array $richFormList. Only recognizes RichFormItems enum elements
     * 
     * @return string
     */
    protected function outputAttributes(string $name, array $richFormList = []): string {
        if (strlen($name)) {
            $strAttributes = ' name="' . str_replace('"', '\"', $name) . '"';
        } else {
            $strAttributes = '';
        }
        $properties = [];

        $staticClass = static::class;
        if (!isset(static::$elementsClass[$staticClass])) {
            static::$elementsClass[$staticClass] = [];
            $refClass = (new \ReflectionClass($staticClass));
            $properties = $refClass->getProperties();
            static::$elementsClass[$staticClass]['type'] = $refClass->getConstants()['TYPE'];
            static::$elementsClass[$staticClass]['propertyMethods'] = [];
            foreach ($properties as $property) {
                $get = 'get' . ucfirst($property->name);
                if (method_exists($staticClass, $get)) {
                    static::$elementsClass[$staticClass]['propertyMethods'][$property->name] = $get;
                }
            }
        }

        foreach (static::$elementsClass[$staticClass]['propertyMethods'] as $property => $method) {
            $attributeValue = static::$method();
            if ($property === 'attributeWildcard') {
                $strAttributes .= ' ' . $attributeValue;
            } elseif ($property === 'data') {
                $strAttributes .= ' ' . 'data-lc' . "='" . $attributeValue . "'";
            } elseif (is_bool($attributeValue) && $attributeValue) {
                $strAttributes .= ' ' . $property;
            } elseif ($property === 'step' && ($attributeValue !== 0 && $attributeValue !== '0' && strlen((string)$attributeValue) > 0)) {
                $strAttributes .= ' ' . $property . '="' . str_replace('"', '\"', $attributeValue) . '"';
            } elseif ((is_string($attributeValue) && strlen($attributeValue)) || (is_numeric($attributeValue) && $attributeValue > 0)) {
                if ($property === 'class') {
                    $attributeValue = trim($attributeValue . ' ' . $this->getElementClassFromTc());
                }
                $strAttributes .= ' ' . $property . '="' . str_replace('"', '\"', $attributeValue) . '"';
            }
            $properties[$property] = $attributeValue;
        }

        foreach ($richFormList as $richForm) {
            $get = 'get' . ucfirst($richForm);
            $class = Element::getRichFormFactory();
            if (method_exists(Element::getRichFormFactory(), $get)) {
                $strAttributes .= ' ' . $class::$get($name, static::$elementsClass[$staticClass]['type'], $properties);
            }
        }
        return $strAttributes;
    }

    protected static function getRichFormFactory(): string {
        if (is_null(Element::$classRichFormFactory)) {
            foreach (Loader::LOCATIONS as $location) {
                $class = Loader::getClassFQN('RichFormFactory', $location . 'Core\\Form\\', '');
                if (class_exists($class)) {
                    Element::$classRichFormFactory = $class;
                    break;
                }
            }
        }
        return Element::$classRichFormFactory;
    }

    private function getElementClassFromTc(): string {
        $class = '';
        if (is_null(self::$elements)) {
            self::$elements = Theme::getInstance()->getConfiguration()->getForms()->getElements();
        }

        if (static::TYPE === Option::TYPE) {
            $class = self::$elements->getOption()->getElementClass();
        } else if (static::TYPE === Textarea::TYPE) {
            $class = self::$elements->getTextarea()->getElementClass();
        } else if (static::TYPE === Select::TYPE) {
            $class = self::$elements->getSelect()->getElementClass();
        } else if (strpos(static::class, Button::class) !== false || strpos(static::class, Input::class) !== false) {
            $getElementType = 'get' . ucfirst(static::TYPE);
            if (strpos(static::class, Button::class) !== false) {
                $class = self::$elements->getButton()->$getElementType()->getElementClass();
            } else if (strpos(static::class, Input::class) !== false) {
                $class = self::$elements->getInput()->$getElementType()->getElementClass();
            }
        }

        return $class;
    }
}
