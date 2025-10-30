<?php

declare(strict_types=1);

namespace FWK\ViewHelpers\Util\Macro\LcCommerceData;

use FWK\Core\ViewHelpers\Macros\Util\LcCommerce\ProductCodes;
use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Dtos\Catalog\Product\Product as ProductDto;

/**
 * This is the Product class, a DTO class for the utilViewHelpers LcCommerce.
 *
 * @see LcCommerceData::getViewParameters()
 *
 * @package FWK\ViewHelpers\Util\Macro\LcCommerceData;
 */
class Product {
    use ElementTrait;

    protected int $id = 0;

    protected string $pId = '';

    protected string $name = '';

    protected string $brand = '';

    protected string $category = '';

    protected ?Prices $prices = null;

    protected ?Prices $pricesWithTaxes = null;

    protected ?ProductCodes $codes = null;

    /**
     * Constructor method for Product
     * 
     * @param ProductDto $product
     */
    public function __construct(ProductDto $product) {
        $this->id = $product->getId();
        $this->pId = $product->getPId();
        $this->name = $product->getLanguage()->getName();
        $this->brand = $product->getBrand()?->getLanguage()->getName() ?? '';
        $this->prices = new Prices($product->getCombinationData()->getPrices());
        $this->pricesWithTaxes = new Prices($product->getCombinationData()->getPricesWithTaxes());
        $this->codes = new ProductCodes($product->getCombinationData()->getProductCodes() ?? $product->getCodes());
        $this->category = $product->getCategories()[0]->getName();
    }
}
