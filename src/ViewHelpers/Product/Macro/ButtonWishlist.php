<?php

namespace FWK\ViewHelpers\Product\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use SDK\Dtos\Catalog\Product\Product;
use FWK\Core\Exceptions\CommerceException;

/**
 * This is the ButtonWishlist class, a macro class for the productViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the product's wishlist button.
 *
 * @see ButtonWishlist::getViewParameters()
 *
 * @package FWK\ViewHelpers\Product\Macro
 */
class ButtonWishlist {

    public ?Product $product = null;

    public bool $showLabel = true;

    public bool $allowDelete = true;

    public string $class = '';

    /**
     * Constructor method for ButtonWishlist class.
     * 
     * @see ButtonWishlist
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
        return $this->getProperties();
    }

    protected function getProperties(): array {
        return [
            'product' => $this->product,
            'showLabel' => $this->showLabel,
            'allowDelete' => $this->allowDelete,
            'class' => $this->class
        ];
    }
}