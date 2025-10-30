<?php

namespace FWK\ViewHelpers\Account\Macro;

use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Form\Form;
use FWK\Core\ViewHelpers\ViewHelper;

/**
 * This is the RegisteredUsersForm class, a macro class for the accountViewHelper.
 * The purpose of this class is to encapsulate the logic to show customers form from a sales agent.
 *
 * @see Orders::getViewParameters()
 * 
 * @package FWK\ViewHelpers\Account\Macro
 */
class RegisteredUsersForm {
    public const Q = 'q';
    public const FIRSTNAME = 'firstName';
    public const LASTNAME = 'lastName';
    public const STATUS_LIST = 'statusList';
    public const ROLE_ID = 'roleId';
    public const MASTER = 'master';
    public const ADDED_FROM = 'addedFrom';
    public const ADDED_TO = 'addedTo';

    public const REGISTERED_USERS_FORM_PARAMETERS = [
        self::Q,
        self::FIRSTNAME,
        self::LASTNAME,
        self::STATUS_LIST,
        self::ROLE_ID,
        self::MASTER,
        self::ADDED_FROM,
        self::ADDED_TO,
    ];

    public ?Form $form = null;
    public array $parameters = [];

    /**
     * Constructor method for RegisteredUsersForm.
     *
     * @see RegisteredUsersForm
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
            'form' => $this->form,
            'parameters' => $this->parameters
        ];
    }
}
