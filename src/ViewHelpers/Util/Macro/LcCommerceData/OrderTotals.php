<?php

declare(strict_types=1);

namespace FWK\ViewHelpers\Util\Macro\LcCommerceData;

use FWK\Dtos\Documents\DocumentTotal;
use SDK\Core\Dtos\Traits\ElementTrait;

/**
 * This is the OrderTotals class, a DTO class for the utilViewHelper LcCommerceData.
 *
 * @package FWK\ViewHelpers\Util\Macro\LcCommerceData
 */
class OrderTotals {
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
     * @param null|DocumentTotal $totals
     */
    public function __construct(?DocumentTotal $totals) {
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
