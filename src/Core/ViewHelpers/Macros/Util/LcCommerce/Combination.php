<?php

declare(strict_types=1);

namespace FWK\Core\ViewHelpers\Macros\Util\LcCommerce;

use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Dtos\Catalog\Product\Combination as CombinationDto;

/**
 * This is the Combination class, a DTO class for the utilViewHelpers LcCommerce.
 *
 * @see LcCommerceData::getViewParameters()
 *
 * @package FWK\Core\ViewHelpers\Macros\Util\LcCommerce
 */
class Combination {
    use ElementTrait;

    private int $id = 0;

    private ?Codes $codes = null;

    /**
     * Constructor method for Combination
     * 
     * @param CombinationDto $combination
     */
    public function __construct(CombinationDto $combination) {
        $this->id = $combination->getId();
        $this->codes = new Codes($combination->getCodes());
    }
}
