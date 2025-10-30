<?php

namespace FWK\Core\ViewHelpers\Macros\Basket\Macro;

use FWK\Dtos\Basket\Basket as FWKBasket;
use SDK\Dtos\Basket\Basket;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Resources\Language;
use FWK\Core\ViewHelpers\ViewHelper;
use SDK\Dtos\Documents\Document;

/**
 * This is the Base Output class, a macro class for the basket viewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the baskets output.
 *
 * @package FWK\ViewHelpers\Basket\Macro
 */
abstract class BaseOutput {

    // macro arguments constants
    public const MODE_DISCLOSURE = 'disclosure';

    public const MODE_CASH_TICKET = 'cashTicket';

    protected const MODE_VALUES = [
        self::MODE_DISCLOSURE,
        self::MODE_CASH_TICKET
    ];

    public const SHOW_OPTION_VALUES_NAME_NOT_SHOW = 'notShow';

    public const SHOW_OPTION_VALUES_NAME_SHOW_NAME = 'showName';

    public const SHOW_OPTION_VALUES_NAME_SHOW_DESCRIPTION = 'showDescription';

    protected const SHOW_OPTION_VALUES_NAME_VALUES = [
        self::SHOW_OPTION_VALUES_NAME_NOT_SHOW,
        self::SHOW_OPTION_VALUES_NAME_SHOW_NAME,
        self::SHOW_OPTION_VALUES_NAME_SHOW_DESCRIPTION
    ];

    public const GIFTS_PRESENTATION_PRODUCTS_BLOCK = 'productsBlock';

    public const GIFTS_PRESENTATION_INDEPENDENT = 'independent';

    public const ADDITIONAL_ITEMS_PRESENTATION = 'additionalItems';

    protected const GIFTS_PRESENTATION_VALUES = [
        self::GIFTS_PRESENTATION_PRODUCTS_BLOCK,
        self::GIFTS_PRESENTATION_INDEPENDENT
    ];

    // Other constants
    protected const TABLE_COLUMNS = [
        'info',
        'price',
        'quantity',
        'discounts',
        'subtotal'
    ];

    // macro arguments
    public string $additionalItems = self::ADDITIONAL_ITEMS_PRESENTATION;

    public string $mode = self::MODE_DISCLOSURE;

    public bool $linkable = true;

    public bool $showOptions = true;

    public string $showOptionValuesName = self::SHOW_OPTION_VALUES_NAME_SHOW_NAME;

    public bool $showOptionValuesSku = false;

    public bool $showZeroDiscount = false;

    public string $giftsPresentation = self::GIFTS_PRESENTATION_PRODUCTS_BLOCK;

    public bool $showTaxDisclosure = true;

    public ?bool $showTaxIncluded = null;

    public ?bool $showTaxName = true;

    public string $tableClass = '';

    public bool $showPrices = true;

    public bool $showImage = true;

    public bool $showSku = false;

    public bool $showManufacturerSku = false;

    public bool $showCustomTags = false;

    public bool $showDevolutionButton = false;

    public bool $showDiscounts = true;

    public bool $showDiscountName = false;

    public bool $showZeroShipping = true;

    public bool $showZeroPayment = true;

    public bool $showFreeTaxMessage = false;

    public bool $showPreviousPrice = false;

    public bool $showPercentDifference = false;

    public bool $showPriceDifference = false;

    public bool $showProductStockId = false;

    public bool $showFooter = true;

    public bool $showBrand = false;

    public ?string $productsTemplate = null;

    public ?string $productsBundleTemplate = null;

    public ?string $productsBundleItemTemplate = null;

    public ?string $productsGiftTemplate = null;

    public ?string $productSelectableGiftTemplate = null;

    public string $routeType = '';

    public float $totalProductDiscounts = 0;

    public bool $showTaxesZeroTotalIncrement = false;

    // calculated properties
    protected array $footerRows = [];

    protected array $disclosure = [];

    protected ?Language $languageSheet = null;

    protected int $tableColumns = 0;

    /**
     * Constructor method for Output class.
     *
     * @see Output
     *
     * @param array $arguments
     * @param Language $languageSheet
     */
    public function __construct(array $arguments, Language $languageSheet) {
        $this->showTaxIncluded = ViewHelper::getApplicationTaxesIncluded();
        ViewHelper::mergeArguments($this, $arguments);
        $this->languageSheet = $languageSheet;
    }

    /**
     * This method check that the main params stored in this parent class are OK
     *
     * @return array
     */
    protected function validateMainParams(): void {
        $messageTemplate = 'The value of [$key] argument: "$value" not exists in ' . self::class;
        if (!in_array($this->mode, self::MODE_VALUES, true)) {
            throw new CommerceException(strtr($messageTemplate, ['$key' => 'mode', '$value' => $this->mode]), CommerceException::VIEW_HELPER_UNDEFINED_ARGUMENT);
        } elseif (!in_array($this->showOptionValuesName, self::SHOW_OPTION_VALUES_NAME_VALUES, true)) {
            throw new CommerceException(strtr($messageTemplate, ['$key' => 'showOptionValuesName', '$value' => $this->showOptionValuesName]), CommerceException::VIEW_HELPER_UNDEFINED_ARGUMENT);
        } elseif (!in_array($this->giftsPresentation, self::GIFTS_PRESENTATION_VALUES, true)) {
            throw new CommerceException(strtr($messageTemplate, ['$key' => 'giftsPresentation', '$value' => $this->giftsPresentation]), CommerceException::VIEW_HELPER_UNDEFINED_ARGUMENT);
        }
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        return [
            'additionalItems' => $this->additionalItems,
            'mode' => $this->mode,
            'linkable' => $this->linkable,
            'showOptions' => $this->showOptions,
            'showOptionValuesName' => $this->showOptionValuesName,
            'showOptionValuesSku' => $this->showOptionValuesSku,
            'showZeroDiscount' => $this->showZeroDiscount,
            'giftsPresentation' => $this->giftsPresentation,
            'showTaxDisclosure' => $this->showTaxDisclosure,
            'showTaxIncluded' => $this->showTaxIncluded,
            'showTaxName' => $this->showTaxName,
            'tableClass' => $this->tableClass,
            'showPrices' => $this->showPrices,
            'showImage' => $this->showImage,
            'showSku' => $this->showSku,
            'showManufacturerSku' => $this->showManufacturerSku,
            'showCustomTags' => $this->showCustomTags,
            'showDiscounts' => $this->showDiscounts,
            'showDevolutionButton' => $this->showDevolutionButton,
            'showDiscountName' => $this->showDiscountName,
            'showZeroShipping' => $this->showZeroShipping,
            'showZeroPayment' => $this->showZeroPayment,
            'showFreeTaxMessage' => $this->showFreeTaxMessage,
            'showPreviousPrice' => $this->showPreviousPrice,
            'showPercentDifference' => $this->showPercentDifference,
            'showPriceDifference' => $this->showPriceDifference,
            'showProductStockId' => $this->showProductStockId,
            'showFooter' => $this->showFooter,
            'showBrand' => $this->showBrand,
            'productsTemplate' => $this->productsTemplate,
            'productsBundleTemplate' => $this->productsBundleTemplate,
            'productsBundleItemTemplate' => $this->productsBundleItemTemplate,
            'productsGiftTemplate' => $this->productsGiftTemplate,
            'productSelectableGiftTemplate' => $this->productSelectableGiftTemplate,
            'footerRows' => $this->footerRows,
            'disclosure' => $this->disclosure,
            'routeType' => $this->routeType,
            'totalProductDiscounts' => $this->totalProductDiscounts,
            'showTaxesZeroTotalIncrement' => $this->showTaxesZeroTotalIncrement,
        ];
    }

    private function checkIsBasketOrDocument($structure): void {
        if (!($structure instanceof Basket || $structure instanceof Document)) {
            throw new CommerceException('Invalid type for structure. Expected Document or Basket', CommerceException::VIEW_HELPER_INVALID_ARGUMENT);
        }
    }

    protected function setCalculatedAttributes(&$structure): void {
        $this->checkIsBasketOrDocument($structure);
        if ($structure instanceof Basket) {
            $structure = FWKBasket::fillFromParent($structure);
        }

        $this->modeOverride($structure);
        $this->parametersRestrictions();

        $this->setTotalProductDiscounts();

        foreach ($structure->getItems() as $row) {
            $row->setClassName($this->getRowClassName($row));
        }

        $this->tableColumns = $this->getTableBodyColumns();

        $this->setFooter();
        $this->setDisclosure();
    }

    abstract protected function setFooter(): void;

    abstract protected function setDisclosure(): void;

    /**
     * Get basketRow classes for <tr> html
     *
     * @param $basketRow
     *
     * @return string
     */
    abstract protected function getRowClassName(&$row): string;

    /**
     * Sets the sum of the total discounts of all rows
     * 
     */
    abstract protected function setTotalProductDiscounts(): void;

    /**
     * Depending parameters override basket mode
     *
     * @return void
     */
    private function modeOverride($structure): void {
        if (empty($structure->getTaxes())) {
            $this->mode = self::MODE_CASH_TICKET;
        }
    }

    /**
     * Depending mode parameter override other parameters
     *
     * @return void
     */
    protected function parametersRestrictions(): void {
        if ($this->mode === self::MODE_CASH_TICKET) {
            if ($this->giftsPresentation !== self::GIFTS_PRESENTATION_PRODUCTS_BLOCK) {
                $this->giftsPresentation = self::GIFTS_PRESENTATION_PRODUCTS_BLOCK;
            }
            $this->showTaxIncluded = true;
            $this->showTaxDisclosure = false;
        } elseif ($this->mode === self::MODE_DISCLOSURE) {
            $this->showTaxIncluded = false;
        }
    }

    /**
     * Based on the maximum possible columns and the parameters,
     * calculate the maximum columns of the table's body.
     *
     * @return int
     */
    public function getTableBodyColumns(): int {
        $columns = count(self::TABLE_COLUMNS);
        if (!$this->showPrices) {
            $columns -= 2;
        }
        if (!($this->showDiscounts && $this->totalProductDiscounts > 0) && !($this->showZeroDiscount && $this->totalProductDiscounts <= 0)) {
            $columns--;
        }
        return $columns;
    }

    /**
     * Util for calculate colspan value of a tr depending tbody columns,
     * default for extra table tr (row) with 2 td
     *
     * @param int $tableBodyTdLength
     *            Actual BasketRow total output columns (td)
     * @param int $thisRowTdLength
     *            Total row columns for which we are calculating colspan
     * @param int $tdWithColspanLength
     *            Total row columns with colspan to calculate, for which we are calculating colspan
     *            
     * @return int
     */
    public static function colspanCalculator(int $tableBodyTdLength, int $thisRowTdLength = 2, int $tdWithColspanLength = 1): int {
        return $tableBodyTdLength - ($thisRowTdLength - $tdWithColspanLength);
    }

    /**
     * Round to zero discount price for show discount comparations
     *
     * @param float $value
     * @return boolean
     */
    public static function isZeroDiscountPrice(float $value): bool {
        $round = round($value, 2);
        return $round === (float)0;
    }
}
