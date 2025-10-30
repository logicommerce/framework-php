<?php

declare(strict_types=1);

namespace FWK\Core\Dtos\Traits;

use SDK\Core\Dtos\ElementCollection;

/**
 * This is the Related items trait.
 *
 * @package FWK\Core\Dtos\Traits
 */
trait RelatedItemsTrait {

    protected ?ElementCollection $relatedItems = null;

    /**
     * Returns the relatedItems.
     *
     * @return null|ElementCollection
     */
    public function getRelatedItems(): ?ElementCollection {
        return $this->relatedItems;
    }

    public function setRelatedItems(ElementCollection $relatedItems): void {
        $this->relatedItems = $relatedItems;
    }
    
}
