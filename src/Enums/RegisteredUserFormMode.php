<?php

namespace FWK\Enums;

use SDK\Core\Enums\Enum;

/**
 * This is the RegisteredUserFormMode enumeration class.
 * This class declares enumerations for applicable filters sort.
 * <br> This class extends SDK\Core\Enums\Enum, see this class.
 *
 * @abstract
 *
 * @see RegisteredUserFormMode::NEW
 * @see RegisteredUserFormMode::EXISTENT
 *
 * @see Enum
 *
 * @package FWK\Enums
 */
abstract class RegisteredUserFormMode extends Enum {

    public const NEW = 'new';

    public const INTERNAL = 'internal';

    public const EXTERNAL = 'external';
}
