<?php

namespace FWK\Dtos\Documents\RichPrices;

use SDK\Core\Dtos\Element;
use SDK\Core\Dtos\Traits\ElementTrait;

/**
 * This is the Totals rich prices container class.
 *
 * @see Totals::getTotalRows
 * @see Totals::getTotalRowsWithTaxes
 * @see Totals::getTotalShippingsWithDiscounts
 * @see Totals::getTotalShippingsWithDiscountsWithTaxes
 * @see Totals::getTotalPaymentSystem
 * @see Totals::getTotalPaymentSystemWithTaxes
 * @see Totals::getTotal
 * @see Totals::getTotalWithDiscounts
 * @see Totals::getTotalTaxesValue
 * @see Totals::getTotalWithDiscountsWithTaxes
 * @see Totals::getTotalRowsDiscountsValue
 * @see Totals::getTotalBasketDiscountsValue
 * @see Totals::getTotalShippingDiscountsValue
 * @see Totals::getTotalVouchers
 *
 * @see ElementTrait
 *
 * @package FWK\Dtos\Documents\RichPrices
 */
class Totals{
    use ElementTrait;

    protected float $totalRows = 0;
    protected float $totalRowsWithTaxes  = 0;
    protected float $totalShippingsWithDiscounts  = 0;
    protected float $totalShippingsWithDiscountsWithTaxes  = 0;
    protected float $totalPaymentSystem  = 0;
    protected float $totalPaymentSystemWithTaxes  = 0;
    protected float $total  = 0;
    protected float $totalWithDiscounts = 0;
    protected float $totalTaxesValue  = 0;
    protected float $totalWithDiscountsWithTaxes = 0;
    protected float $totalRowsDiscountsValue  = 0;
    protected float $totalBasketDiscountsValue = 0;
    protected float $totalShippingDiscountsValue = 0;
    protected float $totalVouchers = 0;

    /**
     * Returns the totalRows value.
     *
     * @return float
     */
    public function getTotalRows(): float {
        return $this->totalRows;
    }

    /**
     * Returns the totalRowsWithTaxes value.
     *
     * @return float
     */
    public function getTotalRowsWithTaxes(): float {
        return $this->totalRowsWithTaxes;
    }

    /**
     * Returns the totalShippingsWithDiscounts value.
     *
     * @return float
     */
    public function getTotalShippingsWithDiscounts(): float {
        return $this->totalShippingsWithDiscounts;
    }

    /**
     * Returns the totalShippingsWithDiscountsWithTaxes value.
     *
     * @return float
     */
    public function getTotalShippingsWithDiscountsWithTaxes(): float {
        return $this->totalShippingsWithDiscountsWithTaxes;
    }

    /**
     * Returns the totalPaymentSystem value.
     *
     * @return float
     */
    public function getTotalPaymentSystem(): float {
        return $this->totalPaymentSystem;
    }

    /**
     * Returns the totalPaymentSystemWithTaxes value.
     *
     * @return float
     */
    public function getTotalPaymentSystemWithTaxes(): float {
        return $this->totalPaymentSystemWithTaxes;
    }

    /**
     * Returns the total value.
     *
     * @return float
     */
    public function getTotal(): float {
        return $this->total;
    }

    /**
     * Returns the totalWithDiscounts value.
     *
     * @return float
     */
    public function getTotalWithDiscounts(): float {
        return $this->totalWithDiscounts;
    }

    /**
     * Returns the totalTaxesValue value.
     *
     * @return float
     */
    public function getTotalTaxesValue(): float {
        return $this->totalTaxesValue;
    }

    /**
     * Returns the totalWithDiscountsWithTaxes value.
     *
     * @return float
     */
    public function getTotalWithDiscountsWithTaxes(): float {
        return $this->totalWithDiscountsWithTaxes;
    }

    /**
     * Returns the totalRowsDiscountsValue value.
     *
     * @return float
     */
    public function getTotalRowsDiscountsValue(): float {
        return $this->totalRowsDiscountsValue;
    }

    /**
     * Returns the totalBasketDiscountsValue value.
     *
     * @return float
     */
    public function getTotalBasketDiscountsValue(): float {
        return $this->totalBasketDiscountsValue;
    }

    /**
     * Returns the totalShippingDiscountsValue value.
     *
     * @return float
     */
    public function getTotalShippingDiscountsValue(): float {
        return $this->totalShippingDiscountsValue;
    }

    /**
     * Returns the totalVouchers value.
     *
     * @return float
     */
    public function getTotalVouchers(): float {
        return $this->totalVouchers;
    }
}
