<?php

namespace FWK\Core\Theme\Dtos;

use FWK\Core\Theme\Dtos\Traits\LocationFormFieldsTrait;
use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Core\Dtos\Element;

/**
 * This is the 'FormSetUserFields' class, a DTO class for the form configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see FormSetUserFields::getFieldsByUserType()
 *
 * @see Element
 * 
 * @uses ElementTrait
 * @uses LocationFormFieldsTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class FormSetUserFields extends Element {
    use ElementTrait, LocationFormFieldsTrait;

    public const FIELDS_BY_USER_TYPE = 'fieldsByUserType';
    
    public const SHIPPING_FIELDS = 'shippingFields';

    private array $fieldsByUserType = [];
    
    private ?FormFieldsSetUser $shippingFields = null;
    
    /**
     * This method returns the array fields to apply by user type.
     * 
     * @return array|NULL
     */
    public function getFieldsByUserType(): array {
        return $this->fieldsByUserType;
    }

    private function setFieldsByUserType(array $fieldsByUserType): void {
        foreach ($fieldsByUserType as $userType => $fields ){
            $this->fieldsByUserType[$userType] = new FormSetUserFieldsByUserType($fields);
        }
    }

    /**
     * This method returns and array with the user types sorted and filtered.
     *
     * @return array
     */
    public function getSortFilterArrayFieldsByUserType(bool $included = true, bool $asc = true): array
    {
        $sortUserType = [];
        foreach ($this->fieldsByUserType as $name => $userType) {
            if ($userType !== null && $userType->getIncluded() === $included) {
                $fieldsByUserType = $userType;
                $fieldsByUserType->setKeyName($name);
                $sortUserType[] = $fieldsByUserType;
            }
        }
        $userTypeA = $userTypeB = null;
        if ($asc) {
            usort($sortUserType, fn (FormSetUserFieldsByUserType $userTypeA, FormSetUserFieldsByUserType $userTypeB) => ($userTypeA->getPriority() <=> $userTypeB->getPriority()));
        } else {
            usort($sortUserType, fn (FormSetUserFieldsByUserType $userTypeA, FormSetUserFieldsByUserType $userTypeB) => - ($userTypeA->getPriority() <=> $userTypeB->getPriority()));
        }
        $result = [];
        foreach ($sortUserType as $row) {
            $keyName = $row->getKeyName();
            $result[$keyName] = $row;
            unset($keyName);
        }
        return $result;
    }


    /**
     * This method returns the array with the shipping fields.
     *
     * @return FormFieldsSetUser|NULL
     */
    public function getShippingFields(): ?FormFieldsSetUser {
        return $this->shippingFields;
    }
    
    private function setShippingFields(array $shippingFields): void {
        $this->shippingFields = new FormFieldsSetUser($this->setLocationFields($shippingFields), false);
    }
}
