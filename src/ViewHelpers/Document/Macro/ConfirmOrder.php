<?php

namespace FWK\ViewHelpers\Document\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use SDK\Dtos\Documents\Document;
use SDK\Enums\PaymentType;

/**
 * This is the ConfirmOrder class, a macro class for the order viewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the order's ConfirmOrder.
 *
 * @see ConfirmOrder::getViewParameters()
 *
 * @package FWK\ViewHelpers\Document\Macro
 */
class ConfirmOrder {

    public ?Document $order = null;

    public array $confirmOrderPlugins = [];

    public bool $showTransactionId = true;

    public bool $showAuthNumber = true;

    /**
     * Constructor method for ConfirmOrder class.
     *
     * @see ConfirmOrder
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
        if (is_null($this->order)) {
            throw new CommerceException("The value of [order] argument: '" . $this->order . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }

        if( 
            $this->showTransactionId && 
            (
                $this->order->getTotals()->getTotal() <= 0 || 
                (
                    $this->order->getPaymentSystem()->getPaymentType() === PaymentType::OFFLINE || 
                    $this->order->getPaymentSystem()->getPaymentType() === PaymentType::CASH_ON_DELIVERY 
                )
            )
        ){
            $this->showTransactionId = false;
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
            'order' => $this->order,
            'confirmOrderPlugins' => $this->confirmOrderPlugins,
            'showTransactionId' => $this->showTransactionId,
            'showAuthNumber' => $this->showAuthNumber
        ];
    }
}