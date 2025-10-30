<?php

namespace FWK\Dtos\Documents\RichPrices;

use SDK\Core\Dtos\Traits\ElementTrait;

/**
 * This is the Shipping rich prices container class.
 *
 * @see Shipping::getPrice
 * @see Shipping::getPriceWithTaxes
 * @see Shipping::getPriceWithDiscounts
 * @see Shipping::getPriceWithDiscountsWithTaxes
 *
 * @see ElementTrait
 *
 * @package FWK\Dtos\Documents\RichPrices
 */
class Shipping{
    use ElementTrait;

    protected float $price = 0;
    protected float $priceWithTaxes = 0;
    protected float $priceWithDiscounts = 0;
    protected float $priceWithDiscountsWithTaxes = 0;

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
     * Returns the priceWithDiscounts value.
     *
     * @return float
     */
    public function getPriceWithDiscounts(): float {
        return $this->priceWithDiscounts;
    }

    /**
     * Returns the priceWithDiscountsWithTaxes value.
     *
     * @return float
     */
    public function getPriceWithDiscountsWithTaxes(): float {
        return $this->priceWithDiscountsWithTaxes;
    }

}
