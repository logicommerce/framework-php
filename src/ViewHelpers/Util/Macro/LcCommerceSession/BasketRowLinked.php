<?php

declare(strict_types=1);

namespace FWK\ViewHelpers\Util\Macro\LcCommerceSession;

use SDK\Dtos\Basket\BasketRows\Linked;
use SDK\Core\Dtos\Traits\ElementTrait;

/**
 * This is the BasketRowLinked class, a DTO class for the utilViewHelper LcCommerceSession.
 *
 * @package FWK\ViewHelpers\Util\Macro\LcCommerceSession
 */
class BasketRowLinked extends BasketRowProduct {
    use ElementTrait;

    private int $parentId = 0;

    private ?string $parentHash = null;

    /**
     * Constructor method for BasketRowLinked
     * 
     * @see BasketRow
     *  
     * @param Linked $basketRow
     */
    public function __construct(Linked $basketRow) {
        parent::__construct($basketRow);
        $this->parentId = $basketRow->getParentId();
        $this->parentHash = $basketRow->getParentHash();
    }

    /**
     * Sets the parent hash.
     */
    public function setParentHash(string $parentHash): void {
        $this->parentHash = $parentHash;
    }

}
