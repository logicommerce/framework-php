<?php

namespace FWK\ViewHelpers\Basket\Macro;

use SDK\Dtos\Basket\Basket;
use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;

/**
 * This is the Comment class, a macro class for the basket viewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the basket's comment.
 *
 * @see Comment::getViewParameters()
 *
 * @package FWK\ViewHelpers\Basket\Macro
 */
class Comment {

    public ?Basket $basket = null;

    public bool $forceOutput = false;
    
    public bool $showPlaceholder = true;

    private int $basketItems = 0;
    
    private string $commentValue = '';

    /**
     * Constructor method for Comment class.
     * 
     * @see Comment
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
        if (is_null($this->basket)) {
            throw new CommerceException("The value of [basket] argument: '" . $this->basket . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }
        
        $this->basketItems = count($this->basket->getItems());
        $this->commentValue = htmlspecialchars($this->basket->getComment(), ENT_QUOTES, CHARSET);
        
        return $this->getProperties();
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        return [
            'forceOutput' => $this->forceOutput,
            'showPlaceholder' => $this->showPlaceholder,
            'basketItems' => $this->basketItems,
            'commentValue' => $this->commentValue
        ];
    }
}