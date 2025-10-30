<?php

namespace FWK\Core\Theme\Dtos;

use FWK\Core\Theme\Dtos\Traits\LocationFormFieldsTrait;
use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Core\Dtos\Element;

/**
 * This is the 'FormSetUserFieldsByUserTypeElement' class, a DTO class for the form configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see FormSetUserFieldsByUserTypeElement::getFields()
 *
 * @see Element
 * @see ElementTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class FormSetUserFieldsByUserTypeElement extends Element {
    use ElementTrait, LocationFormFieldsTrait;

    public const FIELDS = 'fields';
        
    private ?FormFieldsSetUser $fields = null;
    
    /**
     * This method returns the array with the fields to set in the set user form.
     * 
     * @return FormFieldsSetUser|NULL
     */
    public function getFields(): ?FormFieldsSetUser {
        return $this->fields;
    }

    private function setFields(array $fields): void {
        $this->fields = new FormFieldsSetUser($this->setLocationFields($fields));
    }

}


