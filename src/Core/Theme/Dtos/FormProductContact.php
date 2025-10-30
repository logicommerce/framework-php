<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Core\Dtos\Element;

/**
 * This is the 'FormProductContact' class, a DTO class for the form configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see FormProductContact::getFields()
 *
 * @see Element
 * @see ElementTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class FormProductContact extends Element {
    use ElementTrait;

    public const FIELDS = 'fields';

    private ?FormFieldsProductContact $fields = null;

    /**
     * This method returns the array with the fields to set in the set user form.
     * 
     * @return FormFieldsProductContact|NULL
     */
    public function getFields(): ?FormFieldsProductContact {
        return $this->fields;
    }

    private function setFields(array $fields): void {
        $this->fields = new FormFieldsProductContact($fields);
    }
}
