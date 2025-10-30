<?php

declare(strict_types=1);

namespace FWK\ViewHelpers\Util\Macro\LcCommerceData;

use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Dtos\Catalog\Product\Options\Option as OptionDto;

/**
 * This is the Option class, a DTO class for the utilViewHelpers LcCommerce.
 *
 * @see LcCommerceData::getViewParameters()
 *
 * @package FWK\ViewHelpers\Util\Macro\LcCommerceData;
 */
class Option {
    use ElementTrait;

    private int $id = 0;

    private string $name = '';

    private string $type = '';

    private string $typology = '';

    private array $values = [];

    /**
     * Constructor method for Option
     * 
     * @param OptionDto $option
     */
    public function __construct(OptionDto $option) {
        $this->id = $option->getId();
        $this->name = $option->getLanguage()->getName();
        $this->type = $option->getType();
        $this->typology = $option->getTypology();
        foreach ($option->getValues() as $value) {
            $this->values[] = new OptionValue($value);
        }
    }
}
