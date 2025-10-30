<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Element;
use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Enums\GeneralRestriction;

/**
 * This is the 'Account' class, a DTO class for the theme configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see Account::getRegisteredUsersList()
 *
 * @see Element
 * @see ElementTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class Account extends Element {
    use ElementTrait;
    public const USED_ACCOUNT_PATH = 'usedAccountPath';
    public const REGISTERED_USERS_LIST = 'registeredUsersList';
    public const COMPANY_ROLES_LIST = 'companyRolesList';
    public const ACCOUNT_TYPE = 'accountType';

    private bool $usedAccountPath = false;
    private ?ItemList $registeredUsersList = null;
    private ?ItemList $companyRolesList = null;
    private ?string $accountType = GeneralRestriction::ONLY_GENERAL;


    /**
     * Account path used
     *
     * @return bool
     */
    public function usedAccountPath(): bool {
        return $this->usedAccountPath;
    }

    private function setUsedAccountPath(bool $usedAccountPath): void {
        $this->usedAccountPath = $usedAccountPath;
    }

    /**
     * This method returns the registeredUsersList configuration.
     *
     * @return ItemList|NULL
     */
    public function getRegisteredUsersList(): ?ItemList {
        return $this->registeredUsersList;
    }
    private function setRegisteredUsersList(array $registeredUsersList): void {
        $this->registeredUsersList = new ItemList($registeredUsersList);
    }

    /**
     * This method returns the companyRolesList configuration.
     *
     * @return ItemList|NULL
     */
    public function getCompanyRolesList(): ?ItemList {
        return $this->companyRolesList;
    }

    private function setCompanyRolesList(array $companyRolesList): void {
        $this->companyRolesList = new ItemList($companyRolesList);
    }

    /**
     * This method returns the accountType configuration dependent on the GeneralRestriction enumeration.
     *
     * @return string|NULL
     */
    public function getAccountType(): ?string {
        return $this->accountType;
    }
    private function setAccountType(string $accountType): void {
        $this->accountType = $accountType;
    }
}
