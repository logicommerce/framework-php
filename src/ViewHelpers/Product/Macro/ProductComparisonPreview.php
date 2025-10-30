<?php

namespace FWK\ViewHelpers\Product\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use SDK\Dtos\Catalog\Product\ProductComparison as ProductProductComparison;

/**
 * This is the ProductComparisonPreview class, a macro class for the productViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the product's rate.
 *
 * @see ProductComparisonPreview::getViewParameters()
 *
 * @package FWK\ViewHelpers\Product\Macro
 */
class ProductComparisonPreview {

    public ?ProductProductComparison $productComparison = null;

    private string $class = '';

    /**
     * Constructor method for ProductComparisonPreview class.
     * 
     * @see ProductComparisonPreview
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
        if (!is_null($this->productComparison) && !($this->productComparison instanceof ProductProductComparison)) {
            throw new CommerceException('The value of productComparison argument must be a instance of ' . ProductProductComparison::class. '. ' . ' Instance of ' . get_class($this->productComparison) . ' sended.', CommerceException::VIEW_HELPER_INVALID_ARGUMENT);
        }
        return $this->getProperties();
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        return [
            'productComparison' => $this->productComparison,
            'class' => $this->class
        ];
    }
}
