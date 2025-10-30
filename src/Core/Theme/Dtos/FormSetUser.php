<?php

namespace FWK\Core\Theme\Dtos;

use FWK\Core\Theme\Dtos\Traits\LocationFormFieldsTrait;
use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Core\Dtos\Element;

/**
 * This is the 'FormSetUser' class, a DTO class for the form configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see FormSetUser::getDefaultUserType()
 * @see FormSetUser::getUnavailableFieldsWithLogin()
 * @see FormSetUser::getUserFields()
 * @see FormSetUser::getAddressbookFields()
 * @see FormSetUser::getAvailableCustomTagPositions
 *
 * @see Element
 * @see ElementTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class FormSetUser extends Element {
    use ElementTrait, LocationFormFieldsTrait;

    public const DEFAULT_USER_TYPE = 'defaultUserType';
    
    public const UNAVAILABLE_FIELDS_WITH_LOGIN = 'unavailableFieldsWithLogin';
    
    public const AVAILABLE_CUSTOM_TAG_POSITIONS = 'availableCustomTagPositions';

    public const AVAILABLE_FIELDS_ONLY_WITH_LOGIN = 'availableFieldsOnlyWithLogin';

    public const AVAILABLE_FIELDS_FAST_REGISTER = 'availableFieldsFastRegister';

    public const USER_FIELDS = 'userFields';
    
    public const ADDRESSBOOK_FIELDS = 'addressbookFields';
    
    private string $defaultUserType = '';
    
    private array $availableCustomTagPositions = [];
    
    private ?FormFieldsSetUser $unavailableFieldsWithLogin = null;
    
    private ?FormFieldsSetUser $availableFieldsOnlyWithLogin = null;

    private ?FormFieldsSetUser $availableFieldsFastRegister = null;

    private ?FormSetUserFields $userFields = null;
    
    private ?FormSetUserFields $addressbookFields = null;
    
    /**
     * This method returns the default user type to show in the form.
     * 
     * @return string
     */
    public function getDefaultUserType(): string {
        return $this->defaultUserType;
    }

    private function setDefaultUserType(string $defaultUserType): void {
        $this->defaultUserType = $defaultUserType;
    }
    
    /**
     * This method returns the available custom tag positions to show in the form.
     * 
     * @return array
     */
    public function getAvailableCustomTagPositions(): array {
        return $this->availableCustomTagPositions;
    }

    private function setAvailableCustomTagPositions(array $availableCustomTagPositions): void {
        $this->availableCustomTagPositions = $availableCustomTagPositions;
    }

    /**
     * This method returns the FormFieldsSetUser with the unavailable fields to show when the user has login.
     *
     * @return FormFieldsSetUser|NULL
     */
    public function getUnavailableFieldsWithLogin(): ?FormFieldsSetUser {
        return $this->unavailableFieldsWithLogin;
        
    }
    
    private function setUnavailableFieldsWithLogin(array $unavailableFieldsWithLogin): void {
        $this->unavailableFieldsWithLogin = new FormFieldsSetUser($this->setLocationFields($unavailableFieldsWithLogin));
    }
    
    /**
     * This method returns the FormFieldsSetUser with the available fields to show when the user has login.
     *
     * @return FormFieldsSetUser|NULL
     */
    public function getAvailableFieldsOnlyWithLogin(): ?FormFieldsSetUser {
        return $this->availableFieldsOnlyWithLogin;
        
    }
    
    private function setAvailableFieldsOnlyWithLogin(array $availableFieldsOnlyWithLogin): void {
        $this->availableFieldsOnlyWithLogin = new FormFieldsSetUser($this->setLocationFields($availableFieldsOnlyWithLogin));
    }
    
    /**
     * This method returns the FormFieldsSetUser with the available fields for fast register .
     *
     * @return FormFieldsSetUser|NULL
     */
    public function getAvailableFieldsFastRegister(): ?FormFieldsSetUser {
        return $this->availableFieldsFastRegister;
        
    }
    
    private function setAvailableFieldsFastRegister(array $availableFieldsFastRegister): void {
        $this->availableFieldsFastRegister = new FormFieldsSetUser($this->setLocationFields($availableFieldsFastRegister));
    }

    /**
     * This method returns the array with the user fields.
     *
     * @return FormSetUserFields|NULL
     */
    public function getUserFields(): ?FormSetUserFields {
        return $this->userFields;
    }
    
    private function setUserFields(array $userFields): void {
        $this->userFields = new FormSetUserFields($userFields);
    }
    
    /**
     * This method returns the array with the addressbook fields.
     *
     * @return FormSetUserFields|NULL
     */
    public function getAddressbookFields(): ?FormSetUserFields {
        return $this->addressbookFields;
    }
    
    private function setAddressbookFields(array $addressbookFields): void {
        $this->addressbookFields = new FormSetUserFields($addressbookFields);
    }

}
