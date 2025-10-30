<?php

namespace FWK\ViewHelpers\Product\Macro;

use FWK\Core\Exceptions\CommerceException;
use FWK\Core\ViewHelpers\ViewHelper;
use SDK\Core\Dtos\ElementCollection;

/**
 * This is the ButtonDiscounts class, a macro class for the productViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the product's wishlist button.
 *
 * @see ButtonDiscounts::getViewParameters()
 *
 * @package FWK\ViewHelpers\Product\Macro
 */
class ButtonDiscounts {

    public ?ElementCollection $discounts = null;

    public ?string $class = null;

    /**
     * Constructor method for ButtonDiscounts class.
     * 
     * @see ButtonDiscounts
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
        if (!is_null($this->discounts) && !($this->discounts instanceof ElementCollection)) {
            throw new CommerceException('The value of discounts argument must be a instance of ' . ElementCollection::class . '. ' . ' Instance of ' . get_class($this->discounts) . ' sended.', CommerceException::VIEW_HELPER_INVALID_ARGUMENT);
        }
        return $this->getProperties();
    }

    protected function getProperties(): array {
        return [
            'discounts' => $this->discounts,
            'class' => $this->class
        ];
    }
}
