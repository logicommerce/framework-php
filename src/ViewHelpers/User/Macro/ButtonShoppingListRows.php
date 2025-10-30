<?php

namespace FWK\ViewHelpers\User\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use FWK\Dtos\User\ShoppingListRow;
use SDK\Core\Dtos\ElementCollection;

/**
 * This is the ButtonShoppingListRows class, a macro class for the userViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the user's shoppingList button.
 *
 * @see ButtonShoppingListRows::getViewParameters()
 *
 * @package FWK\ViewHelpers\User\Macro
 */
class ButtonShoppingListRows {

    /**
     * This type show open modal button (delete shoppingList form modal)
     */
    public const TYPE_DELETE = 'delete';

    /**
     * This type show open modal button (send shoppingList form modal)
     */
    public const TYPE_SEND = 'send';

    private const TYPES = [
        self::TYPE_DELETE,
        self::TYPE_SEND
    ];

    public string $type = '';

    public string $class = '';

    public ?ElementCollection $shoppingListRows = null;

    /**
     * Constructor method for ButtonShoppingListRows class.
     * 
     * @see ButtonShoppingListRows
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
        if (!in_array($this->type, self::TYPES, true)) {
            throw new CommerceException("The value of [type] argument: '" . $this->type . "' not exists in " . self::class, CommerceException::VIEW_HELPER_UNDEFINED_ARGUMENT);
        }
        return $this->getProperties();
    }

    protected function getProperties(): array {
        return [
            'type' => $this->type,
            'class' => $this->class,
            'shoppingListRows' => $this->shoppingListRows
        ];
    }
}
