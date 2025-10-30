<?php

namespace FWK\Core\Resources;

use SDK\Core\RegistryTrait;

/**
 * This is the Registry class, this class defines the general registry keys (application, connection)
 * and allows to store for each one the corresponding instance.
 *
 * @see Registry::APPLICATION
 * @see Registry::CONNECTION
 *
 * @see RegistryTrait
 *
 * @package FWK\Core\Resources
 */
abstract class Registry {
    use RegistryTrait;

    
    //These constants sets the ones that can be used as keys on this Registry

    public const APPLICATION = 'application';

    public const CONNECTION = 'connection';

    /**
     * Stored instances for each registry key.
     * @var array
     */
    private static $storedValues = [
        self::APPLICATION => null,
        self::CONNECTION => null
    ];
}
