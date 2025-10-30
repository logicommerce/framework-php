<?php

namespace FWK\Core\Theme\Dtos;

use FWK\Core\Theme\Dtos\Traits\FiltrableFormFieldsTrait;
use SDK\Core\Dtos\Element;
use SDK\Core\Dtos\Traits\ElementTrait;
use FWK\Enums\Parameters;

/**
 * This is the 'FormFieldsShoppingList' class, a DTO class for the theme configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see FormFieldsShoppingList::getName()
 * @see FormFieldsShoppingList::getDescription()
 * @see FormFieldsShoppingList::getKeepPurchasedItems()
 * @see FormFieldsShoppingList::getDefaultOne()
 * @see FormFieldsShoppingList::getPriority()
 *
 * @see Element
 * 
 * @uses ElementTrait
 * @uses FiltrableFormFieldsTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class FormFieldsShoppingList extends Element {
    use ElementTrait, FiltrableFormFieldsTrait;

    public const NAME = Parameters::NAME;

    public const DESCRIPTION = Parameters::DESCRIPTION;

    public const KEEP_PURCHASED_ITEMS = Parameters::KEEP_PURCHASED_ITEMS;

    public const DEFAULT_ONE = Parameters::DEFAULT_ONE;

    public const PRIORITY = Parameters::PRIORITY;

    private ?FormField $name = null;

    private ?FormField $description = null;

    private ?FormField $keepPurchasedItems = null;

    private ?FormField $defaultOne = null;

    private ?FormField $priority = null;

    /**
     * This method returns if the name FormField.
     *
     * @return FormField|Null
     */
    public function getName(): ?FormField {
        return $this->name;
    }

    private function setName(array $name): void {
        $this->name = new FormField($name);
    }

    /**
     * This method returns if the description FormField.
     *
     * @return FormField|Null
     */
    public function getDescription(): ?FormField {
        return $this->description;
    }

    private function setDescription(array $description): void {
        $this->description = new FormField($description);
    }

    /**
     * This method returns if the keepPurchasedItems FormField.
     *
     * @return FormField|Null
     */
    public function getKeepPurchasedItems(): ?FormField {
        return $this->keepPurchasedItems;
    }

    private function setKeepPurchasedItems(array $keepPurchasedItems): void {
        $this->keepPurchasedItems = new FormField($keepPurchasedItems);
    }

    /**
     * This method returns if the defaultOne FormField.
     *
     * @return FormField|Null
     */
    public function getDefaultOne(): ?FormField {
        return $this->defaultOne;
    }

    private function setDefaultOne(array $defaultOne): void {
        $this->defaultOne = new FormField($defaultOne);
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
