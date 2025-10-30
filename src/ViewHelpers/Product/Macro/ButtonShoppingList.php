<?php

namespace FWK\ViewHelpers\Product\Macro;

use FWK\Core\Form\Form;
use FWK\Core\Form\FormFactory;
use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Services\LmsService;
use SDK\Core\Dtos\Element;
use SDK\Core\Enums\MethodType;
use SDK\Dtos\Catalog\BundleGrouping;
use SDK\Dtos\Catalog\Product\Product;
use SDK\Enums\ElementType;

/**
 * This is the ButtonShoppingList class, a macro class for the productViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the ShoppingListRow's button.
 *
 * @see ButtonShoppingList::getViewParameters()
 *
 * @package FWK\ViewHelpers\Product\Macro
 */
class ButtonShoppingList {

    public bool $showLabel = true;

    public bool $allowDelete = true;

    public string $class = '';

    public string $itemType = '';

    public ?Element $item = null;

    public bool $showDefaultShoppingListButton = true;

    public bool $showShoppingLists = false;

    public bool $allowAddShoppingList = false;

    private ?Form $shoppingListForm = null;

    /**
     * Constructor method for ButtonShoppingList class.
     * 
     * @see ButtonShoppingList
     * 
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        ViewHelper::mergeArguments($this, $arguments);
    }

    /**
     * This method returns all calculated arguments and new parameters for ProductViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        if (!$this->showShoppingLists || !LmsService::getShoppingListLicense()) {
            $this->showShoppingLists = $this->allowAddShoppingList = false;
        }
        if (is_null($this->shoppingListForm)) {
            $this->shoppingListForm = FormFactory::getShoppingList(MethodType::POST, null);
        }
        if ($this->item instanceof Product) {
            $this->itemType = ElementType::PRODUCT;
        } else if ($this->item instanceof BundleGrouping) {
            $this->itemType = ElementType::BUNDLE;
        }
        return $this->getProperties();
    }

    protected function getProperties(): array {
        return [
            'item' => $this->item,
            'itemType' => $this->itemType,
            'showLabel' => $this->showLabel,
            'allowDelete' => $this->allowDelete,
            'class' => $this->class,
            'showDefaultShoppingListButton' => $this->showDefaultShoppingListButton,
            'showShoppingLists' => $this->showShoppingLists,
            'allowAddShoppingList' => $this->allowAddShoppingList,
            'shoppingListForm' => $this->shoppingListForm
        ];
    }
}
