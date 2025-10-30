<?php

namespace FWK\Dtos\Catalog;

use SDK\Dtos\Basket\BasketPickingDelivery;
use SDK\Dtos\Catalog\PhysicalLocation as SDKPhysicalLocation;

/**
 * This is the PhysicalLocation container class.
 *
 * @see getHash()
 * @see setHash()
 * @see getShowInMap()
 * @see setShowInMap()
 * @see getDelivery
 * 
 * @package FWK\Dtos\Catalog
 */
class PhysicalLocation extends SDKPhysicalLocation {

    protected string $hash = '';

    protected bool $showInMap = false;

    protected ?BasketPickingDelivery $delivery = null;

    /**
     * Returns the hash.
     *
     * @return string
     */
    public function getHash(): string {
        return $this->hash;
    }

    /**
     * Set the hash.
     * 
     * @param string $hash
     *
     */
    public function setHash(string $hash): void {
        $this->hash = $hash;
    }

    /**
     * Returns the showInMap.
     *
     * @return bool
     */
    public function getShowInMap(): bool {
        return $this->showInMap;
    }

    /**
     * Set the showInMap.
     * 
     * @param bool $showInMap
     *
     */
    public function setShowInMap(bool $showInMap): void {
        $this->showInMap = $showInMap;
    }


    /**
     * Returns the delivery.
     *
     * @return ?BasketPickingDelivery
     */
    public function getDelivery(): ?BasketPickingDelivery {
        return $this->delivery;
    }

    /**
     * Set the delivery.
     * 
     * @param BasketPickingDelivery $delivery
     *
     */
    public function setDelivery(array|BasketPickingDelivery $delivery): void {
        if (is_array($delivery)) {
            $this->delivery = new BasketPickingDelivery($delivery);
        } else {
            $this->delivery = $delivery;
        }
    }
}
