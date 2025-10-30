<?php

namespace FWK\Dtos\Documents\RichPrices;

use SDK\Core\Dtos\Traits\ElementTrait;

/**
 * This is the Discount rich prices container class.
 *
 * @see Discount::getValue
 * @see Discount::getValueWithTaxes
 *
 * @see ElementTrait
 *
 * @package FWK\Dtos\Documents\RichPrices
 */
class Discount{
    use ElementTrait;

    protected float $value = 0;
    protected float $valueWithTaxes = 0;

    /**
     * Returns the value value.
     *
     * @return float
     */
    public function getValue(): float {
        return $this->value;
    }

    /**
     * Returns the valueWithTaxes value.
     *
     * @return float
     */
    public function getValueWithTaxes(): float {
        return $this->valueWithTaxes;
    }

}
