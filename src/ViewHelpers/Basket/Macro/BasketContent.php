<?php

namespace FWK\ViewHelpers\Basket\Macro;

use SDK\Dtos\Basket\Basket;
use SDK\Dtos\Basket\BasketRow;
use SDK\Enums\BasketRowType;
use SDK\Enums\BasketWarningCode;
use SDK\Enums\BasketWarningSeverity;
use SDK\Enums\RouteType;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Resources\Session;
use FWK\ViewHelpers\Basket\BasketViewHelper;
use FWK\ViewHelpers\Basket\Macro\Output\Disclosure;
use FWK\ViewHelpers\Basket\Macro\Output\Footer;
use FWK\Core\ViewHelpers\Macros\Basket\Macro\BaseOutput;
use FWK\Dtos\Basket\Basket as FWKBasket;
use SDK\Enums\DiscountType;

/**
 * This is the BasketContent class, a macro class for the basket viewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the baskets output.
 *
 * @see BasketContent::getViewParameters()
 *
 * @package FWK\ViewHelpers\Basket\Macro
 */
class BasketContent extends BaseOutput {

    // macro arguments constants
    public const WARNING_POSITION_HEADER = 'header';

    public const WARNING_POSITION_LINE = 'line';

    protected const WARNING_POSITION_VALUES = [
        self::WARNING_POSITION_HEADER,
        self::WARNING_POSITION_LINE,
    ];

    public const SHOW_DISCOUNT_VALUE_VALUE = 'value';

    public const SHOW_DISCOUNT_VALUE_PERCENTAGE = 'percentage';

    public const SHOW_DISCOUNT_VALUE_BOTH = 'both';

    public const SHOW_DISCOUNT_VALUE_NONE = 'none';

    public const SHOW_DISCOUNT_VALUE_VALUES = [
        self::SHOW_DISCOUNT_VALUE_VALUE,
        self::SHOW_DISCOUNT_VALUE_PERCENTAGE,
        self::SHOW_DISCOUNT_VALUE_BOTH,
        self::SHOW_DISCOUNT_VALUE_NONE,
    ];

    // macro arguments
    public ?Basket $basket = null;

    public bool $editable = false;

    public bool $saveForLater = false;

    public bool $editableGifts = false;

    public bool $quantityPlugin = false;

    public bool $showShippingSection = false;

    public bool $showSelectableBox = false;

    public int $selectableBoxRows = 5;

    public bool $showWarnings = true;

    public bool $showWarningsBlock = true;

    public array $hiddenWarningCodes = [];

    public string $errorWarningPosition = self::WARNING_POSITION_HEADER;

    public string $warningWarningPosition = self::WARNING_POSITION_HEADER;

    public string $infoWarningPosition = self::WARNING_POSITION_HEADER;

    private array $warningsBlockItems = [];

    public array $showCustomTagPositions = [];

    public string $showDiscountValue = self::SHOW_DISCOUNT_VALUE_BOTH;

    public bool $showAsGridUniqueDimension = false;

    public bool $mergeGridDiscounts = false;

    public bool $showLockedStocks = true;

    public bool $showLockedStocksDescription = false;

    // Other properties
    private bool $empty = false;

    private array $appliedDiscountHashes = [];

    /**
     * This method returns all calculated arguments and new parameters for BasketViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        if (is_null($this->basket)) {
            throw new CommerceException('The value of [basket] argument: "' . $this->basket . '" is required in ' . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        } else {
            $this->validateMainParams();
            $messageTemplate = 'The value of [$key] argument: "$value" not exists in ' . self::class;
            if (!in_array($this->errorWarningPosition, self::WARNING_POSITION_VALUES, true)) {
                throw new CommerceException(strtr($messageTemplate, ['$key' => 'errorWarningPosition', '$value' => $this->errorWarningPosition]), CommerceException::VIEW_HELPER_UNDEFINED_ARGUMENT);
            } elseif (!in_array($this->warningWarningPosition, self::WARNING_POSITION_VALUES, true)) {
                throw new CommerceException(strtr($messageTemplate, ['$key' => 'warningWarningPosition', '$value' => $this->warningWarningPosition]), CommerceException::VIEW_HELPER_UNDEFINED_ARGUMENT);
            } elseif (!in_array($this->infoWarningPosition, self::WARNING_POSITION_VALUES, true)) {
                throw new CommerceException(strtr($messageTemplate, ['$key' => 'infoWarningPosition', '$value' => $this->infoWarningPosition]), CommerceException::VIEW_HELPER_UNDEFINED_ARGUMENT);
            }
            if (!in_array($this->showDiscountValue, self::SHOW_DISCOUNT_VALUE_VALUES, true)) {
                throw new CommerceException(strtr($messageTemplate, ['$key' => 'showDiscountValue', '$value' => $this->showDiscountValue]), CommerceException::VIEW_HELPER_UNDEFINED_ARGUMENT);
            }
        }
        $this->setCalculatedAttributes($this->basket);

        $this->setEmpty();
        $this->warningsBlock();

        $this->basket = FWKBasket::fillFromParent($this->basket);
        foreach ($this->basket->getItems() as $basketRow) {
            $basketRow->setWarningSeverity($this->getRowWarningSeverity($basketRow));
            $basketRow->setOffer($this->getBasketRowOffer($basketRow));
            $basketRow->setQuantityOutput($this->getQuantityOutput($basketRow));
            $basketRow->setLockedStockTimer($basketRow->getHash(), $this->basket->getLockedStockTimers());
        }

        if ($this->saveForLater && Session::getInstance()->getUser()->getId() === 0) {
            $this->saveForLater = false;
        }

        foreach ($this->basket->getAppliedDiscounts() as $appliedDiscount) {
            if ($appliedDiscount->getType() == DiscountType::SELECTABLE_GIFT) {
                foreach ($appliedDiscount->getBasketGiftIdList() as $basketGift) {
                    $this->appliedDiscountHashes[] = $basketGift;
                }
            }
        }

        return $this->getProperties();
    }

    protected function setFooter(): void {
        $footer = new Footer($this, $this->languageSheet);
        $this->footerRows = $footer->getRows();
    }

    protected function setDisclosure(): void {
        $disclosure = new Disclosure($this);
        $this->disclosure = $disclosure->getData();
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {        
        return parent::getProperties() + [
            'basket' => $this->basket,
            'editable' => $this->editable,
            'saveForLater' => $this->saveForLater,
            'editableGifts' => $this->editableGifts,
            'empty' => $this->empty,
            'errorWarningPosition' => $this->errorWarningPosition,
            'infoWarningPosition' => $this->infoWarningPosition,
            'quantityPlugin' => $this->quantityPlugin,
            'selectableBoxRows' => $this->selectableBoxRows,
            'showCustomTagPositions' => $this->showCustomTagPositions,
            'showDiscountValue' => $this->showDiscountValue,
            'showSelectableBox' => $this->showSelectableBox,
            'showShippingSection' => $this->showShippingSection,
            'showWarnings' => $this->showWarnings,
            'showWarningsBlock' => $this->showWarningsBlock,
            'warningsBlockItems' => $this->warningsBlockItems,
            'warningWarningPosition' => $this->warningWarningPosition,
            'showAsGridUniqueDimension' => $this->showAsGridUniqueDimension,
            'appliedDiscountHashes' => $this->appliedDiscountHashes,
            'mergeGridDiscounts' => $this->mergeGridDiscounts,
            'showLockedStocks' => $this->showLockedStocks,
            'showLockedStocksDescription' => $this->showLockedStocksDescription
        ];
    }

    /**
     * Return true if basket items are empty
     *
     * @return void
     */
    private function setEmpty(): void {
        $this->empty = empty($this->basket->getItems());
    }

    /**
     * Depending mode parameter override other parameters
     *
     * @return void
     */
    protected function parametersRestrictions(): void {
        parent::parametersRestrictions();
        if (!$this->showWarningsBlock) {
            $this->errorWarningPosition = self::WARNING_POSITION_LINE;
            $this->warningWarningPosition = self::WARNING_POSITION_LINE;
            $this->infoWarningPosition = self::WARNING_POSITION_LINE;
        }
    }

    /**
     * According tag arguments calculate the html quantity output of the basket row
     *
     * @param BasketRow $basketRow
     *
     * @return string
     */
    private function getQuantityOutput(BasketRow $basketRow): string {
        $quantity = $basketRow->getQuantity();
        $nameAttr = 'quantity' . $basketRow->getHash();
        $output = '<span class="basketTextQuantity">' . $quantity . '</span>';

        $basketRowType = $basketRow->getType();
        if ($this->editable && ($basketRowType === BasketRowType::PRODUCT || $basketRowType === BasketRowType::BUNDLE || $basketRowType === BasketRowType::LINKED || $basketRowType === BasketRowType::VOUCHER_PURCHASE )) {
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
     * 
     */
    protected function getRowClassName(&$basketRow): string {
        $hasWarnings = false;
        $className = 'basket';
        if ($this->showWarnings && $hasWarnings /* && $isReturn === false*/) {
            $className .= ' invalidProductBasket';
        }
        if ($basketRow->getType() === BasketRowType::PRODUCT) {
            $className .= ' basketProduct';
            $warnings = $basketRow->getBasketWarnings();
            if (!is_null($warnings) && sizeof($warnings) > 0) {
                $hasWarnings = true;
            }
        } elseif ($basketRow->getType() === BasketRowType::BUNDLE) {
            $className .= ' basketBundleProduct';
            foreach ($basketRow->getItems() as $item) {
                $warnings = $item->getBasketWarnings();
                if (!is_null($warnings) && sizeof($warnings) > 0) {
                    $hasWarnings = true;
                }
            }
        } elseif ($basketRow->getType() === BasketRowType::LINKED) {
            $className .= ' basketLinked';
            $warnings = $basketRow->getBasketWarnings();
            if (!is_null($warnings) && sizeof($warnings) > 0) {
                $hasWarnings = true;
            }
            foreach ($this->basket->getItems() as $item) {
                if ($basketRow->getParentId() === $item->getId()) {
                    $basketRow->setParentItem($item);
                    break;
                }
            }
        } elseif ($basketRow->getType() === BasketRowType::GIFT) {
            $className .= ' basketGift';
        }
        return $className;
    }

    /**
     * Return basketRow offer
     *
     * @param BasketRow $basketRow
     *
     * @return bool
     */
    private function getBasketRowOffer(BasketRow $basketRow): bool {
        $basketRowPrices = $basketRow->getPrices();
        if (!is_null($basketRowPrices) && $basketRowPrices->getUnitPreviousPrice() > $basketRowPrices->getUnitPrice()) {
            return true;
        }
        return false;
    }

    /**
     * 
     */
    protected function setTotalProductDiscounts(): void {
        $totalBasketRowTotalDiscounts = 0;
        foreach ($this->basket->getItems() as $basketRow) {
            $prices = $basketRow->getPrices();
            if ($this->showTaxIncluded === true) {
                $prices = $basketRow->getPricesWithTaxes();
            }
            if (!is_null($prices)) {
                $totalBasketRowTotalDiscounts += $prices->getTotalDiscounts();
            }
        }
        $this->totalProductDiscounts = $totalBasketRowTotalDiscounts;
    }

    /**
     * Search and return the highest warnings severity of the basketRow
     *
     * @param BasketRow $row
     *
     * @return string
     */
    private function getRowWarningSeverity(BasketRow $row): string {
        $severity = '';
        $warnings = [];
        if ($row->getType() === BasketRowType::PRODUCT or $row->getType() === BasketRowType::LINKED) {
            $warnings = $row->getBasketWarnings();
            foreach ($this->basket->getBasketWarnings() as $basketWarning) {
                foreach ($basketWarning->getHashes() as $hash) {
                    if ($row->getHash() === $hash) {
                        $warnings[] = $basketWarning;
                    }
                }
            }
        } elseif ($row->getType() === BasketRowType::BUNDLE) {
            foreach ($row->getItems() as $item) {
                foreach ($item->getBasketWarnings() as $basketWarning) {
                    foreach ($basketWarning->getHashes() as $hash) {
                        if ($item->getHash() === $hash) {
                            $warnings[] = $basketWarning;
                        }
                    }
                }
            }
        }
        foreach ($warnings as $warning) {
            $severity = $warning->getSeverity();

            if ($severity === BasketWarningSeverity::ERROR) {
                break;
            } elseif ($severity === BasketWarningSeverity::WARNING) {
                break;
            } elseif ($severity === BasketWarningSeverity::INFO) {
                break;
            }
        }

        return $severity;
    }

    /**
     * Get basket warnings, classify by types and assign a message
     *
     * @return void
     */
    private function warningsBlock(): void {
        $basketWarnings = [];
        $basketItemWarnings = [];
        $messageHashes = [];

        if ($this->errorWarningPosition !== self::WARNING_POSITION_HEADER || $this->warningWarningPosition !== self::WARNING_POSITION_HEADER || $this->infoWarningPosition !== self::WARNING_POSITION_HEADER && count($basketWarnings) > 0) {
            $basketWarnings = $this->basket->getBasketWarnings();
        } else {
            $basketItemWarnings = $this->basket->getItemsBasketWarnings();
            $basketWarnings = $this->basket->getBasketWarnings();
        }

        foreach ($basketItemWarnings as $key => $item) {
            if (in_array($item->getCode(), $this->hiddenWarningCodes, true)) {
                unset($basketWarnings[$key]);
            } else {
                $basketItemWarnings[$key] = BasketViewHelper::setBasketWarningMessage($item, $this->basket, 'WARNING_', true);
                $messageHashes[] = $basketItemWarnings[$key]->getMessageHash();
            }
        }

        foreach ($basketWarnings as $key => $item) {
            $basketWarnings[$key] = BasketViewHelper::setBasketWarningMessage($item, $this->basket);
            // Avoid show NEEDS_PAYMENTSYSTEM, NEEDS_DELIVERY, INVALID_BILLING_ADDRESS, INVALID_SHIPPING_ADDRESS warnings into first step of 3 steps checkout
            if (($basketWarnings[$key]->getCode() === BasketWarningCode::NEEDS_PAYMENTSYSTEM ||
                $basketWarnings[$key]->getCode() === BasketWarningCode::NEEDS_DELIVERY ||
                $basketWarnings[$key]->getCode() === BasketWarningCode::INVALID_BILLING_ADDRESS ||
                $basketWarnings[$key]->getCode() === BasketWarningCode::INVALID_SHIPPING_ADDRESS
            ) && $this->routeType === RouteType::CHECKOUT_BASKET) {
                unset($basketWarnings[$key]);
            } else if (in_array($basketWarnings[$key]->getCode(), $this->hiddenWarningCodes, true)) {
                unset($basketWarnings[$key]);
            } else if (in_array($basketWarnings[$key]->getMessageHash(), $messageHashes, true)) {
                unset($basketWarnings[$key]);
            } else {
                $messageHashes[] = $basketWarnings[$key]->getMessageHash();
            }
        }
        $allWarnings = array_merge($basketWarnings, $basketItemWarnings);
        $this->warningsBlockItems = BasketViewHelper::groupBasketWarnings($allWarnings);
    }
}
