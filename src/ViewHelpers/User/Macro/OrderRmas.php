<?php

namespace FWK\ViewHelpers\User\Macro;

use FWK\Core\Exceptions\CommerceException;
use FWK\Core\ViewHelpers\ViewHelper;

/**
 * This is the OrderRmas class, a macro class for the userViewHelper.
 * The purpose of this class is to encapsulate the logic to make a product return.
 *
 * @see Orders::getViewParameters()
 * 
 * @package FWK\ViewHelpers\User\Macro
 */

class OrderRmas {

    public const ACTION_RMA = 'rma';

    public const ACTION_RMA_PDF = 'rmaPdf';

    private const ACTIONS = [
        self::ACTION_RMA,
        self::ACTION_RMA_PDF,
    ];

    public array $rmas = [];

    public int $userId = 0;

    public int $orderId = 0;

    public array $showRmasActions = [];

    public array $showRmasIcons = [];

    public const POPUP = 'popup';

    public const WINDOW = 'window';
    
    public string $documentView = self::POPUP;

    /**
     * Constructor method for OrderRmas.
     *
     * @see OrderRmas
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
        if(is_null($this->rmas)) {
            throw new CommerceException("The value of [rmas] argument: '" . $this->rmas . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }
        
        if($this->userId === 0) {
            throw new CommerceException("The value of [userId] argument: '" . $this->userId . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }

        foreach ($this->showRmasActions as $action) {
            if (!in_array($action, self::ACTIONS, true)) {
                throw new CommerceException("The value of [showRmasActions] argument: '" . $action . "' not exists in " . self::class, CommerceException::VIEW_HELPER_INVALID_ARGUMENT);
            }
        }
        
        foreach ($this->showRmasIcons as $action) {
            if (!in_array($action, self::ACTIONS, true)) {
                throw new CommerceException("The value of [showRmasIcons] argument: '" . $action . "' not exists in " . self::class, CommerceException::VIEW_HELPER_INVALID_ARGUMENT);
            }
        }

        return $this->getProperties();
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    private function getProperties(): array {
        return [
            'rmas' => $this->rmas,
            'userId' => $this->userId,
            'showRmasActions' => $this->showRmasActions,
            'showRmasIcons' => $this->showRmasIcons,
            'orderId' => $this->orderId,
        ];
    }
}