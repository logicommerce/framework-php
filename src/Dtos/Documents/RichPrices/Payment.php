<?php

namespace FWK\Dtos\Documents\RichPrices;

use SDK\Core\Dtos\Traits\ElementTrait;

/**
 * This is the Payment rich prices container class.
 *
 * @see Payment::getPrice
 * @see Payment::getPriceWithTaxes
 *
 * @see ElementTrait
 *
 * @package FWK\Dtos\Documents\RichPrices
 */
class Payment{
    use ElementTrait;

    protected float $price = 0;
    protected float $priceWithTaxes = 0;

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

}
