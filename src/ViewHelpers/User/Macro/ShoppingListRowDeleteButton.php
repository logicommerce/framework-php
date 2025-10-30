<?php

namespace FWK\ViewHelpers\User\Macro;

use FWK\Core\Exceptions\CommerceException;
use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Dtos\User\ShoppingListRow;

/**
 * This is the ShoppingListRowDeleteButton class, a macro class for the userViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the user's login form.
 *
 * @see ShoppingListRowDeleteButton::getViewParameters()
 *
 * @package FWK\ViewHelpers\User\Macro
 */
class ShoppingListRowDeleteButton {

    public ?ShoppingListRow $row = null;

    public string $class = '';

    public string $containerId = '';

    public int $shoppingListId = 0;

    /**
     * Constructor method for ShoppingListRowDeleteButton
     * 
     * @see ShoppingListRowDeleteButton
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
        if (strlen($this->containerId) === 0) {
            throw new CommerceException("The value argument 'containerId' is required in " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }
        if ($this->shoppingListId === 0) {
            throw new CommerceException("The value argument 'shoppingListId' is required in " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
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
            'class' => $this->class,
            'shoppingListId' => $this->shoppingListId,
            'containerId' => $this->containerId
        ];
    }
}
