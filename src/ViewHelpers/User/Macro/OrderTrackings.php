<?php

namespace FWK\ViewHelpers\User\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;

/**
 * This is the OrderTrackings class, a macro class for the userViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the user's order trackings.
 *
 * @see Orders::getViewParameters()
 *
 * @package FWK\ViewHelpers\User\Macro
 */

class OrderTrackings {

    public ?array $shipments = null;

    public string $itemClass = 'userOrderActionWrap';

    public bool $showContainer = true;

    /**
     * Constructor method for Orders.
     *
     * @see Orders
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
        if (is_null($this->shipments)) {
            throw new CommerceException("The value of [order] argument: '" . $this->order . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
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
            'shipments' => $this->shipments,
            'itemClass' => $this->itemClass,
            'showContainer' => $this->showContainer,
        ];
    }
}
