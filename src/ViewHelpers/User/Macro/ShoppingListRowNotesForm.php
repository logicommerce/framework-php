<?php

namespace FWK\ViewHelpers\User\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Form\Form;
use FWK\Core\Form\FormFactory;
use SDK\Dtos\User\ShoppingListRow;

/**
 * This is the ShoppingListRowNotesForm class, a macro class for the userViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the user's login form.
 *
 * @see ShoppingListRowNotesForm::getViewParameters()
 *
 * @package FWK\ViewHelpers\User\Macro
 */
class ShoppingListRowNotesForm {

    public ?Form $form = null;

    public int $shoppingListId = 0;

    public ?ShoppingListRow $shoppingListRow = null;

    /**
     * Constructor method for ShoppingListRowNotesForm
     * 
     * @see ShoppingListRowNotesForm
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
            FormFactory::getShoppingListRowNotes($this->shoppingListRow);
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
