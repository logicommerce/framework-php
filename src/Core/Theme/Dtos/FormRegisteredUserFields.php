<?php

namespace FWK\Core\Theme\Dtos;

use FWK\Core\Theme\Dtos\Traits\FiltrableFormFieldsTrait;
use SDK\Core\Dtos\Element;
use SDK\Core\Dtos\Traits\ElementTrait;
use FWK\Enums\Parameters;

/**
 * This is the 'FormRegisteredUserFields' class, a DTO class for the theme configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see FormRegisteredUserFields::getGender()
 * @see FormRegisteredUserFields::getFirstName()
 * @see FormRegisteredUserFields::getLastName()
 * @see FormRegisteredUserFields::getEmail()
 * @see FormRegisteredUserFields::getUsername()
 * @see FormRegisteredUserFields::getRegisteredUserPId()
 * @see FormRegisteredUserFields::getBirthday()
 * @see FormRegisteredUserFields::getImage()
 * @see FormRegisteredUserFields::getRole()
 * @see FormRegisteredUserFields::getJob()
 * 
 *
 * @see Element
 * @see ElementTrait
 * @see FiltrableFormFieldsTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class FormRegisteredUserFields extends Element {
    use ElementTrait, FiltrableFormFieldsTrait;

    public const GENDER = Parameters::GENDER;

    public const FIRST_NAME = Parameters::FIRST_NAME;

    public const LAST_NAME = Parameters::LAST_NAME;

    public const REGISTERED_USER_EMAIL = Parameters::REGISTERED_USER_EMAIL;

    public const REGISTERED_USER_IMAGE = Parameters::REGISTERED_USER_IMAGE;

    public const REGISTERED_USER_USERNAME = Parameters::REGISTERED_USER_USERNAME;

    public const REGISTERED_USER_P_ID = Parameters::REGISTERED_USER_P_ID;

    public const BIRTHDAY = Parameters::BIRTHDAY;

    public const SUBSCRIBED = Parameters::SUBSCRIBED;

    public const ROLE = Parameters::ROLE_ID;

    public const JOB = Parameters::JOB;

    private ?FormField $gender = null;

    private ?FormField $firstName = null;

    private ?FormField $lastName = null;

    private ?FormField $registeredUserEmail = null;

    private ?FormField $registeredUserImage = null;

    private ?FormField $registeredUserPId = null;

    private ?FormField $registeredUserUsername = null;

    private ?FormField $birthday = null;

    private ?FormField $subscribed = null;

    private ?FormField $roleId = null;

    private ?FormField $job = null;

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
     * This method returns if the registeredUserImage FormField.
     *
     * @return FormField|Null
     */
    public function getRegisteredUserImage(): ?FormField {
        return $this->registeredUserImage;
    }

    private function setRegisteredUserImage(array $registeredUserImage): void {
        $this->registeredUserImage = new FormField($registeredUserImage);
    }

    /**
     * This method returns if the subscribed FormField.
     *
     * @return FormField|Null
     */
    public function getSubscribed(): ?FormField {
        return $this->subscribed;
    }

    private function setSubscribed(array $subscribed): void {
        $this->subscribed = new FormField($subscribed);
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

    /**
     * This method returns if the role FormField.
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
