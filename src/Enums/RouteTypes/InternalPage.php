<?php

namespace FWK\Enums\RouteTypes;

use SDK\Core\Enums\Enum;

/**
 * This is the InternalPage enumeration class.
 * This class declares enumerations for a page route type.
 * <br> This class extends SDK\Core\Enums\Enum, see this class.
 *
 * @see InternalPage::SEND_CONTACT
 * @see InternalPage::SPONSOR_SHIP
 * @see InternalPage::PRIVACY_POLICY
 * @see InternalPage::TERMS_OF_USE
 * @see InternalPage::SEND_MAIL
 *
 * @see Enum
 *
 * @package FWK\Enums\RouteTypes
 */
class InternalPage extends Enum {

    public const SEND_CONTACT = 'PAGE_INTERNAL_SEND_CONTACT';

    public const SPONSOR_SHIP = 'PAGE_INTERNAL_SPONSOR_SHIP';

    public const PRIVACY_POLICY = 'PAGE_INTERNAL_PRIVACY_POLICY';

    public const TERMS_OF_USE = 'PAGE_INTERNAL_TERMS_OF_USE';

    public const SEND_MAIL = 'PAGE_INTERNAL_SEND_MAIL';
}
