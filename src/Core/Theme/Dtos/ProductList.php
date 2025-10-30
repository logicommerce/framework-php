<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Element;
use SDK\Core\Dtos\Traits\ElementTrait;

/**
 * This is the 'ProductList' class, a DTO class for the theme configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @abstract
 *
 * @see ProductList::getProductList()
 *
 * @see Element
 * @see ElementTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
abstract class ProductList extends Element {
    use ElementTrait;

    public const PRODUCT_LIST = 'productList';

    protected ?ItemList $productList = null;

    /**
     * This method returns the productList configuration.
     *
     * @return ItemList|NULL
     */
    public function getProductList(): ?ItemList {
        return $this->productList;
    }

    protected function setProductList(array $productList): void {
        $this->productList = new ItemList($productList);
    }
}
