<?php

namespace FWK\ViewHelpers\User\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Form\Form;

/**
 * This is the SmallNewsletterForm class, a macro class for the userViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the user's smallNewsletter.
 *
 * @see SmallNewsletterForm::getViewParameters()
 *
 * @package FWK\ViewHelpers\User\Macro
 */
class SmallNewsletterForm {

    public ?Form $form = null;

    public bool $showLabel = true;

    public bool $showPlaceholder = false;

    public bool $disableValidationMessages = true;

    public bool $hiddeWithLogin = true;

    public bool $addLegalCheck = true;

    /**
     * Constructor method for SmallNewsletterForm.
     * 
     * @see SmallNewsletterForm
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
            'disableValidationMessages' => $this->disableValidationMessages,
            'hiddeWithLogin' => $this->hiddeWithLogin,
            'addLegalCheck' => $this->addLegalCheck
        ];
    }
}
