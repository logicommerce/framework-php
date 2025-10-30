<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Core\Dtos\Element;

/**
 * This is the 'FormShoppingListRowNote' class, a DTO class for the form configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see FormShoppingListRowNote::getFields()
 *
 * @see Element
 * @see ElementTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class FormShoppingListRowNote extends Element {
    use ElementTrait;

    public const FIELDS = 'fields';

    private ?FormFieldsShoppingListRowNote $fields = null;

    /**
     * This method returns the array with the fields to set in the set user form.
     * 
     * @return FormFieldsShoppingListRowNote|NULL
     */
    public function getFields(): ?FormFieldsShoppingListRowNote {
        return $this->fields;
    }

    private function setFields(array $fields): void {
        $this->fields = new FormFieldsShoppingListRowNote($fields);
    }
}
