<?php

namespace FWK\Core\Theme\Dtos;

use FWK\Core\Theme\Dtos\Traits\FiltrableFormFieldsTrait;
use SDK\Core\Dtos\Element;
use SDK\Core\Dtos\Traits\ElementTrait;
use FWK\Enums\Parameters;

/**
 * This is the 'FormFieldsShoppingListRowNote' class, a DTO class for the theme configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see FormFieldsShoppingListRowNote::getName()
 * @see FormFieldsShoppingListRowNote::getDescription()
 * @see FormFieldsShoppingListRowNote::getKeepPurchasedItems()
 * @see FormFieldsShoppingListRowNote::getDefaultOne()
 * @see FormFieldsShoppingListRowNote::getPriority()
 *
 * @see Element
 * 
 * @uses ElementTrait
 * @uses FiltrableFormFieldsTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class FormFieldsShoppingListRowNote extends Element {
    use ElementTrait, FiltrableFormFieldsTrait;

    public const COMMENT = Parameters::COMMENT;

    public const QUANTITY = Parameters::QUANTITY;

    public const IMPORTANCE = Parameters::IMPORTANCE;

    public const PRIORITY = Parameters::PRIORITY;

    private ?FormField $comment = null;

    private ?FormField $quantity = null;

    private ?FormField $importance = null;

    private ?FormField $priority = null;

    /**
     * This method returns if the comment FormField.
     *
     * @return FormField|Null
     */
    public function getComment(): ?FormField {
        return $this->comment;
    }

    private function setComment(array $comment): void {
        $this->comment = new FormField($comment);
    }

    /**
     * This method returns if the quantity FormField.
     *
     * @return FormField|Null
     */
    public function getQuantity(): ?FormField {
        return $this->quantity;
    }

    private function setQuantity(array $quantity): void {
        $this->quantity = new FormField($quantity);
    }

    /**
     * This method returns if the importance FormField.
     *
     * @return FormField|Null
     */
    public function getImportance(): ?FormField {
        return $this->importance;
    }

    private function setImportance(array $importance): void {
        $this->importance = new FormField($importance);
    }

    /**
     * This method returns if the priority FormField.
     *
     * @return FormField|Null
     */
    public function getPriority(): ?FormField {
        return $this->priority;
    }

    private function setPriority(array $priority): void {
        $this->priority = new FormField($priority);
    }
}
