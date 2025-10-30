<?php

namespace FWK\Core\Resources;

/**
 * This is the Metatag class.
 *
 * @see Metatag::getName()
 * @see Metatag::getProperty()
 * @see Metatag::getContent()
 * @see Metatag::getHttpEquiv()
 * @see Metatag::addContent()
 * @see Metatag::output()
 *
 * @package FWK\Core\Resources
 */
class Metatag {

    public const NAME = 'name';

    public const PROPERTY = 'property';

    public const CONTENT = 'content';

    public const HTTP_EQUIV = 'http-equiv';

    private string $name = '';

    private string $property = '';

    private string $content = '';

    private string $httpEquiv = '';


    /**
     * This method sets the http-equiv tag of the metatag and returns self.
     *
     * @param string $httpEquiv
     *
     * @return self
     */
    public function setHttpEquiv(string $httpEquiv): Metatag {
        $this->httpEquiv = $httpEquiv;
        return $this;
    }

    /**
     * This method sets the name tag of the metatag and returns self.
     * 
     * @param string $name
     * 
     * @return self
     */
    public function setName(string $name): Metatag {
        $this->name = $name;
        return $this;
    }

    /**
     * This method sets the property tag of the metatag and returns self.
     * 
     * @param string $propertyç
     * 
     * @return self
     */
    public function setProperty(string $property): Metatag {
        $this->property = $property;
        return $this;
    }

    /**
     * This method sets the content tag of the metatag and returns self.
     * 
     * @param string $content
     * 
     * @return self
     */
    public function setContent(string $content): Metatag {
        $this->content = $content;
        return $this;
    }

    /**
     * Returns the name metatag value.
     *
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * Returns the property metatag value.
     * 
     * @return string
     */
    public function getProperty(): string {
        return $this->property;
    }

    /**
     * Returns the content metatag value.
     *
     * @return string
     */
    public function getContent(): string {
        return $this->content;
    }

    /**
     * Returns the http-equiv metatag value.
     *
     * @return string
     */
    public function getHttpEquiv(): string {
        return $this->httpEquiv;
    }

    /**
     * Add content to the content metatag value.
     * 
     * @param string $content
     */
    public function addContent(string $content): Metatag {
        if (strlen($this->content)) {
            $this->content = $this->content . ' ' . $content;
        } else {
            $this->content = $content;
        }
        return $this;
    }

    /**
     * Returns the full output of the metatag.
     *
     * @param string $content
     */
    public function output(): string {
        $output = '';
        $meta = false;
        $content = false;
        if (strlen($this->name)) {
            $output .= self::NAME . '="' . htmlspecialchars($this->name, ENT_QUOTES, CHARSET) . '" ';
            $meta = true;
        }
        if (strlen($this->httpEquiv)) {
            $output .= self::HTTP_EQUIV . '="' . htmlspecialchars($this->httpEquiv, ENT_QUOTES, CHARSET) . '" ';
            $meta = true;
        }
        if (strlen($this->property)) {
            $output .= self::PROPERTY . '="' . htmlspecialchars($this->property, ENT_QUOTES, CHARSET) . '" ';
            $meta = true;
        }
        if (strlen($this->content)) {
            $output .= self::CONTENT . '="' . htmlspecialchars($this->content, ENT_QUOTES, CHARSET) . '" ';
            $content = true;
        }
        if ($meta && $content) {
            return '<meta ' . rtrim($output) . '>';
        }
        return '';
    }
}
