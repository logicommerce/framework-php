<?php

namespace FWK\ViewHelpers\User\Macro;

use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Form\Form;
use FWK\Core\ViewHelpers\ViewHelper;

/**
 * This is the SalesAgentSalesForm class, a macro class for the userViewHelper.
 * The purpose of this class is to encapsulate the logic to show sales from a sales agent.
 *
 * @see Orders::getViewParameters()
 * 
 * @package FWK\ViewHelpers\User\Macro
 * @deprecated
 */

class SalesAgentSalesForm {

    public ?Form $form = null;

    /**
     * Constructor method for ReturnRequestForm.
     *
     * @see ReturnRequestForm
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
