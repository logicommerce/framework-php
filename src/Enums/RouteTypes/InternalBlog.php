<?php

namespace FWK\Enums\RouteTypes;

use SDK\Core\Enums\Enum;

/**
 * This is the InternalBlog enumeration class.
 * This class declares Blog route type enumerations.
 * <br> This class extends SDK\Core\Enums\Enum, see this class.
 *
 * @abstract
 *
 * @see InternalBlog::ADD_COMMENT
 * @see InternalBlog::CATEGORY_SUBSCRIBE
 * @see InternalBlog::POST_SUBSCRIBE
 * @see InternalBlog::SUBSCRIBE
 *
 * @see Enum
 *
 * @package FWK\Enums\RouteTypes
 */
abstract class InternalBlog extends Enum {
    
    public const ADD_COMMENT = 'BLOG_INTERNAL_ADD_COMMENT';    

    public const CATEGORY_SUBSCRIBE = 'BLOG_INTERNAL_CATEGORY_SUBSCRIBE';

    public const POST_SUBSCRIBE = 'BLOG_INTERNAL_POST_SUBSCRIBE';

    public const SUBSCRIBE = 'BLOG_INTERNAL_SUBSCRIBE';
}
