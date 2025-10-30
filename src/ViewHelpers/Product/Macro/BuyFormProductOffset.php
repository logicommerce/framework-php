<?php

namespace FWK\ViewHelpers\Product\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;

/**
 * This is the BuyFormProductOffset class, a macro class for the productViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the product's buy form products offset.
 *
 * @see BuyFormProductOffset::getViewParameters()
 *
 * @package FWK\ViewHelpers\Product\Macro
 */
class BuyFormProductOffset {

    public string $class = '';

    public ?bool $showOrderBox = null;

    /**
     * Constructor method for BuyFormProductOffset class.
     * 
     * @see BuyFormProductOffset
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
        if (is_null($this->showOrderBox)) {
            throw new CommerceException("The value of [showOrderBox] argument: '" . $this->showOrderBox . "' is required! (showOrderBox: product.definition.showOrderBox) " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
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
            'class' => $this->class,
            'showOrderBox' => $this->showOrderBox
        ];
    }
}