<?php

declare(strict_types=1);

namespace FWK\ViewHelpers\Util\Macro\LcCommerceData;

use SDK\Core\Dtos\Traits\ElementTrait;

/**
 * This is the OrderProductPrices class, a DTO class for the utilViewHelpers LcCommerce.
 *
 * @see LcCommerceData::getViewParameters()
 *
 * @package FWK\ViewHelpers\Util\Macro\LcCommerceData;
 */
class OrderRowProductPrices extends OrderRowPrices {
    use ElementTrait;

    private float $productPrice = 0.0;

    private float $productPriceWithTaxes = 0.0;

    private float $optionsPrice = 0.0;

    private float $optionsPriceWithTaxes = 0.0;

    private float $totalDiscountsValue = 0.0;

    private float $totalDiscountsValueWithTaxes = 0.0;

    private float $totalWithDiscounts = 0.0;

    private float $totalWithDiscountsWithTaxes = 0.0;


    /**
     * Constructor method for Prices
     * 
     * @param $prices
     */
    public function __construct($prices) {
        parent::__construct($prices);
        $this->productPrice = $prices->getProductPrice();
        $this->productPriceWithTaxes = $prices->getProductPriceWithTaxes();
        $this->optionsPrice = $prices->getOptionsPrice();
        $this->optionsPriceWithTaxes = $prices->getOptionsPriceWithTaxes();
        $this->totalDiscountsValue = $prices->getTotalDiscountsValue();
        $this->totalDiscountsValueWithTaxes = $prices->getTotalDiscountsValueWithTaxes();
        $this->totalWithDiscounts = $prices->getTotalWithDiscounts();
        $this->totalWithDiscountsWithTaxes = $prices->getTotalWithDiscountsWithTaxes();
    }
}
