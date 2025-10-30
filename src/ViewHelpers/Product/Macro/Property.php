<?php

namespace FWK\ViewHelpers\Product\Macro;

use FWK\Core\Resources\Language;
use FWK\Enums\LanguageLabels;
use SDK\Dtos\Catalog\Product\Product;
use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;

/**
 * This is the Property class, a macro class for the productViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the product's property.
 *
 * @see Property::getViewParameters()
 *
 * @package FWK\ViewHelpers\Product\Macro
 */
class Property {

    public const PROPERTY_PRICE = 'price';

    public const PROPERTY_BASE_PRICE = 'basePrice';

    public const PROPERTY_SAVING = 'saving';

    public const PROPERTY_STOCK = 'stock';

    public const PROPERTY_SKU = 'sku';

    public const PROPERTY_EAN = 'ean';

    public const GRID_TOTAL_UNITS = 'gridTotalUnits';

    private const PROPERTY_VALUES = [
        self::PROPERTY_PRICE,
        self::PROPERTY_BASE_PRICE,
        self::PROPERTY_SAVING,
        self::PROPERTY_STOCK,
        self::PROPERTY_SKU,
        self::PROPERTY_EAN,
        self::GRID_TOTAL_UNITS
    ];

    public ?Product $product = null;

    public string $property = self::PROPERTY_PRICE;

    public bool $showTaxText = false;

    public ?bool $priceWithTaxes = null;

    public bool $stockAlertButton = false;

    public string $stockAlertButtonClass = '';

    public bool $showStock = true;

    public bool $showAvailabilityName = true;

    public bool $showStockText = true;

    public bool $showAvailabilityImage = true;

    private ?bool $applicationTaxes = null;

    private bool $appliedTaxes = false;

    private bool $isAlternativePrice = false;

    private string $taxText = '';

    private ?Language $languageSheet = null;

    /**
     * Constructor method for Property class.
     * 
     * @see Property
     * 
     * @param array $arguments
     * @param Language $languageSheet
     */
    public function __construct(array $arguments, Language $languageSheet) {
        $this->setDefaults();
        ViewHelper::mergeArguments($this, $arguments);
        $this->languageSheet = $languageSheet;
    }

    /**
     * Sets non static default properties
     *
     * @return void
     */
    private function setDefaults(): void {
        $this->priceWithTaxes = ViewHelper::getApplicationTaxesIncluded();
        $this->applicationTaxes = ViewHelper::getApplicationTaxesIncluded();
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
        if (!in_array($this->property, self::PROPERTY_VALUES, true)) {
            throw new CommerceException("The value of [property] argument: '" . $this->property . "' not exists in " . self::class, CommerceException::VIEW_HELPER_UNDEFINED_ARGUMENT);
        }

        $this->setIsAlternativePrice();
        $this->setTaxText();
        $this->setAppliedTaxes();

        return $this->getProperties();
    }

    /**
     * Set property isAlternativePrice
     *
     * @return void
     */
    private function setIsAlternativePrice(): void {
        if ($this->priceWithTaxes !== $this->applicationTaxes) {
            $this->isAlternativePrice = true;
        }
    }

    /**
     * Set property taxText
     *
     * @return void
     */
    private function setTaxText(): void {
        if ($this->showTaxText === true) {
            if ($this->priceWithTaxes === true) {
                $this->taxText = $this->languageSheet->getLabelValue(LanguageLabels::LBL_TAXES_INCLUDED);
            } else {
                $this->taxText = $this->languageSheet->getLabelValue(LanguageLabels::LBL_TAXES_NOT_INCLUDED);
            }
        }
    }

    /**
     * Set property appliedTaxes
     *
     * @return void
     */
    private function setAppliedTaxes(): void {
        $prices = $this->product->getCombinationData()->getPrices()->getPrices();
        $pricesWithTaxes = $this->product->getCombinationData()->getPricesWithTaxes()->getPrices();

        if (!is_null($prices) && !is_null($pricesWithTaxes)) {
            if ($this->priceWithTaxes && $prices->getBasePrice() !== $pricesWithTaxes->getBasePrice()) {
                $this->appliedTaxes = true;
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
            'product' => $this->product,
            'property' => $this->property,
            'showTaxText' => $this->showTaxText,
            'priceWithTaxes' => $this->priceWithTaxes,
            'isAlternativePrice' => $this->isAlternativePrice,
            'taxText' => $this->taxText,
            'appliedTaxes' => $this->appliedTaxes,
            'stockAlertButton' => $this->stockAlertButton,
            'stockAlertButtonClass' => $this->stockAlertButtonClass,
            'showStock' => $this->showStock,
            'showAvailabilityName' => $this->showAvailabilityName,
            'showStockText' => $this->showStockText,
            'showAvailabilityImage' => $this->showAvailabilityImage
        ];
    }
}
