<?php

namespace FWK\Dtos\Documents\RichPrices;

use SDK\Core\Dtos\Traits\ElementTrait;

/**
 * This is the Document Row rich prices container class.
 *
 * @see DocumentRow::getPreviousPrice
 * @see DocumentRow::getPreviousPriceWithTaxes
 * @see DocumentRow::getPrice
 * @see DocumentRow::getPriceWithTaxes
 * @see DocumentRow::getTotalTaxesValue
 * @see DocumentRow::getTotal
 * @see DocumentRow::getTotalWithTaxes
 *
 * @see ElementTrait
 *
 * @package FWK\Dtos\Documents\RichPrices
 */
abstract class DocumentRow{
    use ElementTrait;

    protected float $previousPrice = 0;
    protected float $previousPriceWithTaxes = 0;
    protected float $price = 0;
    protected float $priceWithTaxes = 0;
    protected float $totalTaxesValue = 0;
    protected float $total = 0;
    protected float $totalWithTaxes = 0;

    /**
     * Returns the previousPrice value.
     *
     * @return float
     */
    public function getPreviousPrice(): float {
        return $this->previousPrice;
    }

    /**
     * Returns the previousPriceWithTaxes value.
     *
     * @return float
     */
    public function getPreviousPriceWithTaxes(): float {
        return $this->previousPriceWithTaxes;
    }

    /**
     * Returns the price value.
     *
     * @return float
     */
    public function getPrice(): float {
        return $this->price;
    }

    /**
     * Returns the priceWithTaxes value.
     *
     * @return float
     */
    public function getPriceWithTaxes(): float {
        return $this->priceWithTaxes;
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
     * Returns the total value.
     *
     * @return float
     */
    public function getTotal(): float {
        return $this->total;
    }

    /**
     * Returns the totalWithTaxes value.
     *
     * @return float
     */
    public function getTotalWithTaxes(): float {
        return $this->totalWithTaxes;
    }

}
