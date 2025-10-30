<?php

declare(strict_types=1);

namespace FWK\Core\Controllers\Traits;

use SDK\Core\Dtos\Element;
use SDK\Services\Parameters\Groups\Basket\EditShipmentParametersGroup;
use SDK\Services\Parameters\Groups\Basket\EditShipmentsParametersGroup;
use FWK\Enums\Parameters;
use SDK\Enums\DeliveryType;

/**
 * This is the set delivery trait.
 *
 * @see SetDeliveryTrait::getDeliveryResponseData()
 *
 * @package FWK\Core\Controllers\Traits
 */
trait SetDeliveryTrait {

    private array $shipments = [];

    /**
     * Returns the response data for the set deliveries actions.
     * 
     * @return Element|NULL
     */
    protected function getDeliveryResponseData(string $type = '', string $deliveryHash = '', array $shipments = [], string $providerPickupPointHash = ''): ?Element {
        if (strlen($type) === 0) {
            $type = $this->getRequestParam(Parameters::TYPE, true);
        }
        if (strlen($deliveryHash) === 0) {
            $deliveryHash = $this->getRequestParam(Parameters::DELIVERY_HASH, true);
        }
        if (strlen($providerPickupPointHash) === 0) {
            $providerPickupPointHash = $this->getRequestParam(Parameters::PROVIDER_PICKUP_POINT_HASH, false, '');
        }

        $editShipmentsParametersGroup = new EditShipmentsParametersGroup();
        $editShipmentsParametersGroup->setDeliveryHash($deliveryHash);
        if (!empty($providerPickupPointHash)) {
            $editShipmentsParametersGroup->setProviderPickupPointHash($providerPickupPointHash);
        }

        if ($type === DeliveryType::SHIPPING) {
            if (count($shipments) === 0) {
                $shipments = $this->getRequestParam(Parameters::SHIPMENTS, true);
            }
            foreach ($shipments as $shipment) {
                if ($this->isValidShipment($shipment)) {
                    $editShipmentParametersGroup = new EditShipmentParametersGroup();
                    $editShipmentParametersGroup->setSelectedShippingHash($shipment[Parameters::SHIPPING_HASH]);
                    $editShipmentParametersGroup->setShipmentHash($shipment[Parameters::SHIPMENT_HASH]);
                    $editShipmentsParametersGroup->addShipment($editShipmentParametersGroup);
                }
            }
        }
        $this->appliedParameters['setDelivery'] = $editShipmentsParametersGroup->toArray();

        return $this->basketService->setShippings($editShipmentsParametersGroup);
    }

    private function isValidShipment($shipment): bool {
        if (strlen($shipment[Parameters::SHIPMENT_HASH]) && strlen($shipment[Parameters::SHIPPING_HASH])) {
            return true;
        }
        return false;
    }
}
