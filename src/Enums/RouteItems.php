<?php

namespace FWK\Enums;

/**
 * This is the RouteItems enumeration class.
 * This class declares route items enumerations.
 *
 * @abstract
 *
 * @see RouteItems::TYPE
 * @see RouteItems::STATUS
 * @see RouteItems::LANGUAGE
 * @see RouteItems::CURRENCY
 * @see RouteItems::AVAILABLE_LANGUAGES
 * @see RouteItems::REDIRECT_URL
 * @see RouteItems::ID
 * @see RouteItems::THEME
 * @see RouteItems::LAYOUT
 * @see RouteItems::CONTENT
 * @see RouteItems::NAME
 * @see RouteItems::OS
 * @see RouteItems::CHANNEL
 * @see RouteItems::DEVICE
 * @see RouteItems::ENUM_CLASS
 * 
 * @package FWK\Enums
 */
abstract class RouteItems {

    public const TYPE = 'type';

    public const STATUS = 'status';

    public const LANGUAGE = 'language';

    public const CURRENCY = 'currency';

    public const AVAILABLE_LANGUAGES = 'availableLanguages';

    public const REDIRECT_URL = 'redirectUrl';

    public const ID = 'id';

    public const THEME = 'theme';

    public const LAYOUT = 'layout';

    public const CONTENT = 'content';

    public const NAME = 'name';

    public const OS = 'os';

    public const CHANNEL = 'channel';

    public const DEVICE = 'device';

    public const ENUM_CLASS = 'enumClass';

    public const METADATA = 'metadata';

    public const INDEXABLE = 'indexable';

    public const LINK_FOLLOWING = 'linkFollowing';
}
