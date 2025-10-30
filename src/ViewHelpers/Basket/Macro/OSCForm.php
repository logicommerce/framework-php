<?php

namespace FWK\ViewHelpers\Basket\Macro;

use FWK\Core\ViewHelpers\ViewHelper;

/**
 * This is the one step checkout form class, a macro class for the basket viewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the basket's one step checkout form.
 *
 * @see OSCForm::getViewParameters()
 *
 * @package FWK\ViewHelpers\Basket\Macro
 */
class OSCForm {

    public ?string $content = null;

    public string $class = '';

    /**
     * Constructor method for OSCForm class.
     * 
     * @see OSCForm
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
    private function getProperties(): array {
        return [
            'content' => $this->content,
            'class' => $this->class
        ];
    }
}
