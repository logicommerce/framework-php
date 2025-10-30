<?php

namespace FWK\ViewHelpers\Page\Macro;

use FWK\Core\Form\Form;
use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;

/**
 * This is the SendMailForm class, a macro class for the PageViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the user's SendMailForm.
 *
 * @see SendMailForm::getViewParameters()
 *
 * @package FWK\ViewHelpers\Page\Macro
 */
class SendMailForm {

    public ?Form $form = null;

    /**
     * Constructor method for SendMailForm.
     * 
     * @see SendMailForm
     * 
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        ViewHelper::mergeArguments($this, $arguments);
    }

    /**
     * This method returns all calculated arguments and new parameters for PageViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        if (is_null($this->form)) {
            throw new CommerceException("The value of [form] argument: '" . $this->form . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
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
