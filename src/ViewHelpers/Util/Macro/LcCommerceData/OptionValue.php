<?php

declare(strict_types=1);

namespace FWK\ViewHelpers\Util\Macro\LcCommerceData;

use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Dtos\Catalog\Product\Options\OptionValue as OptionValueDto;

/**
 * This is the OptionValue class, a DTO class for the utilViewHelpers LcCommerce.
 *
 * @see LcCommerceData::getViewParameters()
 *
 * @package FWK\ViewHelpers\Util\Macro\LcCommerceData;
 */
class OptionValue {
    use ElementTrait;

    private int $id = 0;

    private string $name = '';

    /**
     * Constructor method for OptionValue
     * 
     * @param OptionValueDto $option
     */
    public function __construct(OptionValueDto $option) {
        $this->id = $option->getId();
        $this->name = $option->getLanguage()->getValue();
    }
}
