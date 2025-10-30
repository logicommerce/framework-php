<?php

namespace FWK\ViewHelpers\User\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Form\Form;
use FWK\Core\Form\FormFactory;
use FWK\Core\Theme\Dtos\FormFieldsShoppingList;
use SDK\Core\Enums\MethodType;
use SDK\Dtos\User\ShoppingList;

/**
 * This is the ShoppingListForm class, a macro class for the userViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the user's login form.
 *
 * @see ShoppingListForm::getViewParameters()
 *
 * @package FWK\ViewHelpers\User\Macro
 */
class ShoppingListForm {

    public ?Form $form = null;

    public int $shoppingListId = 0;

    public ?ShoppingList $shoppingList = null;

    public ?FormFieldsShoppingList $shoppingListFields = null;

    /**
     * Constructor method for ShoppingListForm
     * 
     * @see ShoppingListForm
     * 
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        ViewHelper::mergeArguments($this, $arguments);
    }

    /**
     * This method returns all calculated arguments and new parameters for UserViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        if (is_null($this->form)) {
            $this->form = FormFactory::getShoppingList(MethodType::POST, null);
        }
        return $this->getProperties();
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        return [
            'form' => $this->form
        ];
    }
}
