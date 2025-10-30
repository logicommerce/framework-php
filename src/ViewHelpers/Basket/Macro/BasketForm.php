<?php

namespace FWK\ViewHelpers\Basket\Macro;

use FWK\Core\ViewHelpers\ViewHelper;

/**
 * This is the BasketForm class, a macro class for the basket viewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the basket's form.
 *
 * @see BasketForm::getViewParameters()
 *
 * @package FWK\ViewHelpers\Basket\Macro
 */
class BasketForm {

    public ?string $content = null;

    public string $class = '';

    /**
     * Constructor method for BasketForm class.
     * 
     * @see BasketForm
     * 
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        ViewHelper::mergeArguments($this, $arguments);
    }
    
    /**
     * This method returns all calculated arguments and new parameters for BasketViewHelper.php
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
            'content' => $this->content,
            'class' => $this->class
        ];
    }
}