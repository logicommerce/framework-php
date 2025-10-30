<?php

namespace FWK\Enums;

use SDK\Core\Enums\Enum;

/**
 * This is the Controllers enumeration class.
 * This class declares Controllers enumerations.
 * <br> This class extends SDK\Core\Enums\Enum, see this class.
 *
 * @abstract
 *
 * @see Controllers::AREA
 * @see Controllers::BANNER
 * @see Controllers::NEWS
 * @see Controllers::CATEGORY
 * @see Controllers::PAGE
 * @see Controllers::BASKET
 *
 * @see Enum
 *
 * @package FWK\Enums
 */
abstract class Controllers extends Enum {

    public const AREA = 'area';

    public const BANNER = 'banner';

    public const NEWS = 'news';

    public const CATEGORY = 'category';

    public const PAGE = 'page';

    public const BASKET = 'basket';
}

