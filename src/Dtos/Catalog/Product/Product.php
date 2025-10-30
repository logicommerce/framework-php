<?php

namespace FWK\Dtos\Catalog\Product;

use FWK\Core\Dtos\Traits\RelatedItemsTrait;
use SDK\Dtos\Catalog\Product\Product as SDKProduct;
use FWK\Dtos\Catalog\Product\Options\Option;
use FWK\Core\Dtos\Traits\FillFromParentTrait;

/**
 * This is the Product container class.
 * 
 * @see RelatedItemsTrait
 * @see FillFromParentTrait
 *
 * @package FWK\Dtos\Catalog
 */
class Product extends SDKProduct{
    use RelatedItemsTrait, FillFromParentTrait;

    protected function setOptions(array $options): void {
        $this->options = $this->setArrayField($options, Option::class);
    }

}
