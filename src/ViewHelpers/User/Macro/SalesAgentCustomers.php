<?php

namespace FWK\ViewHelpers\User\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use SDK\Core\Dtos\ElementCollection;

/**
 * This is the SalesAgentCustomers class, a macro class for the userViewHelper.
 * The purpose of this class is to encapsulate the logic to show customers from a sales agent.
 *
 * @see Orders::getViewParameters()
 *
 * @package FWK\ViewHelpers\User\Macro
 * @deprecated
 */

class SalesAgentCustomers {

    public const PID = 'pId';
    public const COMPANY = 'company';
    public const NIF = 'nif';
    public const ADDRESS = 'address';
    public const CITY = 'city';
    public const STATE = 'state';
    public const COUNTRY = 'country';
    public const PHONE = 'phone';
    public const EMAIL = 'email';
    public const NAME = 'name';
    public const SALES_AGENT = 'salesAgent';
    public const TOTAL_AMOUNT = 'totalAmount';
    public const COMMISSION_AMOUNT = 'commissionAmount';
    public const PENDING_AMOUNT = 'pendingAmount';
    public const ACTIONS = 'actionOrders';

    public const SALES_AGENT_PARAMETERS = [
        self::PID,
        self::COMPANY,
        self::NIF,
        self::ADDRESS,
        self::CITY,
        self::STATE,
        self::COUNTRY,
        self::PHONE,
        self::EMAIL,
        self::NAME,
        self::SALES_AGENT,
        self::TOTAL_AMOUNT,
        self::COMMISSION_AMOUNT,
        self::PENDING_AMOUNT,
        self::ACTIONS
    ];

    public const ACTION_SALES_AGENT_SALES = 'salesAgentSales';

    public const ACTION_LOGIN_SIMULATION = 'loginSimulation';

    public array $availableActions = [self::ACTION_SALES_AGENT_SALES, self::ACTION_LOGIN_SIMULATION];

    public ?ElementCollection $salesAgentCustomers = null;

    public array $parameters = [];

    public array $request = [];

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
            'salesAgentCustomers' => $this->salesAgentCustomers,
            'parameters' => $this->parameters,
            'request' => $this->request,
            'availableActions' => $this->availableActions
        ];
    }
}
