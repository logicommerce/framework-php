<?php

namespace FWK\Core\ViewHelpers\Macros;

use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Form\Form;
use FWK\Core\ViewHelpers\ViewHelper;
use SDK\Core\Dtos\Element;

/**
 * This is the Modal class, a macro class for the productViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the product's buttonClearProductsFilter.
 *
 * @package FWK\ViewHelpers\Product\Macro
 */
class Modal {

    public bool $showHeader = true;

    public string $headerTitle = '';

    public string $dialogClasses = '';

    public ?Element $element = null;

    /**
     * Constructor method for Form.
     *
     * @see Form
     *
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        ViewHelper::mergeArguments($this, $arguments);
    }

    /**
     * This method returns all calculated arguments and new parameters for UserViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        return $this->getProperties();
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        return [
            'showHeader' => $this->showHeader,
            'headerTitle' => $this->headerTitle,
            'dialogClasses' => $this->dialogClasses,
            'element' => $this->element
        ];
    }
}
