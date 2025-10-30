<?php

namespace FWK\Core\Dtos\Factories;

use SDK\Core\Dtos\Factory;
use SDK\Dtos\Documents\Transactions\Deliveries\DocumentDelivery;

/**
 * This class will return the kind of DocumentDelivery we need.
 *
 * @see Factory::getElement()
 * @see DocumentDeliveryFactory::getDocumentDelivery()
 *
 * @uses Factory
 * @see Factory
 *
 * @package FWK\Dtos\Factories
 */
abstract class DocumentDeliveryFactory extends Factory {

    protected const TYPE = 'type';

    protected const NAMESPACE = 'FWK\Dtos\Documents';

    protected const CLASS_SUFFIX = 'DocumentDelivery';

    /**
     * Returns the needed type of DocumentDelivery.
     *
     * @return DocumentDelivery|NULL
     */
    public static function getDocumentDelivery(array $data = []): ?DocumentDelivery {
        return self::getItem($data);
    }

    /**
     *
     * @see \SDK\Core\Dtos\Factory::getElement()
     */
    public static function getElement(array $data = []): ?DocumentDelivery {
        return self::getDocumentDelivery($data);
    }
}
