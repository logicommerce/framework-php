<?php

namespace FWK\Core\Dtos\Factories;

use SDK\Core\Dtos\Factory;
use SDK\Dtos\Basket\BasketDelivery;

/**
 * This class will return the kind of BasketDelivery we need.
 *
 * @see Factory::getElement()
 * @see BasketDeliveryFactory::getBasketDelivery()
 *
 * @uses Factory
 * @see Factory
 *
 * @package FWK\Dtos\Factories
 */
abstract class BasketDeliveryFactory extends Factory {

    protected const TYPE = 'type';

    protected const NAMESPACE = 'FWK\Dtos\Basket';

    protected const CLASS_PREFIX = 'Basket';

    protected const CLASS_SUFFIX = 'Delivery';

    /**
     * Returns the needed type of BasketDelivery.
     *
     * @return BasketDelivery|NULL
     */
    public static function getBasketDelivery(array $data = []): ?BasketDelivery {
        return self::getItem($data);
    }

    /**
     *
     * @see \SDK\Core\Dtos\Factory::getElement()
     */
    public static function getElement(array $data = []): ?BasketDelivery {
        return self::getBasketDelivery($data);
    }
}
