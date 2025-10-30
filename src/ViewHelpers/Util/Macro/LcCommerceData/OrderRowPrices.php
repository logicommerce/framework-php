<?php

declare(strict_types=1);

namespace FWK\ViewHelpers\Util\Macro\LcCommerceData;

use SDK\Core\Dtos\Traits\ElementTrait;
use FWK\Core\ViewHelpers\ViewHelper;

/**
 * This is the OrderRowPrices class, a DTO class for the utilViewHelpers LcCommerce.
 *
 * @see LcCommerceData::getViewParameters()
 *
 * @package FWK\ViewHelpers\Util\Macro\LcCommerceData;
 */
class OrderRowPrices {
    use ElementTrait;

    protected float $previousPrice = 0.0;

    protected float $previousPriceWithTaxes = 0.0;

    protected float $price = 0.0;

    protected float $priceWithTaxes = 0.0;

    protected float $totalTaxesValue = 0.0;

    protected float $total = 0.0;

    protected float $totalWithTaxes = 0.0;

    /**
     * Constructor method for Prices
     * 
     * @param $prices
     */
    public function __construct($prices) {
        $this->previousPrice = $prices->getPreviousPrice();
        $this->previousPriceWithTaxes = $prices->getPreviousPriceWithTaxes();
        $this->price = $prices->getPrice();
        $this->priceWithTaxes = $prices->getPriceWithTaxes();
        $this->totalTaxesValue = $prices->getTotalTaxesValue();
        $this->total = $prices->getTotal();
        $this->totalWithTaxes = $prices->getTotalWithTaxes();
    }
}
