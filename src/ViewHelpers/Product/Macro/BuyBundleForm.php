<?php

namespace FWK\ViewHelpers\Product\Macro;

use FWK\Core\Resources\Language;
use FWK\Core\Resources\Loader;
use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Enums\LanguageLabels;
use FWK\Enums\Services;
use SDK\Core\Dtos\ElementCollection;
use SDK\Dtos\Catalog\BundleGrouping;
use SDK\Enums\PluginConnectorType;

/**
 * This is the BuyBundleForm class, a macro class for the productViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the product's buy form.
 *
 * @see BuyBundleForm::getViewParameters()
 *
 * @package FWK\ViewHelpers\Product\Macro
 */
class BuyBundleForm {

    public string $class = '';

    public bool $showPrice = true;

    public bool $showBasePrice = true;

    public bool $showSaving = true;

    public string $showBundleBuyTitle = '';

    public bool $showQuantity = false;

    public bool $showQuantitySelectableBox = false;

    public bool $showTaxText = false;

    private string $taxText = '';

    public bool $priceWithTaxes = false;

    private ?bool $applicationTaxes = null;

    private bool $appliedTaxes = false;

    private bool $isAlternativePrice = false;

    private ?Language $languageSheet = null;

    public int $minQuantity = 1;

    public int $maxQuantity = 1;

    public bool $quantityPlugin = true;

    public string $bundleGroupingContent = '';

    public string $shoppingListButton = '';

    public string $buttonRecommend = '';

    public ?BundleGrouping $bundleGrouping = null;

    public array $mainProducts = [];

    public int $bundleId = 0;

    public bool $showLabel = true;

    public int $shoppingListRowId = 0;

    public bool $expressCheckout = true;

    protected ?ElementCollection $expressCheckoutPlugins = null;

    /**
     * Constructor method for BuyBundleForm class.
     * 
     * @see BuyBundleForm
     * 
     * @param array $arguments
     * @param Language $languageSheet
     */
    public function __construct(array $arguments, Language $languageSheet) {
        $this->languageSheet = $languageSheet;
        $this->setDefaults();
        ViewHelper::mergeArguments($this, $arguments);
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

        $this->priceWithTaxes = $this->applicationTaxes = ViewHelper::getApplicationTaxesIncluded();

        $this->setIsAlternativePrice();
        $this->setTaxText();
        $this->setAppliedTaxes();

        if ($this->expressCheckout) {
            $this->expressCheckoutPlugins = Loader::service(Services::PLUGIN)->getExpressCheckoutPlugins();
        }




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
        $prices = $this->bundleGrouping->getCombinationData()->getPrices();
        $pricesWithTaxes = $this->bundleGrouping->getCombinationData()->getPricesWithTaxes();

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
            'class' => $this->class,
            'showBundleBuyTitle' => $this->showBundleBuyTitle,
            'showQuantity' => $this->showQuantity,
            'showQuantitySelectableBox' => $this->showQuantitySelectableBox,
            'bundleGroupingContent' => $this->bundleGroupingContent,
            'bundleGrouping' => $this->bundleGrouping,
            'minQuantity' => $this->minQuantity,
            'maxQuantity' => $this->maxQuantity,
            'priceWithTaxes' => $this->priceWithTaxes,
            'showTaxText' => $this->showTaxText,
            'showPrice' => $this->showPrice,
            'showBasePrice' => $this->showBasePrice,
            'showSaving' => $this->showSaving,
            'isAlternativePrice' => $this->isAlternativePrice,
            'taxText' => $this->taxText,
            'appliedTaxes' => $this->appliedTaxes,
            'mainProducts' => $this->mainProducts,
            'quantityPlugin' => $this->quantityPlugin,
            'bundleId' => $this->bundleId,
            'showLabel' => $this->showLabel,
            'shoppingListButton' => $this->shoppingListButton,
            'buttonRecommend' => $this->buttonRecommend,
            'shoppingListRowId' => $this->shoppingListRowId,
            'expressCheckout' => $this->expressCheckout,
            'expressCheckoutPlugins' => $this->expressCheckoutPlugins
        ];
    }
}
