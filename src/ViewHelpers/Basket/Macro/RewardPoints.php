<?php

namespace FWK\ViewHelpers\Basket\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use SDK\Dtos\Basket\Basket;

/**
 * This is the RewardPoints class, a macro class for the basketViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the basket reward points.
 *
 * @see RewardPoints::getViewParameters()
 *
 * @package FWK\ViewHelpers\User\Macro
 */
class RewardPoints {

    public ?Basket $basket = null;

    public string $class = '';

    public bool $showHeader = true;

    /**
     * Constructor method for RewardPoints.
     * 
     * @see RewardPoints
     * 
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        ViewHelper::mergeArguments($this, $arguments);
    }

    /**
     * This method returns all calculated arguments and new parameters for UserViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {

        if (is_null($this->basket)) {
            throw new CommerceException("The value of [basket] argument: '" . $this->basket . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
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
            'class' => $this->class,
            'showHeader' => $this->showHeader,
        ];
    }
}
