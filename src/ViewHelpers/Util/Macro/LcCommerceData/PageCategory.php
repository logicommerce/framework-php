<?php

declare(strict_types=1);

namespace FWK\ViewHelpers\Util\Macro\LcCommerceData;

use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Core\Dtos\ElementCollection;
use SDK\Dtos\Catalog\Category;

/**
 * This is the PageCategory class, a DTO class for the utilViewHelper LcCommerceData.
 *
 * @package FWK\ViewHelpers\Util\Macro\LcCommerceData
 */
class PageCategory {
    use ElementTrait;

    private int $id = 0;

    private string $pId = '';

    private string $name = '';

    private array $products = [];

    /**
     * 
     * Constructor method for PageCategory
     *
     * @param Category $category
     * @param ?ElementCollection $products
     */
    public function __construct(Category $category, ?ElementCollection $products = null) {
        $this->id = $category->getId();
        $this->pId = $category->getPId();
        $this->name = $category->getLanguage()->getName();
        if (!is_null($products)) {
            foreach ($products as $product) {
                $this->products[] = new Product($product);
            }
        }
    }
}
