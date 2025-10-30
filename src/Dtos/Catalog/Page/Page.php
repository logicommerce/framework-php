<?php

namespace FWK\Dtos\Catalog\Page;

use FWK\Core\Dtos\Traits\RelatedItemsTrait;
use SDK\Dtos\Catalog\Page\Page as SDKPage;

/**
 * This is the Page container class.
 * 
 * @see RelatedItemsTrait
 *
 * @package FWK\Dtos\Catalog
 */
class Page extends SDKPage{
    use RelatedItemsTrait;

}
