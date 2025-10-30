<?php

namespace FWK\ViewHelpers\User\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use SDK\Core\Dtos\ElementCollection;

/**
 * This is the CustomerOrders class, a macro class for the userViewHelper.
 * The purpose of this class is to encapsulate the logic to show customer orders.
 *
 * @see Orders::getViewParameters()
 * 
 * @package FWK\ViewHelpers\User\Macro
 */

class CustomerOrders {

    public ?ElementCollection $orders = null;

    /**
     * Constructor method for ReturnRequestForm.
     *
     * @see ReturnRequestForm
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
        return $this->getProperties();
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        return [
            'orders' => $this->orders,
        ];
    }

}