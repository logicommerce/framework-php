<?php

declare(strict_types=1);

namespace FWK\ViewHelpers\Util\Macro\LcCommerceData;

use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Core\Dtos\Catalog\ProductCombinationDataPrice;

/**
 * This is the Prices class, a DTO class for the utilViewHelpers LcCommerce.
 *
 * @see LcCommerceData::getViewParameters()
 *
 * @package FWK\ViewHelpers\Util\Macro\LcCommerceData;
 */
class Prices {
    use ElementTrait;

    private float $price = 0.0;

    private float $retailPrice = 0.0;

    /**
     * Constructor method for Prices
     * 
     * @param ProductCombinationDataPrice $prices
     */
    public function __construct(ProductCombinationDataPrice $prices) {
        $this->price = $prices->getPrices()->getBasePrice();
        $this->retailPrice = $prices->getPrices()->getRetailPrice();
    }
}
