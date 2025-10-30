<?php

namespace FWK\ViewHelpers\Account\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use SDK\Core\Dtos\ElementCollection;
use SDK\Dtos\Accounts\CompanyStructureTreeNode;

/**
 * This is the CompanyStructure class, a macro class for the accountViewHelper.
 * The purpose of this class is to encapsulate the logic to show the company structure tree.
 *
 * @see CompanyStructure::getViewParameters()
 * 
 * @package FWK\ViewHelpers\Account\Macro
 */
class CompanyStructure {
    
    public const COMPANY_TREE = 'companyTree';
    public const CURRENT_USER = 'currentUser';
    public const PERMISSIONS = 'permissions';
    
    public const COMPANY_STRUCTURE_PARAMETERS = [
        self::COMPANY_TREE,
        self::CURRENT_USER,
        self::PERMISSIONS,
    ];
    
    public const ACTION_EDIT = 'actionEdit';
    public const ACTION_EMPLOYEES = 'actionEmployees';
    public const ACTION_ORDERS = 'actionOrders';
    public const ACTION_ADD_DIVISION = 'actionAddDivision';
    public const ACTION_DELETE = 'actionDelete';
    
    public array $availableActions = [
        self::ACTION_EDIT, 
        self::ACTION_EMPLOYEES, 
        self::ACTION_ORDERS, 
        self::ACTION_ADD_DIVISION, 
        self::ACTION_DELETE
    ];
    
    public ElementCollection|CompanyStructureTreeNode|null $companyStructure = null;
    public $currentUser = null;
    public array $permissions = [];
    
    /**
     * Constructor.
     */
    public function __construct() {
        $this->companyStructure = null;
        $this->permissions = [];
    }
    
    /**
     * Get view parameters for the company structure macro
     *
     * @param array $parameters
     * @return array
     */
    public function getViewParameters(array $parameters = []): array {
        return [
            'companyStructure' => $this->companyStructure,
            'currentUser' => $this->currentUser,
            'permissions' => $this->permissions,
            'availableActions' => $this->availableActions,
            'parameters' => $parameters
        ];
    }
    
    /**
     * Set company structure data
     *
     * @param ElementCollection|CompanyStructureTreeNode $companyStructure
     * @return self
     */
    public function setCompanyStructure(ElementCollection|CompanyStructureTreeNode $companyStructure): self {
        $this->companyStructure = $companyStructure;
        return $this;
    }
    
    /**
     * Set current user data
     *
     * @param mixed $currentUser
     * @return self
     */
    public function setCurrentUser($currentUser): self {
        $this->currentUser = $currentUser;
        return $this;
    }
    
    /**
     * Set permissions
     *
     * @param array $permissions
     * @return self
     */
    public function setPermissions(array $permissions): self {
        $this->permissions = $permissions;
        return $this;
    }
}