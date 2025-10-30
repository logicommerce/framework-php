<?php

namespace FWK\ViewHelpers\Product\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\ViewHelpers\Macros\Product\Macro\BundleGrouping as CoreMacroBundleGrouping;
use SDK\Dtos\Catalog\BundleGrouping as CatalogBundleGrouping;
use SDK\Dtos\Catalog\Product\Product;

/**
 * This is the BundleGrouping class, a macro class for the productViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the product's buttonClearProductsFilter.
 *
 * @see BundleGrouping::getViewParameters()
 *
 * @package FWK\ViewHelpers\Product\Macro
 */
class BundleGrouping extends CoreMacroBundleGrouping {

    public ?CatalogBundleGrouping $bundleGrouping = null;

    public bool $addMainProducts = true;

    public array $products = [];

    public int $bundleId = 0;

    /**
     * Constructor method for Bundles class.
     * 
     * @see Bundles
     * 
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        ViewHelper::mergeArguments($this, $arguments);
    }

    /**
     * This method returns all calculated arguments and new parameters for ProductViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        if (is_null($this->bundleGrouping)) {
            throw new CommerceException("The value of [bundleGrouping] argument is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }
        $auxProducts = [];
        foreach ($this->products as $product) {
            if (!$product instanceof Product) {
                throw new CommerceException('Each element of products must be a instance of ' . Product::class . '. ' . ' Instance of ' . get_class($product) . ' sended.', CommerceException::VIEW_HELPER_INVALID_ARGUMENT);
            }
            $auxProducts[$product->getId()] = $product;
        }
        $products = $auxProducts;

        return $this->getProperties();
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        return parent::getProperties() + [
            'bundleGrouping' => $this->bundleGrouping,
            'addMainProducts' => $this->addMainProducts,
            'products' => $this->products,
            'bundleId' => $this->bundleId
        ];
    }
}
