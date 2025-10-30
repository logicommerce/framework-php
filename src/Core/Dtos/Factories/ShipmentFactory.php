<?php

namespace FWK\Core\Dtos\Factories;

use FWK\Dtos\Basket\BasketShipment;
use FWK\Dtos\Basket\Shipment;
use SDK\Dtos\Basket\BaseShipment;
use SDK\Core\Dtos\Factories\ShipmentFactory as SDKFactoriesShipmentFactory;

/**
 * This class will return the kind of shipment we need.
 *
 * @see ShipmentFactory::getShipment()
 * @see ShipmentFactory::getElement()
 * 
 * @see SDK\Core\Dtos\Factories\ShipmentFactory
 *
 * @package FWK\Core\Dtos\Factories
 */
abstract class ShipmentFactory extends SDKFactoriesShipmentFactory {

    /**
     * Returns the needed type of shipment.
     *
     * @return BaseShipment|NULL
     */
    public static function getShipment(array $data = []): ?BaseShipment {
        if (isset($data['shippings'])) {
            return new Shipment($data);
        }
        return new BasketShipment($data);
    }

    /**
     *
     * @see \SDK\Core\Dtos\Factory::getElement()
     */
    public static function getElement(array $data = []): ?BaseShipment {
        return self::getShipment($data);
    }
}
