<?php

namespace FWK\ViewHelpers\Product\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Form\Form;

/**
 * This is the RecommendForm class, a macro class for the productViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the product's recommend button.
 *
 * @see RecommendForm::getViewParameters()
 *
 * @package FWK\ViewHelpers\Product\Macro
 */
class RecommendForm {

    public ?Form $form = null;

    /**
     * Constructor method for RecommendForm class.
     * 
     * @see Form
     * 
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        ViewHelper::mergeArguments($this, $arguments);
    }

    /**
     * This method returns all calculated arguments and new parameters for ProductViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        if (is_null($this->form)) {
            throw new CommerceException("The value of [form] argument: '" . $this->form . "' is required " . self::class, CommerceException::VIEW_HELPER_UNDEFINED_ARGUMENT);
        }

        return $this->getProperties();
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        return [
            'form' => $this->form
        ];
    }
}