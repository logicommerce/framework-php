<?php

namespace FWK\ViewHelpers\Basket\Macro;

use FWK\Core\Exceptions\CommerceException;
use FWK\Core\ViewHelpers\ViewHelper;
use SDK\Dtos\PaymentValidationResponse;

/**
 * This is the AsyncOrder class, a macro class for the basket viewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the basket's form.
 *
 * @see AsyncOrder::getViewParameters()
 *
 * @package FWK\ViewHelpers\Basket\Macro
 */
class AsyncOrder {

    public ?PaymentValidationResponse $validationResponse = null;

    public array $postParameters = [];

    public array $getParameters = [];

    /**
     * Constructor method for AsyncOrder class.
     * 
     * @see AsyncOrder
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
        if (is_null($this->validationResponse)) {
            throw new CommerceException("The value of [validationResponse] argument: '" . $this->validationResponse . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }
        
        return $this->getProperties();
    }
    
    /**
     * Return macro use properties
     *
     * @return array
     */
    private function getProperties(): array {
        return [
            'validationResponse' => $this->validationResponse,
            'postParameters' => $this->postParameters,
            'getParameters' => $this->getParameters
        ];
    }
}