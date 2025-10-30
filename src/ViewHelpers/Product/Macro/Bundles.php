<?php

namespace FWK\ViewHelpers\Product\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\ViewHelpers\Macros\Product\Macro\BundleGrouping;
use FWK\Services\Dtos\BundleDefinitionsWithGroupings;

/**
 * This is the Bundles class, a macro class for the productViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the product's buttonClearProductsFilter.
 *
 * @see Bundles::getViewParameters()
 *
 * @package FWK\ViewHelpers\Product\Macro
 */
class Bundles extends BundleGrouping {

    public ?BundleDefinitionsWithGroupings $productBundles = null;

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
        if (is_null($this->productBundles)) {
            throw new CommerceException("The value of [productBundles] argument is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }
        return $this->getProperties();
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        return parent::getProperties() + [
            'productBundles' => $this->productBundles
        ];
    }
}
