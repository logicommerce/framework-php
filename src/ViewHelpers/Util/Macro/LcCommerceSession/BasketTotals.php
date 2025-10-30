<?php

declare(strict_types=1);

namespace FWK\ViewHelpers\Util\Macro\LcCommerceSession;

use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Dtos\Basket\BasketTotal;

/**
 * This is the BasketTotals class, a DTO class for the utilViewHelper LcCommerceSession.
 *
 * @package FWK\ViewHelpers\Util\Macro\LcCommerceSession
 */
class BasketTotals {
    use ElementTrait;

    private float $subtotalRows = 0.0;

    private float $subtotalPaymentSystem = 0.0;

    private float $subtotalShippings = 0.0;

    private float $subtotal = 0.0;

    private float $totalRows = 0.0;

    private float $totalDiscounts = 0.0;

    private float $totalShipping = 0.0;

    private float $totalPayment = 0.0;

    private float $totalTaxes = 0.0;

    private float $total = 0.0;

    /**
     * Constructor method for Totals
     *
     * @param null|BasketTotal $totals
     */
    public function __construct(?BasketTotal $totals) {
        if (!is_null($totals)) {
            $this->subtotalRows = $totals->getSubtotalRows();
            $this->subtotalPaymentSystem = $totals->getSubtotalPaymentSystem();
            $this->subtotalShippings = $totals->getSubtotalShippings();
            $this->subtotal = $totals->getSubtotal();
            $this->totalRows = $totals->getTotalRows();
            $this->totalDiscounts = $totals->getTotalDiscounts();
            $this->totalShipping = $totals->getTotalShippings();
            $this->totalPayment = $totals->getTotalPaymentSystem();
            $this->totalTaxes = $totals->getTotalTaxes();
            $this->total = $totals->getTotal();
        }
    }
}
