<?php

declare(strict_types=1);

namespace FWK\Core\Controllers\Traits;

use FWK\Dtos\Catalog\PhysicalLocation;
use SDK\Core\Dtos\ElementCollection;
use SDK\Core\Dtos\PhysicalLocationsCollection;
use SDK\Enums\DeliveryType;
use SDK\Enums\PickingDeliveryType;

/**
 * This is the set delivery trait.
 *
 * @see SetPhysicalLocationsFromDeliveries::GetPhysicalLocationsFromDeliveries()
 *
 * @package FWK\Core\Controllers\Traits
 */
trait SetPhysicalLocationsFromDeliveries {

    /**
     * Add to controller data the physical locations from the given deliveries.
     * 
     * @return void
     */
    protected function setPhysicalLocationsFromDeliveries(ElementCollection $deliveries, string $dataKey, string $defaultDataKey): void {
        $physicalLocationFromDeliveries = [];
        $physicalLocationFromDeliveriesCount = 0;
        foreach ($deliveries->getItems() as $delivery) {
            if ($delivery->getType() == DeliveryType::PICKING) {
                $physicalLocationFromDeliveriesCount++;
                if ($delivery->getMode()->getType() == PickingDeliveryType::PROVIDER_PICKUP_POINT) {
                    $physicalLocationFromDelivery = $delivery->getMode()->getProviderPickupPoint()->toArray();
                    $physicalLocationFromDelivery['id'] = 0 - $physicalLocationFromDeliveriesCount;
                } else {
                    $physicalLocationFromDelivery = $delivery->getMode()->getPhysicalLocation()->toArray();
                }
                $physicalLocationFromDelivery['hash'] = $delivery->getHash();
                $physicalLocation = new PhysicalLocation($physicalLocationFromDelivery);
                $physicalLocation->setDelivery($delivery);
                $physicalLocationFromDeliveries[] = $physicalLocation;
                if ($delivery->getSelected()) {
                    $this->setDataValue($defaultDataKey, $physicalLocation->getId());
                }
            }
        }

        if ($physicalLocationFromDeliveriesCount > 0) {
            $physicalLocations = new PhysicalLocationsCollection([
                'items' => $physicalLocationFromDeliveries,
                'filter' => (is_null($deliveries->getFilter()) || !method_exists($deliveries->getFilter(), 'getPhysicalLocations') || is_null($deliveries->getFilter()->getPhysicalLocations())) ? [] : $deliveries->getFilter()->getPhysicalLocations()->toArray(),
                'pagination' => [
                    'page' => 1,
                    'perPage' => $physicalLocationFromDeliveriesCount,
                    'totalItems' => $physicalLocationFromDeliveriesCount,
                    'totalPages' => 1,
                ],
            ]);
        } else {
            $physicalLocations = new ElementCollection();
        }

        $this->setDataValue($dataKey, $physicalLocations);
    }
}
