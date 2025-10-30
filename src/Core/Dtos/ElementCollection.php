<?php

declare(strict_types=1);

namespace FWK\Core\Dtos;

use FWK\Core\Dtos\Factories\BasketDeliveryFactory;
use FWK\Core\Dtos\Traits\FillFromParentTrait;
use SDK\Core\Dtos\ElementCollection as SDKElementCollection;
use FWK\Dtos\Catalog\PhysicalLocation;
use SDK\Core\Dtos\DeliveriesCollection;
use SDK\Core\Dtos\PhysicalLocationsCollection;

/**
 * This is the main collection class
 *
 * @see SDK\Core\Dtos\ElementCollection
 * @see FillFromParentTrait
 *
 * @package FWK\Core\Dtos
 */
class ElementCollection extends SDKElementCollection {
    use FillFromParentTrait;

    public static function getCollectionClass(array $data, string $class) {
        if ($class === PhysicalLocation::class) {
            return PhysicalLocationsCollection::class;
        } else if ($class === BasketDeliveryFactory::class)
            return DeliveriesCollection::class;
        else {
            return parent::getCollectionClass($data, $class);
        }
    }
}
