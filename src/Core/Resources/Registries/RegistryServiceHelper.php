<?php

namespace FWK\Core\Resources\Registries;

/**
 * This is the RegistryServiceHelper class, this class defines the registry key of each service helper
 * and allows to store for each key the corresponding service helper instance.
 *
 * @see RegistryServiceHelper::CATEGORY_PRODUCT_HELPER
 *
 * @see RegistryTrait
 *
 * @package FWK\Core\Resources\Registries
 */
abstract class RegistryServiceHelper {

    use \SDK\Core\RegistryTrait;

    /**
     * These constants sets the ones that can be used as keys on this Registry
     */
    public const CATEGORY_PRODUCT_HELPER = 'CategoryProductHelper';

    /**
     *
     * @var array
     */
    private static $storedValues = [
        self::CATEGORY_PRODUCT_HELPER => null,
    ];
}
