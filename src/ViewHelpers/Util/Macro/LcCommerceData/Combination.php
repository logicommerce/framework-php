<?php

declare(strict_types=1);

namespace FWK\ViewHelpers\Util\Macro\LcCommerceData;

use FWK\Core\ViewHelpers\Macros\Util\LcCommerce\Codes;
use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Dtos\Catalog\Product\Combination as CombinationDto;
use SDK\Dtos\Catalog\ProductCombinationData;

/**
 * This is the Combination class, a DTO class for the utilViewHelpers LcCommerce.
 *
 * @see LcCommerceData::getViewParameters()
 *
 * @package FWK\ViewHelpers\Util\Macro\LcCommerceData;
 */
class Combination {
    use ElementTrait;

    private int $id = 0;

    private bool $selected = false;

    private ?Codes $codes = null;

    /**
     * Constructor method for Combination
     * 
     * @param CombinationDto $combination
     * @param ProductCombinationData $combinationData
     */
    public function __construct(CombinationDto $combination, ?ProductCombinationData $combinationData = null) {
        $this->id = $combination->getId();
        $this->codes = new Codes($combination->getCodes());
        if (!is_null($combinationData)) {
            $this->selected = $combination->getId() === $combinationData->getStock()->getCombinationId();
        }
    }
}
