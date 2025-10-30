<?php

namespace FWK\ViewHelpers\Blog\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Form\Form;

/**
 * This is the SubscribeForm class, a macro class for the blogViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the user's SubscribeForm.
 *
 * @see SubscribeForm::getViewParameters()
 *
 * @package FWK\ViewHelpers\Blog\Macro
 */
class SubscribeForm {

    public ?Form $form = null;

    public bool $showLabel = true;

    public bool $showPlaceholder = false;

    public bool $disableValidationMessages = true;

    /**
     * Constructor method for SubscribeForm.
     * 
     * @see SubscribeForm
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
            'form' => $this->form,
            'showLabel' => $this->showLabel,
            'showPlaceholder' => $this->showPlaceholder,
            'disableValidationMessages' => $this->disableValidationMessages
        ];
    }
}
