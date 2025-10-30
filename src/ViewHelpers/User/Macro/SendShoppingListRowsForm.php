<?php

namespace FWK\ViewHelpers\User\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Form\Form;
use SDK\Core\Dtos\ElementCollection;
use SDK\Dtos\User\ShoppingListRow;

/**
 * This is the SendShoppingListRowsForm class, a macro class for the userViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the user's shopping list.
 *
 * @see SendShoppingListRowsForm::getViewParameters()
 *
 * @package FWK\ViewHelpers\User\Macro
 */
class SendShoppingListRowsForm {

    public ?Form $form = null;

    public ?ElementCollection $shoppingListRows = null;

    /**
     * Constructor method for SendShoppingListRowsForm.
     * 
     * @see SendShoppingListRowsForm
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
        if (!is_null($this->shoppingListRows)) {
            foreach ($this->shoppingListRows as $shoppingListRow) {
                if (!$shoppingListRow instanceof ShoppingListRow) {
                    throw new CommerceException('The value of each shoppingListRows argument must be a instance of ' . ShoppingListRow::class . '. ' . ' Instance of ' . get_class($shoppingListRow) . ' sended.', CommerceException::VIEW_HELPER_INVALID_ARGUMENT);
                }
            }
        }

        if (is_null($this->form)) {
            throw new CommerceException("The value of [form] argument: '" . $this->form . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
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
            'form' => $this->form,
            'shoppingListRows' => $this->shoppingListRows
        ];
    }
}
