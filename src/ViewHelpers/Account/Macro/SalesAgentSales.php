<?php

namespace FWK\ViewHelpers\Account\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use SDK\Core\Dtos\ElementCollection;

/**
 * This is the SalesAgentSales class, a macro class for the accountViewHelper.
 * The purpose of this class is to encapsulate the logic to show sales from a sales agent.
 *
 * @see Orders::getViewParameters()
 * 
 * @package FWK\ViewHelpers\Account\Macro
 */

class SalesAgentSales {

    public const PID = 'pId';
    public const CLIENT = 'client';
    public const DATE = 'date';
    public const STATUS = 'status';
    public const TOTAL = 'total';
    public const COMMISSION = 'commission';
    public const PAID = 'paid';
    public const VIEW_ORDER = 'viewOrder';

    public const SALES_AGENT_PARAMETERS = [
        self::PID,
        self::CLIENT,
        self::DATE,
        self::STATUS,
        self::TOTAL,
        self::COMMISSION,
        self::PAID,
        self::VIEW_ORDER,
    ];

    public ?ElementCollection $salesAgentSales = null;

    public array $parameters = [];

    /**
     * Constructor method for SalesAgentCustomers.
     *
     * @see SalesAgentCustomers
     *
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        ViewHelper::mergeArguments($this, $arguments);
    }

    /**
     * This method returns all calculated arguments and new parameters for AccountViewHelper.php
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
            'salesAgentSales' => $this->salesAgentSales,
            'parameters' => $this->parameters,
        ];
    }
}
