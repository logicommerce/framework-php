<?php

declare(strict_types=1);

namespace FWK\ViewHelpers\Util\Macro\LcCommerceData;

use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Dtos\Catalog\Product\Product as ProductDto;
use SDK\Dtos\Common\Route;
use SDK\Enums\ItemType;

/**
 * This is the PageProduct class, a DTO class for the utilViewHelper LcCommerceData.
 *
 * @package FWK\ViewHelpers\Util\Macro\LcCommerceData
 */
class PageProduct extends Product {
    use ElementTrait;

    private bool $stockAvailable = false;

    private string $url = '';

    private string $image = '';

    private string $description = '';

    private bool $offer = false;

    private array $combinations = [];

    private array $options = [];

    /**
     * Constructor method for PageProduct
     *
     * @param ProductDto $product
     * @param Route $route
     */
    public function __construct(ProductDto $product, Route $route) {
        parent::__construct($product);
        $categoriesInBreadcrumb = array_filter($route->getBreadcrumb(), fn ($item) => $item->getItemType() == ItemType::CATEGORY);
        $lastCategoriesInBreadcrumb = end($categoriesInBreadcrumb);
        $this->category = $lastCategoriesInBreadcrumb != false ? $lastCategoriesInBreadcrumb->getName() : '';
        $this->stockAvailable = $product->getCombinationData()?->getStock()->getUnits() > 0 ? true : false;
        $this->url = $product->getLanguage()->getUrlSeo();
        $this->image = $product->getMainImages()->getMediumImage();
        $this->description = $product->getLanguage()->getShortDescription();
        $this->offer = $product->getDefinition()->getOffer() > 0 ? true : false;
        foreach ($product->getCombinations() as $combination) {
            $this->combinations[] = new Combination($combination, $product->getCombinationData());
        }
        foreach ($product->getOptions() as $option) {
            $this->options[] = new Option($option);
        }
    }
}
