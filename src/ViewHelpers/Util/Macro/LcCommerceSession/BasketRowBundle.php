<?php

declare(strict_types=1);

namespace FWK\ViewHelpers\Util\Macro\LcCommerceSession;

use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Dtos\Basket\BasketRows\Bundle;

/**
 * This is the Basket class, a DTO class for the utilViewHelper LcCommerceSession.
 *
 * @package FWK\ViewHelpers\Util\Macro\LcCommerceSession
 */
class BasketRowBundle extends BasketRow {
    use ElementTrait;

    private array $items = [];

    /**
     * Constructor method for BasketRowBundle
     * 
     * @see BasketRow
     *  
     * @param Bundle $basketRow
     */
    public function __construct(Bundle $basketRow) {
        parent::__construct($basketRow);
        foreach ($basketRow->getItems() as $item) {
            $this->items[] = new BasketRowBundleItem($item);
        }
    }
}
