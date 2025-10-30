<?php

namespace FWK\ViewHelpers\User\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\ViewHelpers\Macros\Basket\Macro\BaseOutput;
use SDK\Dtos\Documents\Transactions\Purchases\Order;

/**
 * This is the OrderShipments class, a macro class for the userViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the user's order shipments.
 *
 * @see Orders::getViewParameters()
 *
 * @package FWK\ViewHelpers\User\Macro
 */

class OrderShipments extends BaseOutput {

    public ?Order $order = null;

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
        if (is_null($this->order)) {
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
        return parent::getProperties() + [
            'order' => $this->order,
        ];
    }

    protected function setFooter(): void {
    }

    protected function setTotalProductDiscounts(): void {
    }

    protected function getRowClassName(&$documentRow): string {
        return '';
    }

    protected function setDisclosure(): void {
    }
}
