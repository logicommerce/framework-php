<?php

namespace FWK\ViewHelpers\Account\Macro;

use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Form\Form;
use FWK\Core\ViewHelpers\ViewHelper;
use SDK\Dtos\Accounts\Master;

/**
 * This is the RegisteredUserForm class, a macro class for the account view helper.
 * The purpose of this class is to encapsulate the logic that filters company roles based on the provided parameters.
 *
 * @see RegisteredUserForm::getViewParameters()
 *
 * @package FWK\ViewHelpers\Account\Macro
 */
class RegisteredUserForm {

    public ?Form $form = null;
    public ?Master $registeredUser = null;

    /**
     * Constructor method for CompanyRolesFilter.
     *
     * @see CompanyRolesFilter
     *
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        ViewHelper::mergeArguments($this, $arguments);
    }

    /**
     * This method returns all calculated arguments and new parameters for CompanyRolesViewHelper.php
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
            'form' => $this->form,
            'registeredUser' => $this->registeredUser
        ];
    }
}
