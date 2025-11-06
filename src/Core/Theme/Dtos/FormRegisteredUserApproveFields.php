<?php

namespace FWK\Core\Theme\Dtos;

use FWK\Core\Theme\Dtos\Traits\FiltrableFormFieldsTrait;
use SDK\Core\Dtos\Element;
use SDK\Core\Dtos\Traits\ElementTrait;
use FWK\Enums\Parameters;

/**
 * This is the 'FormRegisteredUserApproveFields' class, a DTO class for the theme configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see FormRegisteredUserApproveFields::getGender()
 * @see FormRegisteredUserApproveFields::getFirstName()
 * @see FormRegisteredUserApproveFields::getLastName()
 * @see FormRegisteredUserApproveFields::getEmail()
 * @see FormRegisteredUserApproveFields::getUsername()
 * @see FormRegisteredUserApproveFields::getRegisteredUserPId()
 * @see FormRegisteredUserApproveFields::getBirthday()
 * 
 *
 * @see Element
 * @see ElementTrait
 * @see FiltrableFormFieldsTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class FormRegisteredUserApproveFields extends Element {
    use ElementTrait, FiltrableFormFieldsTrait;

    public const GENDER = Parameters::GENDER;

    public const FIRST_NAME = Parameters::FIRST_NAME;

    public const LAST_NAME = Parameters::LAST_NAME;

    public const REGISTERED_USER_EMAIL = Parameters::REGISTERED_USER_EMAIL;

    public const REGISTERED_USER_USERNAME = Parameters::REGISTERED_USER_USERNAME;

    public const REGISTERED_USER_P_ID = Parameters::REGISTERED_USER_P_ID;

    public const BIRTHDAY = Parameters::BIRTHDAY;

    private ?FormField $gender = null;

    private ?FormField $firstName = null;

    private ?FormField $lastName = null;

    private ?FormField $registeredUserEmail = null;

    private ?FormField $registeredUserPId = null;

    private ?FormField $registeredUserUsername = null;

    private ?FormField $birthday = null;

    /**
     * This method returns if the gender FormField.
     *
     * @return FormField|Null
     */
    public function getGender(): ?FormField {
        return $this->gender;
    }

    private function setGender(array $gender): void {
        $this->gender = new FormField($gender);
    }

    /**
     * This method returns if the firstName FormField.
     *
     * @return FormField|Null
     */
    public function getFirstName(): ?FormField {
        return $this->firstName;
    }

    private function setFirstName(array $firstName): void {
        $this->firstName = new FormField($firstName);
    }

    /**
     * This method returns if the lastName FormField.
     *
     * @return FormField|Null
     */
    public function getLastName(): ?FormField {
        return $this->lastName;
    }

    private function setLastName(array $lastName): void {
        $this->lastName = new FormField($lastName);
    }

    /**
     * This method returns if the registeredUserEmail FormField.
     *
     * @return FormField|Null
     */
    public function getRegisteredUserEmail(): ?FormField {
        return $this->registeredUserEmail;
    }

    private function setRegisteredUserEmail(array $registeredUserEmail): void {
        $this->registeredUserEmail = new FormField($registeredUserEmail);
    }

    /**
     * This method returns if the registeredUserImage FormField.
     *
     * @return FormField|Null
     */
    public function getRegisteredUserUsername(): ?FormField {
        return $this->registeredUserUsername;
    }

    private function setRegisteredUserUsername(array $registeredUserUsername): void {
        $this->registeredUserUsername = new FormField($registeredUserUsername);
    }

    /**
     * This method returns if the registeredUserPId FormField.
     *
     * @return FormField|Null
     */
    public function getRegisteredUserPId(): ?FormField {
        return $this->registeredUserPId;
    }

    private function setRegisteredUserPId(array $registeredUserPId): void {
        $this->registeredUserPId = new FormField($registeredUserPId);
    }

    /**
     * This method returns if the birthday FormField.
     *
     * @return FormField|Null
     */
    public function getBirthday(): ?FormField {
        return $this->birthday;
    }

    private function setBirthday(array $birthday): void {
        $this->birthday = new FormField($birthday);
    }
}
