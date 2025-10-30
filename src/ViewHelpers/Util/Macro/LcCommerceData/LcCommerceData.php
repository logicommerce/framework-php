<?php

declare(strict_types=1);

namespace FWK\ViewHelpers\Util\Macro\LcCommerceData;

use FWK\Core\Resources\Utils;
use SDK\Core\Dtos\ElementCollection;
use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Dtos\Catalog\Product\Product;
use SDK\Dtos\Common\Route;
use SDK\Dtos\Catalog\Category;
use SDK\Dtos\Documents\Document;


/**
 * This is the LcCommerceData class, a DTO class for the utilViewHelper LcCommerceData.
 *
 * @package FWK\ViewHelpers\Util\Macro\LcCommerceData
 */
class LcCommerceData implements \JsonSerializable {
    use ElementTrait;

    private ?Route $route = null;

    private ?Navigation $navigation = null;

    private ?PageProduct $main_pageProduct = null;

    private ?PageCategory $main_pageCategory = null;

    private ?PageProducts $main_pageProducts = null;

    private ?Order $order = null;

    private array $properties = [];

    private string $folcsVersion = '';

    /**
     * Constructor method for LcCommerceData
     *
     * @see LcCommerceData
     *
     * @param array $arguments
     */
    public function __construct(Route $route) {
        $this->route = $route;
        $this->setNavigation();
    }

    private function getObjectProperties(array $data = []): array {
        return $this->properties;
    }

    public function setNavigation(): void {
        $this->navigation = new Navigation($this->route);
        $this->properties['navigation'] = $this->navigation;
    }

    public function setPageProduct(Product $product): void {
        $this->main_pageProduct = new PageProduct($product, $this->route);
        $this->properties['main_pageProduct'] = $this->main_pageProduct;
    }

    public function setPageCategory(Category $category, ?ElementCollection $categoryProducts = null): void {
        $this->main_pageCategory = new PageCategory($category, $categoryProducts);
        $this->properties['main_pageCategory'] = $this->main_pageCategory;
    }

    public function setPageProducts(?ElementCollection $categoryProducts = null): void {
        $this->main_pageProducts = new PageProducts($categoryProducts);
        $this->properties['main_pageProducts'] = $this->main_pageProducts;
    }

    public function setOrder(Document $order): void {
        $this->order = new Order($order);
        $this->properties['order'] = $this->order;
    }

    public function setFolcsVersion(bool $cacheable): void {
        $this->folcsVersion = Utils::getFolcsVersion($cacheable);
        $this->properties['folcsVersion'] = $this->folcsVersion;
    }
}
