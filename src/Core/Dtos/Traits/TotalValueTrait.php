<?php declare(strict_types=1);

namespace FWK\Core\Dtos\Traits;

/**
 * This is the Related items trait.
 *
 * @package FWK\Core\Dtos\Traits
 */
trait TotalValueTrait {

    protected float $totalValue = 0.0;
    
    /**
     * Returns the totalValue
     *
     * @return float
     */
    public function getTotalValue(): float {
        return $this->totalValue;
    }

    /**
     * Sets the totalValue
     *
     * @param float 
     */
    public function setTotalValue(float $totalValue): void {
        $this->totalValue = $totalValue;
    }

    /**
     * Adds to the totalValue
     *
     * @param float 
     */
    public function addTotalValue(float $totalValue): void {
        $this->totalValue += $totalValue;
    }
    
}
