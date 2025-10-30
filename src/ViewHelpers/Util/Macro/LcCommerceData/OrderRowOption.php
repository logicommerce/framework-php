<?php

declare(strict_types=1);

namespace FWK\ViewHelpers\Util\Macro\LcCommerceData;

use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Dtos\Documents\Rows\DocumentRowOption;

/**
 * This is the OrderRowOption class, a DTO class for the utilViewHelper LcCommerceData.
 *
 * @package FWK\ViewHelpers\Util\Macro\LcCommerceData
 */
class OrderRowOption {
    use ElementTrait;

    private int $id = 0;

    private string $name = '';

    private string $value = '';

    /**
     * Constructor method for OrderRowOption
     *
     * @param Option $basket
     */
    public function __construct(DocumentRowOption $option) {
        $this->id = $option->getId();
        $this->name = $option->getName();
        $this->value = $option->getValue();
    }
}
