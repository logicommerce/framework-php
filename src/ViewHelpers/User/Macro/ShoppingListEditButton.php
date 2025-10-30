<?php

namespace FWK\ViewHelpers\User\Macro;

use FWK\Core\Exceptions\CommerceException;
use FWK\Core\ViewHelpers\ViewHelper;
use SDK\Dtos\User\ShoppingList;

/**
 * This is the ShoppingListEditButton class, a macro class for the userViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the user's login form.
 *
 * @see ShoppingListEditButton::getViewParameters()
 *
 * @package FWK\ViewHelpers\User\Macro
 */
class ShoppingListEditButton {

    public ?ShoppingList $shoppingList = null;

    public string $class = '';

    public int $totalItems = 0;

    /**
     * Constructor method for ShoppingListEditButton
     * 
     * @see ShoppingListEditButton
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
        if (is_null($this->shoppingList)) {
            throw new CommerceException("The value argument 'shoppingList' is required in " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
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
            'shoppingList' => $this->shoppingList,
            'totalItems' => $this->totalItems,
            'class' => $this->class
        ];
    }
}
