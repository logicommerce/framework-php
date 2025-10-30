<?php

namespace FWK\ViewHelpers\Document\Macro;

use FWK\Core\ViewHelpers\ViewHelper;

/**
 * This is the Buttons class, a macro class for the order viewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the order's buttons.
 *
 * @see Buttons::getViewParameters()
 *
 * @package FWK\ViewHelpers\Document\Macro
 */
class Buttons {

    public int $orderId = 0;

    public bool $showBackButton = true;

    public bool $showPrintButton = true;

    public string $classList = '';

    public string $token = '';

    /**
     * Constructor method for Buttons class.
     *
     * @see Buttons
     *
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        ViewHelper::mergeArguments($this, $arguments);
    }

    /**
     * This method returns all calculated arguments and new parameters for OrderViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        return $this->getProperties();
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        return [
            'orderId' => $this->orderId,
            'showBackButton' => $this->showBackButton,
            'showPrintButton' => $this->showPrintButton,
            'classList' => $this->classList,
            'token' => $this->token
        ];
    }
}