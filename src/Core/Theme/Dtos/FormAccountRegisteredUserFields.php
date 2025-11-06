<?php

namespace FWK\Core\Theme\Dtos;

use FWK\Core\Theme\Dtos\Traits\FiltrableFormFieldsTrait;
use SDK\Core\Dtos\Element;
use SDK\Core\Dtos\Traits\ElementTrait;
use FWK\Enums\Parameters;

/**
 * This is the 'FormAccountRegisteredUserFields' class, a DTO class for the theme configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see FormAccountRegisteredUserFields::getGender()
 * @see FormAccountRegisteredUserFields::getFirstName()
 * @see FormAccountRegisteredUserFields::getLastName()
 * @see FormAccountRegisteredUserFields::getEmail()
 * @see FormAccountRegisteredUserFields::getUsername()
 * @see FormAccountRegisteredUserFields::getRegisteredUserPId()
 * @see FormAccountRegisteredUserFields::getBirthday()
 * @see FormAccountRegisteredUserFields::getImage()
 * @see FormAccountRegisteredUserFields::getRole()
 * @see FormAccountRegisteredUserFields::getJob()
 * 
 *
 * @see Element
 * @see ElementTrait
 * @see FiltrableFormFieldsTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class FormAccountRegisteredUserFields extends Element {
    use ElementTrait, FiltrableFormFieldsTrait;

    public const MASTER = Parameters::MASTER;

    public const ACCOUNT_ALIAS = Parameters::ACCOUNT_ALIAS;

    public const REGISTERED_USER_STATUS = Parameters::REGISTERED_USER_STATUS;

    public const ROLE = Parameters::ROLE_ID;

    public const JOB = Parameters::JOB;

    private ?FormField $master = null;

    private ?FormField $accountAlias = null;

    private ?FormField $registeredUserStatus = null;

    private ?FormField $roleId = null;

    private ?FormField $job = null;

    /**
     * This method returns if the master FormField.
     *
     * @return FormField|Null
     */
    public function getMaster(): ?FormField {
        return $this->master;
    }

    private function setMaster(array $master): void {
        $this->master = new FormField($master);
    }

    /**
     * This method returns if the accountAlias FormField.
     *
     * @return FormField|Null
     */
    public function getAccountAlias(): ?FormField {
        return $this->accountAlias;
    }

    private function setAccountAlias(array $accountAlias): void {
        $this->accountAlias = new FormField($accountAlias);
    }

    /**
     * This method returns if the registeredUserStatus FormField.
     *
     * @return FormField|Null
     */
    public function getRegisteredUserStatus(): ?FormField {
        return $this->registeredUserStatus;
    }

    private function setRegisteredUserStatus(array $registeredUserStatus): void {
        $this->registeredUserStatus = new FormField($registeredUserStatus);
    }

    /**
     * This method returns if the roleId FormField.
     *
     * @return FormField|Null
     */
    public function getRoleId(): ?FormField {
        return $this->roleId;
    }

    private function setRoleId(array $roleId): void {
        $this->roleId = new FormField($roleId);
    }

    /**
     * This method returns if the job FormField.
     *
     * @return FormField|Null
     */
    public function getJob(): ?FormField {
        return $this->job;
    }

    private function setJob(array $job): void {
        $this->job = new FormField($job);
    }
}
