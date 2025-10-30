<?php

namespace FWK\ViewHelpers\Account\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use SDK\Core\Dtos\ElementCollection;
use FWK\Core\Form\Form;

/**
 * This is the registeredUsers class, a macro class for the accountViewHelper.
 * The purpose of this class is to encapsulate the logic to show customers from a sales agent.
 *
 * @see Orders::getViewParameters()
 * 
 * @package FWK\ViewHelpers\Account\Macro
 */
class RegisteredUsers {
    public const SELECTED = 'selected';
    public const FIRSTNAME = 'firstName';
    public const LASTNAME = 'lastName';
    public const USERNAME = 'userName';
    public const EMAIL = 'email';
    public const ID = 'id';
    public const MASTER = 'master';
    public const ROLE = 'role';
    public const STATUS = 'status';
    public const DATEADDED = 'dateAdded';
    public const LASTUSED = 'lastUsed';
    public const ACTIONS = 'action';
    public const REGISTERED_USERS_PARAMETERS = [
        self::SELECTED,
        self::FIRSTNAME,
        self::LASTNAME,
        self::USERNAME,
        self::EMAIL,
        self::ID,
        self::MASTER,
        self::ROLE,
        self::STATUS,
        self::DATEADDED,
        self::LASTUSED,
        self::ACTIONS,
    ];
    public const ACTION_REGISTERED_USERS_ADD = 'actionRegisteredUsersAdd';
    public const ACTION_REGISTERED_USERS_EDIT = 'actionRegisteredUsersEdit';
    public const ACTION_REGISTERED_USERS_MOVE = 'actionRegisteredUsersMove';
    public const ACTION_REGISTERED_USERS_DELETE = 'actionRegisteredUsersDelete';
    public array $availableActions = [self::ACTION_REGISTERED_USERS_ADD, self::ACTION_REGISTERED_USERS_EDIT, self::ACTION_REGISTERED_USERS_MOVE, self::ACTION_REGISTERED_USERS_DELETE];
    public ?ElementCollection $registeredUsers = null;
    public array $parameters = [];
    public string $errorMessage = '';
    public string $accountId = '';
    /**
     * Constructor method for RegisteredUsers.
     *
     * @see RegisteredUsers
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
        return $this->getProperties();
    }
    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        return [
            'registeredUsers' => $this->registeredUsers,
            'parameters' => $this->parameters,
            'errorMessage' => $this->errorMessage,
            'accountId' => $this->accountId,
            'availableActions' => $this->availableActions,
        ];
    }
}
