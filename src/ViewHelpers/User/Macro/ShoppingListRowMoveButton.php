<?php

namespace FWK\ViewHelpers\User\Macro;

use FWK\Core\Exceptions\CommerceException;
use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Dtos\User\ShoppingListRow;

use SDK\Dtos\User\ShoppingList;

/**
 * This is the ShoppingListRowMoveButton class, a macro class for the userViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the user's login form.
 *
 * @see ShoppingListRowMoveButton::getViewParameters()
 *
 * @package FWK\ViewHelpers\User\Macro
 */
class ShoppingListRowMoveButton {

    public ?ShoppingListRow $row = null;

    public array $shoppingLists = [];

    public string $class = '';

    public string $containerId = '';

    /**
     * Constructor method for ShoppingListRowMoveButton
     * 
     * @see ShoppingListRowMoveButton
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
        if (is_null($this->row)) {
            throw new CommerceException("The value argument 'row' is required in " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }
        foreach ($this->shoppingLists as $shoppingList) {
            if (!$shoppingList instanceof ShoppingList) {
                throw new CommerceException('Each element of shoppingLists must be a instance of ' . ShoppingList::class . '. ' . ' Instance of ' . get_class($shoppingList) . ' sended.', CommerceException::VIEW_HELPER_INVALID_ARGUMENT);
            }
        }
        if (strlen($this->containerId) === 0) {
            throw new CommerceException("The value argument 'containerId' is required in " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
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
            'row' => $this->row,
            'shoppingLists' => $this->shoppingLists,
            'class' => $this->class,
            'containerId' => $this->containerId
        ];
    }
}
