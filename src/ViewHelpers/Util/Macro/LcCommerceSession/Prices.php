<?php

declare(strict_types=1);

namespace FWK\ViewHelpers\Util\Macro\LcCommerceSession;

use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Dtos\Basket\BasketRowPrices;

/**
 * This is the Prices class, a DTO class for the utilViewHelpers LcCommerce.
 *
 * @see LcCommerceData::getViewParameters()
 *
 * @package FWK\Core\ViewHelpers\Macros\Util\LcCommerce
 */
class Prices {
    use ElementTrait;

    private float $unitPrice = 0.0;

    private float $unitPreviousPrice = 0.0;

    private float $price = 0.0;

    private float $previousPrice = 0.0;

    private float $totalDiscounts = 0.0;

    /**
     * Constructor method for Prices
     * 
     * @param BasketRowPrices $prices
     */
    public function __construct(BasketRowPrices $prices) {
        $this->unitPrice = $prices->getUnitPrice();
        $this->unitPreviousPrice = $prices->getUnitPreviousPrice();
        $this->price = $prices->getPrice();
        $this->previousPrice = $prices->getPreviousPrice();
        $this->totalDiscounts = $prices->getTotalDiscounts();
    }
}
