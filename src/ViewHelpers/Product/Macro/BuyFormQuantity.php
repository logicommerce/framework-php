<?php

namespace FWK\ViewHelpers\Product\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use SDK\Dtos\Catalog\Product\Product;

/**
 * This is the BuyFormQuantity class, a macro class for the productViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the product's buy form quantity.
 *
 * @see BuyFormQuantity::getViewParameters()
 *
 * @package FWK\ViewHelpers\Product\Macro
 */
class BuyFormQuantity {

    public ?Product $product = null;

    public string $class = '';

    public bool $quantityPlugin = false;

    public bool $showSelectableBox = false;

    public int $selectableBoxRows = 5;

    public bool $forceMinQuantityZero = false;

    public ?int $manualMinQuantity = null;

    public ?int $manualMaxQuantity = null; // real max int 32: 2147483647

    public string $id = '';

    public ?bool $showOrderBox = null;

    private bool $groupQuantityByOptions = false;

    private int $minOrderQuantity = 0;

    private int $maxOrderQuantity = 0;

    private int $multipleOrderQuantity = 0;

    private int $multipleActsOver = 0;

    private int $minQuantity = 1;

    private int $maxQuantity = 100000;

    private function setQuantityLogic(): void {
        if ($this->manualMinQuantity != null) {
            $this->minQuantity = $this->manualMinQuantity;
        }
        if ($this->manualMaxQuantity != null) {
            $this->maxQuantity = $this->manualMaxQuantity;
        }
        if (!$this->groupQuantityByOptions && $this->minOrderQuantity > 0) {
            $this->minQuantity = $this->minOrderQuantity;
        }
        if (!$this->groupQuantityByOptions && $this->maxOrderQuantity > 0) {
            $this->maxQuantity = $this->maxOrderQuantity;
        }

        if ($this->multipleOrderQuantity > 0) {
            if ($this->multipleActsOver > 0) {
                if ($this->minQuantity >= $this->multipleActsOver && $this->minQuantity % $this->multipleOrderQuantity !== 0) {
                    $difference = $this->minQuantity % $this->multipleOrderQuantity;
                    $this->minQuantity = $this->minQuantity + ($this->multipleOrderQuantity - $difference);
                }
            } else {
                if ($this->minQuantity < $this->multipleOrderQuantity) {
                    $this->minQuantity = $this->multipleOrderQuantity;
                } else {
                    if ($this->minQuantity % $this->multipleOrderQuantity !== 0) {
                        $difference = $this->minQuantity % $this->multipleOrderQuantity;
                        $this->minQuantity = $this->minQuantity + ($this->multipleOrderQuantity - $difference);
                    }
                }
            }
        }
    }

    /**
     * Constructor method for BuyFormQuantity class.
     * 
     * @see BuyFormQuantity
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

        $this->groupQuantityByOptions = $this->product->getDefinition()->getGroupQuantityByOptions();
        $this->minOrderQuantity = $this->product->getDefinition()->getMinOrderQuantity();
        $this->maxOrderQuantity = $this->product->getDefinition()->getMaxOrderQuantity();
        $this->multipleOrderQuantity = $this->product->getDefinition()->getMultipleOrderQuantity();
        $this->multipleActsOver = $this->product->getDefinition()->getMultipleActsOver();

        $this->setQuantityLogic();
        $this->setSelectableBoxRows();

        if (empty($this->id)) $this->id = 'quantity-' . $this->product->getId();

        return $this->getProperties();
    }

    /**
     * Set html select total option rows
     *
     * @return void
     */
    private function setSelectableBoxRows(): void {
        $this->selectableBoxRows = $this->selectableBoxRows * $this->minQuantity;
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
            'quantityPlugin' => $this->quantityPlugin,
            'showSelectableBox' => $this->showSelectableBox,
            'selectableBoxRows' => $this->selectableBoxRows,
            'manualMinQuantity' => $this->manualMinQuantity,
            'manualMaxQuantity' => $this->manualMaxQuantity,
            'minOrderQuantity' => $this->minOrderQuantity,
            'maxOrderQuantity' => $this->maxOrderQuantity,
            'multipleOrderQuantity' => $this->multipleOrderQuantity,
            'multipleActsOver' => $this->multipleActsOver,
            'minQuantity' => $this->minQuantity,
            'maxQuantity' => $this->maxQuantity,
            'forceMinQuantityZero' => $this->forceMinQuantityZero,
            'id' => $this->id,
            'showOrderBox' => $this->showOrderBox,
        ];
    }
}
