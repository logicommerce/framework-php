<?php

namespace FWK\ViewHelpers\Product\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use SDK\Dtos\Catalog\Product\Product;
use FWK\ViewHelpers\Product\ProductJsonData;

/**
 * This is the BuyProductForm class, a macro class for the productViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the product's buy form.
 *
 * @see BuyProductForm::getViewParameters()
 *
 * @package FWK\ViewHelpers\Product\Macro
 */
class BuyProductForm {

    public ?Product $product = null;

    public string $class = '';

    public string $style = '';

    public string $content = '';

    public int $sectionId = 0;

    public bool $printParentHash = false;

    public int $discountSelectableGiftId = 0;

    public int $shoppingListRowId = 0;

    /**
     * Constructor method for BuyProductForm class.
     * 
     * @see BuyProductForm
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
        if (is_null($this->product)) {
            throw new CommerceException("The value of [product] argument: '" . $this->product . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
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
            'product' => $this->product,
            'class' => $this->class,
            'style' => $this->style,
            'content' => $this->content,
            'sectionId' => $this->sectionId,
            'shoppingListRowId' => $this->shoppingListRowId,
            'printParentHash' => $this->printParentHash,
            'discountSelectableGiftId' => $this->discountSelectableGiftId
        ];
    }
}
