<?php

namespace FWK\ViewHelpers\User\Macro;

use FWK\Core\Exceptions\CommerceException;
use FWK\Core\ViewHelpers\ViewHelper;
use SDK\Dtos\User\ShoppingList;

/**
 * This is the ShoppingListAddNoteButton class, a macro class for the userViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the user's login form.
 *
 * @see ShoppingListAddNoteButton::getViewParameters()
 *
 * @package FWK\ViewHelpers\User\Macro
 */
class ShoppingListAddNoteButton {

    public ?ShoppingList $shoppingList = null;

    public string $class = '';

    public string $rowTemplate = '';

    public string $containerId = '';

    public ?int $totalItems = null;

    /**
     * Constructor method for ShoppingListAddNoteButton
     * 
     * @see ShoppingListAddNoteButton
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
        if (is_null($this->totalItems)) {
            $this->totalItems = 0;
        }
        if (is_null($this->shoppingList)) {
            throw new CommerceException("The value argument 'shoppingList' is required in " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }
        if (strlen($this->containerId) === 0) {
            throw new CommerceException("The value argument 'containerId' is required in " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }
        if (strlen($this->rowTemplate) === 0) {
            throw new CommerceException("The value argument 'rowTemplate' is required in " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
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
            'class' => $this->class,
            'rowTemplate' => $this->rowTemplate,
            'containerId' => $this->containerId
        ];
    }
}
