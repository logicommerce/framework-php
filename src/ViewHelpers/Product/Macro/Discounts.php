<?php

namespace FWK\ViewHelpers\Product\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use SDK\Core\Dtos\ElementCollection;

/**
 * This is the Discounts class, a macro class for the productViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the product's recommend button.
 *
 * @see Discounts::getViewParameters()
 *
 * @package FWK\ViewHelpers\Product\Macro
 */
class Discounts {

    public int $productId = 0;

    public ?ElementCollection $discounts = null;

    public bool $showActivityLimit = false;

    public bool $showSmallImage = false;

    public bool $showLargeImage = false;

    public bool $showName = false;

    public bool $showShortDescription = false;

    public bool $showLongDescription = false;

    /**
     * Constructor method for Discounts class.
     * 
     * @see ElementCollection
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
        if (is_null($this->discounts)) {
            throw new CommerceException("The value of [discounts] argument: '" . $this->discounts . "' is required " . self::class, CommerceException::VIEW_HELPER_UNDEFINED_ARGUMENT);
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
            'discounts' => $this->discounts,
            'productId' => $this->productId,
            'showActivityLimit' => $this->showActivityLimit,
            'showSmallImage' => $this->showSmallImage,
            'showLargeImage' => $this->showLargeImage,
            'showName' => $this->showName,
            'showShortDescription' => $this->showShortDescription,
            'showLongDescription' => $this->showLongDescription,
        ];
    }
}
