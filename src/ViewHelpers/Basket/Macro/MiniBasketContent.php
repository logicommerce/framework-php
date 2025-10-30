<?php

namespace FWK\ViewHelpers\Basket\Macro;

use FWK\Dtos\Basket\Basket as FWKBasket;
use SDK\Dtos\Basket\Basket;
use SDK\Dtos\Basket\BasketRow;
use SDK\Enums\BasketRowType;
use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Resources\Session;
use SDK\Dtos\Basket\BasketRows\Product;
use FWK\Core\Theme\Theme;

/**
 * This is the MiniBasketContent class, a macro class for the basket viewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the mini basket output.
 *
 * @see MiniBasketContent::getViewParameters()
 *
 * @package FWK\ViewHelpers\Basket\Macro
 */
class MiniBasketContent {

    public const SHOW_OPTION_VALUES_NAME_NOT_SHOW = 'notShow';

    public const SHOW_OPTION_VALUES_NAME_SHOW_NAME = 'showName';

    public const SHOW_OPTION_VALUES_NAME_SHOW_DESCRIPTION = 'showDescription';

    private const SHOW_OPTION_VALUES_NAME_VALUES = [
        self::SHOW_OPTION_VALUES_NAME_NOT_SHOW,
        self::SHOW_OPTION_VALUES_NAME_SHOW_NAME,
        self::SHOW_OPTION_VALUES_NAME_SHOW_DESCRIPTION
    ];

    public ?Basket $basket = null;

    public ?bool $showTaxIncluded = null;

    public bool $showTotal = true;

    public bool $linkable = true;

    public bool $showPaymentSystem = true;

    public bool $showShipping = true;

    public bool $showCustomTags = false;

    public array $showCustomTagPositions = [];

    public bool $showDeleteItem = true;

    public bool $showImage = true;

    public bool $showBrand = false;

    public bool $showSku = false;

    public bool $showOptions = true;

    public bool $showHeader = true;

    public bool $showFooter = true;

    public string $showOptionValuesName = self::SHOW_OPTION_VALUES_NAME_SHOW_NAME;

    public bool $showTotalDiscounts = true;

    public bool $showItemNameDiscounts = false;

    public bool $showZeroDiscount = false;

    public string $showItemValueDiscounts = BasketContent::SHOW_DISCOUNT_VALUE_NONE;

    public bool $showTotalVouchers = true;

    public string $class = '';

    public string $gripOptionsClassPrefix = '';

    public bool $editable = false;

    public bool $quantityPlugin = false;

    public bool $showSelectableBox = false;

    public int $selectableBoxRows = 5;

    public bool $showGifts = false;

    public bool $showLockedStocks = true;

    public bool $showLockedStocksDescription = false;

    /**
     * Constructor method for MiniBasketContent class.
     *
     * @see MiniBasketContent
     *
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        $applicationTaxIncluded = ViewHelper::getApplicationTaxesIncluded();
        $this->showTaxIncluded = $applicationTaxIncluded;
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
        if (!in_array($this->showOptionValuesName, self::SHOW_OPTION_VALUES_NAME_VALUES, true)) {
            throw new CommerceException("The value of [showOptionValuesName] argument: '" . $this->showOptionValuesName . "' not exists in " . self::class, CommerceException::VIEW_HELPER_UNDEFINED_ARGUMENT);
        }
        if (!in_array($this->showItemValueDiscounts, BasketContent::SHOW_DISCOUNT_VALUE_VALUES, true)) {
            throw new CommerceException("The value of [showItemValueDiscounts] argument: '" . $this->showItemValueDiscounts . "' not exists in BaseOutput::SHOW_DISCOUNT_VALUE_VALUES", CommerceException::VIEW_HELPER_UNDEFINED_ARGUMENT);
        }

        $this->setShowTotalDiscounts();
        $this->setShowTotalVouchers();

        $this->basket = FWKBasket::fillFromParent($this->basket);
        foreach ($this->basket->getItems() as $basketRow) {
            $basketRow->setHtmlId($this->getBasketRowHtmlId($basketRow));
            $basketRow->setQuantityOutput($this->getQuantityOutput($basketRow));
            $basketRow->setLockedStockTimer($basketRow->getHash(), $this->basket->getLockedStockTimers());
        }

        return $this->getProperties();
    }

    /**
     * According tag arguments calculate the html quantity output of the miniBasket item
     *
     * @return string
     */
    private function getQuantityOutput(BasketRow $basketRow): string {
        $session = Session::getInstance();

        $quantity = 0;
        if (
            !Theme::getInstance()->getConfiguration()->getCommerce()->isDisableShowAsGridProductOptions() &&
            $session->getBasketGridProducts()[$basketRow->getId()] &&
            !empty($session->getBasketGridProducts()[$basketRow->getId()]->getGridOptionIds())
        ) {
            foreach ($session->getBasketGridProducts()[$basketRow->getId()]->getRows() as $gridRow) {
                $quantity += $gridRow->totalQuantity;
            }
        } else {
            $quantity = $basketRow->getQuantity();
        }


        $nameAttr = 'quantity' . $basketRow->getHash();
        $output = '<span class="basketTextQuantity">' . $quantity . '</span>';

        $basketRowType = $basketRow->getType();
        if ($this->editable && ($basketRowType === BasketRowType::PRODUCT || $basketRowType === BasketRowType::BUNDLE || $basketRowType === BasketRowType::LINKED || $basketRowType === BasketRowType::VOUCHER_PURCHASE)) {
            if ($this->quantityPlugin) {
                $output = '<input type="text" class="{{className}} basketQuantity validate-integer" name="' . $nameAttr . '" value="' . $quantity . '" data-lc-row-type="' . $basketRowType . '" data-lc-quantity="quantity" min=1 max=99999999>';
            } elseif ($this->showSelectableBox && $this->selectableBoxRows > 0) {
                // El select no tiene en cuenta el min/max/multiple de quantity Deprecated.
                $output = '<select class="{{className}} basketQuantity quantitySelect" name="' . $nameAttr . '" data-lc-row-type="' . $basketRowType . '" >';
                for ($optionIndex = 1; $optionIndex <= $this->selectableBoxRows; $optionIndex += 1) {
                    $selectedAttr = '';
                    if ($quantity === $optionIndex) {
                        $selectedAttr = 'selected="selected"';
                    }
                    $output .= '<option value="' . $optionIndex . '" ' . $selectedAttr . '>' . $optionIndex . '</option>';
                }
                $output .= '</select>';
            } else {
                $output = '<input type="text" class="{{className}} basketQuantity validate-integer" name="' . $nameAttr . '" data-lc-row-type="' . $basketRowType . '" value="' . $quantity . '">';
            }
        }
        return $output;
    }

    /**
     * Set html id attribute to BasketRow, html id is calculated by
     * id + ?combinationId
     *
     * @return string
     */
    private function getBasketRowHtmlId(BasketRow $basketRow): string {
        $htmlId = 'miniBasketItem_' . $basketRow->getId();
        if ($basketRow instanceof Product) {
            $combination = $basketRow->getCombination();
            if (!is_null($combination)) {
                $values = $combination->getValues();
                if (!empty($values)) {
                    foreach ($values as $value) {
                        $id = $value->getProductOptionValueId();
                        if ($id !== 0) {
                            $htmlId .= '_' . $id;
                        }
                    }
                }
            }
        }
        return $htmlId;
    }

    /**
     * Set showTotalDiscounts to false if macro parameter passed is true
     * and basket hasn't discounts
     *
     * @return void
     */
    private function setShowTotalDiscounts(): void {
        $totals = $this->basket->getTotals();
        if (!is_null($totals)) {
            if ($totals->getTotalDiscounts() <= 0) {
                $this->showTotalDiscounts = false;
            }
        }
    }

    /**
     * Set showTotalVouchers to false if macro parameter passed is true
     * and basket hasn't vouchers
     *
     * @return void
     */
    private function setShowTotalVouchers(): void {
        $totals = $this->basket->getTotals();
        if (!is_null($totals)) {
            if ($totals->getTotalVouchers() <= 0) {
                $this->showTotalVouchers = false;
            }
        }
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        return [
            'basket' => $this->basket,
            'linkable' => $this->linkable,
            'showTaxIncluded' => $this->showTaxIncluded,
            'showPaymentSystem' => $this->showPaymentSystem,
            'showShipping' => $this->showShipping,
            'showTotal' => $this->showTotal,
            'showCustomTags' => $this->showCustomTags,
            'showCustomTagPositions' => $this->showCustomTagPositions,
            'showDeleteItem' => $this->showDeleteItem,
            'showImage' => $this->showImage,
            'showBrand' => $this->showBrand,
            'showSku' => $this->showSku,
            'showOptions' => $this->showOptions,
            'showHeader' => $this->showHeader,
            'showFooter' => $this->showFooter,
            'showOptionValuesName' => $this->showOptionValuesName,
            'showTotalDiscounts' => $this->showTotalDiscounts,
            'showItemNameDiscounts' => $this->showItemNameDiscounts,
            'showItemValueDiscounts' => $this->showItemValueDiscounts,
            'showZeroDiscount' => $this->showZeroDiscount,
            'showTotalVouchers' => $this->showTotalVouchers,
            'class' => $this->class,
            'gripOptionsClassPrefix' => $this->gripOptionsClassPrefix,
            'editable' => $this->editable,
            'quantityPlugin' => $this->quantityPlugin,
            'showSelectableBox' => $this->showSelectableBox,
            'showGifts' => $this->showGifts,
            'showLockedStocks' => $this->showLockedStocks,
            'showLockedStocksDescription' => $this->showLockedStocksDescription
        ];
    }
}
