<?php

namespace FWK\ViewHelpers\Basket\Macro;

use SDK\Dtos\Basket\Basket;
use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;

/**
 * This is the MiniBasketWrap class, a macro class for the basket viewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the mini basket.
 *
 * @see MiniBasketWrap::getViewParameters()
 *
 * @package FWK\ViewHelpers\Basket\Macro
 */
class MiniBasketWrap {

    public ?Basket $basket = null;

    public ?string $content = null;

    public ?bool $showTaxIncluded = null;

    public string $class = '';

    /**
     * Constructor method for MiniBasketWrap class.
     * 
     * @see MiniBasketWrap
     * 
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        $this->showTaxIncluded = ViewHelper::getApplicationTaxesIncluded();
        ViewHelper::mergeArguments($this, $arguments);
    }

    /**
     * This method returns all calculated arguments and new parameters for BasketViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        if (is_null($this->content)) {
            throw new CommerceException("The value of [content] argument: '" . $this->content . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
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
            'basket' => $this->basket,
            'content' => $this->content,
            'showTaxIncluded' => $this->showTaxIncluded,
            'class' => $this->class
        ];
    }
}
