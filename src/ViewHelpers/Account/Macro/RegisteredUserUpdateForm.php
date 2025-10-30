<?php

namespace FWK\ViewHelpers\Account\Macro;

use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Form\Form;
use FWK\Core\ViewHelpers\ViewHelper;
use SDK\Core\Dtos\ElementCollection;
use SDK\Dtos\Accounts\MasterVal;

/**
 * This is the RegisteredUserUpdateForm class, a macro class for the accountViewHelper.
 * The purpose of this class is to encapsulate the logic to show customers form from a sales agent.
 *
 * @see Orders::getViewParameters()
 * 
 * @package FWK\ViewHelpers\User\Macro
 */
class RegisteredUserUpdateForm {
    public ?Form $form = null;
    public ?MasterVal $registeredUser = null;
    public ?ElementCollection $currencies = null;
    /**
     * Constructor method for RegisteredUserUpdateForm.
     *
     * @see RegisteredUserUpdateForm
     *
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        ViewHelper::mergeArguments($this, $arguments);
    }
    /**
     * This method returns all calculated arguments and new parameters for AccountViewHelper.php
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
            'registeredUser' => $this->registeredUser,
            'form' => $this->form,
            'currencies' => $this->currencies
        ];
    }
}
