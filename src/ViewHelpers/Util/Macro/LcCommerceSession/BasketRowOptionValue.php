<?php

declare(strict_types=1);

namespace FWK\ViewHelpers\Util\Macro\LcCommerceSession;

use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Dtos\Basket\BasketRows\Options\OptionValue;

/**
 * This is the BasketRowOptionValue class, a DTO class for the utilViewHelper LcCommerceSession.
 *
 * @package FWK\ViewHelpers\Util\Macro\LcCommerceSession
 */
class BasketRowOptionValue {
    use ElementTrait;

    private int $id = 0;

    private string $value = '';

    /**
     * Constructor method for BasketRowOptionValue
     *
     * @param OptionValue $value
     * 
     */
    public function __construct(OptionValue $value) {
        $this->id = $value->getId();
        $this->value = $value->getValue();
    }
}
