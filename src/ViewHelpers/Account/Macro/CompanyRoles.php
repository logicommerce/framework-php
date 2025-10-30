<?php

namespace FWK\ViewHelpers\Account\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use SDK\Core\Dtos\ElementCollection;
use FWK\Core\Form\Form;

/**
 * This is the companyRoles class, a macro class for the accountViewHelper.
 * The purpose of this class is to encapsulate the logic to show customers from a sales agent.
 *
 * @see Orders::getViewParameters()
 * 
 * @package FWK\ViewHelpers\Account\Macro
 */
class CompanyRoles {
    public const NAME = 'name';
    public const DESCRIPTION = 'description';
    public const TARGET = 'target';
	public const TARGET_DEFAULT = 'targetDefault';
	public const EMPLOYEES_QUANTITY = 'employeesQuantity';
    public const ACTIONS = 'action';
    public const COMPANY_ROLES_PARAMETERS = [
        self::NAME,
		self::DESCRIPTION,
		self::TARGET,
		self::TARGET_DEFAULT,
		self::EMPLOYEES_QUANTITY,
		self::ACTIONS,
    ];
    public const ACTION_COMPANY_ROLES_ADD = 'actionCompanyRolesAdd';
    public const ACTION_COMPANY_ROLES_EDIT = 'actionCompanyRolesEdit';
    public const ACTION_COMPANY_ROLES_DUPLICATE = 'actionCompanyRolesDuplicate';
    public const ACTION_COMPANY_ROLES_DELETE = 'actionCompanyRolesDelete';
    public array $availableActions = [
		self::ACTION_COMPANY_ROLES_ADD,
		self::ACTION_COMPANY_ROLES_EDIT,
		self::ACTION_COMPANY_ROLES_DUPLICATE,
		self::ACTION_COMPANY_ROLES_DELETE
	];
    public ?ElementCollection $companyRoles = null;
    public array $parameters = [];
    public string $errorMessage = '';
    /**
     * Constructor method for CompanyRoles.
     *
     * @see CompanyRoles
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
            'companyRoles' => $this->companyRoles,
            'parameters' => $this->parameters,
            'errorMessage' => $this->errorMessage,
            'availableActions' => $this->availableActions,
        ];
    }
}
