<?php

namespace FWK\ViewHelpers\Basket\Macro;

use FWK\Core\Exceptions\CommerceException;
use FWK\Core\ViewHelpers\ViewHelper;
use SDK\Dtos\PayResponse;

/**
 * This is the EndOrder class, a macro class for the basket viewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the basket's form.
 *
 * @see EndOrder::getViewParameters()
 *
 * @package FWK\ViewHelpers\Basket\Macro
 */
class EndOrder {

    public ?PayResponse $payResponse = null;

    /**
     * Constructor method for EndOrder class.
     * 
     * @see EndOrder
     * 
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        ViewHelper::mergeArguments($this, $arguments);
    }
    
    /**
     * This method returns all calculated arguments and new parameters for BasketViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        if (is_null($this->payResponse)) {
            throw new CommerceException("The value of [payResponse] argument: '" . $this->payResponse . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
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
            'payResponse' => $this->payResponse
        ];
    }
}