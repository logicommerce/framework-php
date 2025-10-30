<?php

namespace FWK\Enums;

use SDK\Core\Enums\Enum;

/**
 * This is the Pagination enumeration class.
 * This class declares enumerations for pagination.
 * <br> This class extends SDK\Core\Enums\Enum, see this class.
 *
 * @abstract
 * 
 * @see Pagination::PAGER_PARAMETERS
 * @see Pagination::PAGES_TO_SHOW
 * @see Pagination::TYPE_BEFORE
 * @see Pagination::TYPE_AFTER
 * @see Pagination::TYPE_SEPARATOR
 * @see Pagination::TYPE_PAGE
 * @see Pagination::ITEM_CLASS
 * @see Pagination::ITEM_LABEL
 * @see Pagination::BEFORE_CLASS
 * @see Pagination::BEFORE_LABEL
 * @see Pagination::PAGE_CLASS
 * @see Pagination::PAGE_LABEL
 * @see Pagination::AFTER_CLASS
 * @see Pagination::AFTER_LABEL
 * @see Pagination::SEPARATOR_CLASS
 * @see Pagination::SEPARATOR_LABEL
 * @see Pagination::LINK_TARGET
 * @see Pagination::PAGE_SELECTED_CLASS
 *
 * @see Enum
 *
 * @package FWK\Enums
 */
abstract class Pagination extends Enum {

    public const PAGER_PARAMETERS = 'pagerParameters';

    public const PAGES_TO_SHOW = 'pagesToShow';


    public const TYPE_BEFORE = 'before';

    public const TYPE_AFTER = 'after';

    public const TYPE_SEPARATOR = 'separator';

    public const TYPE_PAGE = 'page';


    public const ITEM_CLASS = 'Class';

    public const ITEM_LABEL = 'Label';


    public const BEFORE_CLASS = self::TYPE_BEFORE . self::ITEM_CLASS;

    public const BEFORE_LABEL = self::TYPE_BEFORE . self::ITEM_LABEL;

    public const PAGE_CLASS = self::TYPE_PAGE . self::ITEM_CLASS;

    public const PAGE_LABEL = self::TYPE_PAGE . self::ITEM_LABEL;

    public const AFTER_CLASS = self::TYPE_AFTER . self::ITEM_CLASS;

    public const AFTER_LABEL = self::TYPE_AFTER . self::ITEM_LABEL;

    public const SEPARATOR_CLASS = self::TYPE_SEPARATOR . self::ITEM_CLASS;

    public const SEPARATOR_LABEL = self::TYPE_SEPARATOR . self::ITEM_LABEL;

    public const LINK_TARGET = 'linkTarget';

    public const LINK_IS_FOLLOW = 'linkIsFollow';

    public const SELECTED_CLASS = 'selected' . self::ITEM_CLASS;
}
