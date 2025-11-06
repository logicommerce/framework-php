<?php

namespace FWK\Core\Theme\Dtos;

use FWK\Enums\Parameters;
use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Core\Dtos\Element;

/**
 * This is the 'FormAccount' class, a DTO class for the form configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see FormSetUser::getFields()
 * @see FormSetUser::getMaster()
 * @see FormSetUser::getAccountRegisteredUserFields()
 * @see FormSetUser::getCompanyDivision()
 * @see FormSetUser::getCompanyRoles()
 * @see FormSetUser::getCustomTags()
 * @see FormSetUser::getAddressBook()
 *
 * @see Element
 * @see ElementTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class FormAccount extends Element {
    use ElementTrait;

    public const FIELDS = 'fields';

    public const MASTER = 'master';

    public const CUSTOM_TAGS = Parameters::CUSTOM_TAGS;

    public const ADDRESS_BOOK = Parameters::ADDRESS_BOOK;

    public const ACCOUNT_REGISTERED_USER_FIELDS = 'accountRegisteredUserFields';

    public const COMPANY_DIVISION = 'companyDivision';

    public const COMPANY_ROLES = 'companyRoles';

    private ?FormAccountFields $fields = null;

    private ?FormMaster $master = null;

    private ?FormField $customTags = null;

    private ?FormField $addressBook = null;

    private ?FormAccountRegisteredUserFields $accountRegisteredUserFields = null;

    private ?FormCompanyDivision $companyDivision = null;

    private ?FormCompanyRoles $companyRoles = null;

    /**
     * This method returns the form account fields configurations.
     *
     * @return FormAccountFields|NULL
     */
    public function getFields(): ?FormAccountFields {
        return $this->fields;
    }

    private function setFields(array $fields): void {
        $this->fields = new FormAccountFields($fields);
    }

    /**
     * This method returns the master form configurations.
     *
     * @return FormMaster|NULL
     */
    public function getMaster(): ?FormMaster {
        return $this->master;
    }

    private function setMaster(array $master): void {
        $this->master = new FormMaster($master);
    }

    /**
     * This method returns the account registered user fields form configurations.
     *
     * @return FormAccountRegisteredUserFields|NULL
     */
    public function getAccountRegisteredUserFields(): ?FormAccountRegisteredUserFields {
        return $this->accountRegisteredUserFields;
    }

    private function setAccountRegisteredUserFields(array $accountRegisteredUserFields): void {
        $this->accountRegisteredUserFields = new FormAccountRegisteredUserFields($accountRegisteredUserFields);
    }

    /**
     * This method returns the company division form configurations.
     *
     * @return FormCompanyDivision|NULL
     */
    public function getCompanyDivision(): ?FormCompanyDivision {
        return $this->companyDivision;
    }

    private function setCompanyDivision(array $companyDivision): void {
        $this->companyDivision = new FormCompanyDivision($companyDivision);
    }

    /**
     * This method returns the company roles form configurations.
     *
     * @return FormCompanyRoles|NULL
     */
    public function getCompanyRoles(): ?FormCompanyRoles {
        return $this->companyRoles;
    }

    private function setCompanyRoles(array $companyRoles): void {
        $this->companyRoles = new FormCompanyRoles($companyRoles);
    }

    /**
     * This method returns if the customTags FormField.
     *
     * @return FormField|Null
     */
    public function getCustomTags(): ?FormField {
        return $this->customTags;
    }

    private function setCustomTags(array $customTags): void {
        $this->customTags = new FormField($customTags);
    }

    /**
     * This method returns if the addressBook FormField.
     *
     * @return FormField|Null
     */
    public function getAddressBook(): ?FormField {
        return $this->addressBook;
    }

    private function setAddressBook(array $addressBook): void {
        $this->addressBook = new FormField($addressBook);
    }
}
