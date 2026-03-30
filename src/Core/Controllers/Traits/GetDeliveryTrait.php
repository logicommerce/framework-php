<?php

declare(strict_types=1);

namespace FWK\Core\Controllers\Traits;

use SDK\Core\Dtos\Element;

/**
 * This is the get delivery trait.
 *
 * @see GetDeliveryTrait::getDeliveryResponseData()
 *
 * @package FWK\Core\Controllers\Traits
 */
trait GetDeliveryTrait {

    private array $shipments = [];

    /**
     * Returns the response data for the get deliveries actions.
     * 
     * @return Element|NULL
     */
    protected function getDeliveryResponseData(): ?Element {
        return $this->basketService->getShippingDeliveries();
    }
}
