<?php

namespace FWK\Dtos\Catalog;

use FWK\Core\Dtos\Traits\RelatedItemsTrait;
use SDK\Dtos\Catalog\CategoryTree as SDKCategoryTree;

/**
 * This is the CategoryTree container class.
 *
 * @see RelatedItemsTrait
 *
 * @package FWK\Dtos\Catalog
 */
class CategoryTree extends SDKCategoryTree{
    use RelatedItemsTrait;

}
