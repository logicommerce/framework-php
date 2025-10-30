<?php

namespace FWK\ViewHelpers\User\Macro;

use FWK\Core\Exceptions\CommerceException;
use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Dtos\User\ShoppingListRow;

/**
 * This is the ShoppingListRowEditButton class, a macro class for the userViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the user's login form.
 *
 * @see ShoppingListRowEditButton::getViewParameters()
 *
 * @package FWK\ViewHelpers\User\Macro
 */
class ShoppingListRowEditButton {

    public ?ShoppingListRow $row = null;

    public string $class = '';

    public int $totalItems = 0;

    /**
     * Constructor method for ShoppingListRowEditButton
     * 
     * @see ShoppingListRowEditButton
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
            'totalItems' => $this->totalItems,
            'class' => $this->class
        ];
    }
}
