<?php

namespace FWK\Dtos\Blog;

use FWK\Core\Dtos\Traits\RelatedItemsTrait;
use SDK\Core\Dtos\ElementCollection;
use SDK\Dtos\Blog\BlogPost as SDKBlogPost;

/**
 * This is the BlogPost container class.
 *
 * @see BlogPost::getMainCategoryData()
 * 
 * @see RelatedItemsTrait
 *
 * @package FWK\Dtos\Blog
 */
class BlogPost extends SDKBlogPost{
    use RelatedItemsTrait;

    protected ?ElementCollection $mainCategoryData = null;

    /**
     * Returns the mainCategoryData.
     *
     * @return null|ElementCollection
     */
    public function getMainCategoryData(): ?ElementCollection {
        return $this->mainCategoryData;
    }

    /**
     * Set the mainCategoryData.
     * 
     * @param $mainCategoryData ElementCollection
     *
     */
    public function setMainCategoryData(ElementCollection $mainCategoryData): void {
        $this->mainCategoryData = $mainCategoryData;
    }

}
