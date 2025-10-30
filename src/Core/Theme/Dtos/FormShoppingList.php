<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Core\Dtos\Element;

/**
 * This is the 'FormShoppingList' class, a DTO class for the form configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see FormShoppingList::getNewFields()
 * @see FormShoppingList::getEditFields()
 *
 * @see Element
 * @see ElementTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class FormShoppingList extends Element {
    use ElementTrait;

    public const NEW_FIELDS = 'newFields';

    public const EDIT_FIELDS = 'editFields';

    private ?FormFieldsShoppingList $newFields = null;

    private ?FormFieldsShoppingList $editFields = null;

    /**
     * This method returns the array with the new fields to set in the set shopping list form.
     * 
     * @return FormFieldsShoppingList|NULL
     */
    public function getNewFields(): ?FormFieldsShoppingList {
        return $this->newFields;
    }

    private function setNewFields(array $newFields): void {
        $this->newFields = new FormFieldsShoppingList($newFields);
    }

    /**
     * This method returns the array with the edit fields to set in the set shopping list form.
     * 
     * @return FormFieldsShoppingList|NULL
     */
    public function getEditFields(): ?FormFieldsShoppingList {
        return $this->editFields;
    }

    private function setEditFields(array $editFields): void {
        $this->editFields = new FormFieldsShoppingList($editFields);
    }
}
