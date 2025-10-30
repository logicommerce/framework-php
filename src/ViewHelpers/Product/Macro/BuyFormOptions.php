<?php

namespace FWK\ViewHelpers\Product\Macro;

use BackedEnum;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\Resources\Language;
use FWK\Core\Theme\Theme;
use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Dtos\Catalog\Product\Product as ViewHelpersDtosProduct;
use FWK\Enums\LanguageLabels;
use FWK\Enums\Parameters;
use FWK\ViewHelpers\Product\ProductGridJsonData;
use SDK\Application;
use SDK\Core\Dtos\Catalog\BundleCombinationData;
use SDK\Dtos\Catalog\Product\Product;
use SDK\Enums\BackorderMode;
use SDK\Enums\OptionType;

/**
 * This is the BuyFormOptions class, a macro class for the productViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the product's buy form options.
 *
 * @see BuyFormOptions::getViewParameters()
 *
 * @package FWK\ViewHelpers\Product\Macro
 */
class BuyFormOptions {

    public ?Product $product = null;

    public bool $showShortDescription = false;

    public bool $showLongDescription = false;

    public bool $showUnavailableLabel = false;

    public bool $selectDefaults = true;

    public bool $showImageOptions = false;

    public bool $showBasePrice = false;

    public bool $showRetailPrice = false;

    public int $bundleDefinitionSectionItemId = 0;

    public array $bundleDefinitionSectionItemOptions = [];

    public bool $useUrlOptionsParams = false;

    public bool $addOptionsToProductLink = false;

    public ?BundleCombinationData $combinationData = null;

    public bool $showTitleInLabelUniqueImage = false;

    public array $optionReferences = [];

    public ?bool $priceWithTaxes = null;

    public bool $showTaxText = false;

    public bool $useFilePlugin = false;

    public string $attachmentAcceptAttribute = '';

    // Grid parameters
    public bool $showGridFirst = true;

    public bool $showAsGridQuantityPlugin = true;

    public bool $showAsGridUniqueDimension = false;

    public bool $showGridImageValues = false;

    public bool $showGridAvailabilityImage = false;

    public bool $showGridAvailabilityName = false;

    public ?bool $showOrderBox = null;

    public bool $showGridDisabled = false;

    // Private properties (no parameters)

    private bool $appliedTaxes = false;

    private array $gridData = [];

    private bool $combinationDataOptionChanged = false;

    private ?bool $applicationTaxes = null;

    private bool $isAlternativePrice = false;

    private string $taxText = '';

    private ?Language $languageSheet = null;

    private bool $purchasableWithoutStock = false;

    /**
     * Constructor method for BuyFormOptions class.
     * 
     * @see BuyFormOptions
     * 
     * @param array $arguments
     */
    public function __construct(array $arguments) {
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
        if (is_null($this->product)) {
            throw new CommerceException("The value of [product] argument is required" . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }

        if (!is_null($this->combinationData) && !($this->combinationData instanceof BundleCombinationData)) {
            throw new CommerceException('The value of combinationData argument must be a instance of ' . BundleCombinationData::class . '. ' . ' Instance of ' . get_class($this->combinationData) . ' sended.', CommerceException::VIEW_HELPER_INVALID_ARGUMENT);
        }

        $this->setIsAlternativePrice();
        $this->setTaxText();
        $this->setAppliedTaxes();

        if ($this->product->getId() > 0) {
            $auxProduct = $this->product->toArray();

            if (count($this->bundleDefinitionSectionItemOptions) > 0) {
                $this->setBundleDefinitionSectionItemOptions($auxProduct);
            }
            if (isset($auxProduct['relatedItems'])) {
                $auxProduct['relatedItems'] = $this->product->getRelatedItems();
            }
            $this->product = new ViewHelpersDtosProduct($auxProduct);

            if ($this->showUnavailableLabel) {
                $this->setNotAvailableOptionValues();
            }

            if (!$this->showGridDisabled && !Theme::getInstance()->getConfiguration()->getCommerce()->isDisableShowAsGridProductOptions() && count($this->bundleDefinitionSectionItemOptions) === 0) {
                $this->gridData = (new ProductGridJsonData($this->product, $this->appliedTaxes, true))->output();
            }
            $this->setSelectedOptionValues();
        }

        if (
            (
                Application::getInstance()->getEcommerceSettings()->getStockSettings()->getAllowReservations()
                && $this->product->getDefinition()->getBackorder() != BackorderMode::NONE
            )
            || $this->product->getDefinition()->getOnRequest()
        ) {
            $this->purchasableWithoutStock = true;
        }

        return $this->getProperties();
    }

    /**
     * Set bundle definition section item options
     *
     * @param array $auxProduct
     * @return void
     */
    private function setBundleDefinitionSectionItemOptions(array &$auxProduct) {
        $availableOptions = [];
        foreach ($this->bundleDefinitionSectionItemOptions as $bundleDefinitionSectionItemOption) {
            $availableOptions[$bundleDefinitionSectionItemOption->getOptionId()][] = $bundleDefinitionSectionItemOption;
        }
        foreach ($auxProduct['options'] as $keyAuxOption => $auxOption) {
            if (isset($availableOptions[$auxOption['id']])) {
                $availableValues = [];
                foreach ($availableOptions[$auxOption['id']] as $value) {
                    $availableValues[] = $value->getOptionValueId();
                }
                foreach ($auxOption['values'] as $keyValue => $value) {
                    if (!in_array($value['id'], $availableValues)) {
                        unset($auxProduct['options'][$keyAuxOption]['values'][$keyValue]);
                    }
                }
            }
        }
        if (!is_null($this->combinationData)) {
            $auxProduct['combinationData'] = $this->combinationData->toArray();
        }
    }

    /**
     * Set selected option values by GET optionId_123=123 parameters and selectDefaults argument logic.
     * Add to Option object [selectedOptionValueId] int property
     *
     * @return void
     */
    private function setSelectedOptionValues(): void {
        $options = $this->product->getOptions();
        // Get url get data
        $filterInputHandler = new FilterInputHandler();
        $urlParams = [];
        $urlOptions = [];
        if ($this->useUrlOptionsParams) {
            $urlParams = $filterInputHandler->getFilterFilterInputs(FilterInputHandler::PARAMS_FROM_GET, FilterInputFactory::getSelectedOptions());
        }
        if (count($urlParams) > 0 && isset($urlParams[Parameters::OPTION_ID])) {
            $this->useUrlOptionsParams = true;
            foreach ($urlParams[Parameters::OPTION_ID] as $name => $value) {
                if (!is_null($value)) {
                    $optionId = explode('_', $name)[0];
                    if (!isset($urlOptions[$optionId])) {
                        $urlOptions[$optionId] = [];
                    }
                    $urlOptions[$optionId][] = $value;
                }
            }
        } else {
            $this->useUrlOptionsParams = false;
        }

        if ($this->useUrlOptionsParams === true || $this->selectDefaults === true) {
            if (count($options) > 0) {
                $usedUrlParams = $this->getUsedUrlParams($options, $urlOptions);
                if ($usedUrlParams === 0) {
                    $this->useUrlOptionsParams = false;
                }
            } else {
                $this->useUrlOptionsParams = false;
            }
        }

        if ($this->useUrlOptionsParams === true) {
            $this->combinationDataOptionChanged = true;
        }
    }

    /**
     * Get used url params variable, used in self::setSelectedOptionValues();
     *
     * @param array $options
     * @param array $urlOptions
     * @return int
     */
    private function getUsedUrlParams(array $options, array $urlOptions): int {
        $usedUrlParams = 0;
        foreach ($options as $option) {
            $optionValues = $option->getValues();
            $selectedOptionValueId = null;
            // Search selected option value by url params
            if ($this->useUrlOptionsParams && isset($urlOptions[$option->getId()])) {
                $urlOptionValues = $urlOptions[$option->getId()];
                if ($option->getType() === OptionType::BOOLEAN) {
                    $selectedOptionValueId = boolval($urlOptionValues[0]);
                    $usedUrlParams++;
                } else {
                    foreach ($optionValues as $optionValue) {
                        if ($option->getType() != OptionType::MULTIPLE_SELECTION && $option->getType() != OptionType::MULTIPLE_SELECTION_IMAGE) {
                            if (intval($urlOptionValues[0]) === $optionValue->getId()) {
                                $selectedOptionValueId = $optionValue->getId();
                                $usedUrlParams++;
                                break;
                            }
                        } else {
                            if (in_array($optionValue->getId(), $urlOptionValues)) {
                                if (is_null($selectedOptionValueId)) {
                                    $selectedOptionValueId = [];
                                }
                                $selectedOptionValueId[] = $optionValue->getId();
                                $usedUrlParams++;
                            }
                        }
                    }
                }
            }
            if (is_null($selectedOptionValueId) && $this->selectDefaults === true && count($optionValues) >= 1) {
                foreach ($this->product->getCombinationData()->getOptions() as $combinationDataOption) {
                    if ($option->getId() === $combinationDataOption->getId()) {
                        if ($combinationDataOption->getMissed() === false) {
                            foreach ($combinationDataOption->getValues() as $combinationDataOptionValue) {
                                if ($option->getType() != OptionType::MULTIPLE_SELECTION && $option->getType() != OptionType::MULTIPLE_SELECTION_IMAGE) {
                                    if ($combinationDataOptionValue->getSelected()) {
                                        $selectedOptionValueId = $combinationDataOptionValue->getId();
                                        break;
                                    }
                                } else {
                                    if ($combinationDataOptionValue->getSelected()) {
                                        $selectedOptionValueId[] = $combinationDataOptionValue->getId();
                                    }
                                }
                            }
                        }
                        break;
                    }
                }
            }

            if (is_null($selectedOptionValueId) && $this->selectDefaults === true && count($this->optionReferences)) {
                foreach ($this->optionReferences as $optionReferences) {
                    if ($option->getId() === $optionReferences->getId() && count($optionReferences->getValues())) {
                        $option->setOptionReferenceValue($optionReferences->getValues()[0]->getValue());
                    }
                }
            }

            // Select default first option value with selectDefaults false when there is only one option
            if (is_null($selectedOptionValueId) && $this->selectDefaults === false && count($optionValues) === 1) {
                $this->combinationDataOptionChanged = true;
                $selectedOptionValueId = $optionValues[0]->getId();
            }
            $option->setSelectedOptionValueId($selectedOptionValueId);
        }
        return $usedUrlParams;
    }

    /**
     * If product only has one option and arguments allow it, set to every option value a property to show notAvailable elements
     * Add to OptionValue object [notAvailable] bool property
     *
     * @return void
     */
    private function setNotAvailableOptionValues(): void {
        $options = $this->product->getOptions();
        $combinationDataOptionsAvailable = [];
        foreach ($this->product->getCombinationData()->getOptions() as $option) {
            $combinationDataOptionsAvailable[$option->getId()] = [];
            foreach ($option->getValues() as $optionValue) {
                $combinationDataOptionsAvailable[$option->getId()][$optionValue->getId()] = $optionValue->getAvailable();
            }
        }
        foreach ($options as $option) {
            if (isset($combinationDataOptionsAvailable[$option->getId()])) {
                foreach ($option->getValues() as $optionValue) {
                    $optionValue->setNotAvailable(!$combinationDataOptionsAvailable[$option->getId()][$optionValue->getId()]);
                }
            }
        }
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
        $prices = $this->product->getPrices();
        $pricesWithTaxes = $this->product->getPricesWithTaxes();

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
            'showShortDescription' => $this->showShortDescription,
            'showLongDescription' => $this->showLongDescription,
            'showUnavailableLabel' => $this->showUnavailableLabel,
            'selectDefaults' => $this->selectDefaults,
            'showImageOptions' => $this->showImageOptions,
            'showBasePrice' => $this->showBasePrice,
            'showRetailPrice' => $this->showRetailPrice,
            'showTitleInLabelUniqueImage' => $this->showTitleInLabelUniqueImage,
            'bundleDefinitionSectionItemId' => $this->bundleDefinitionSectionItemId,
            'useUrlOptionsParams' => $this->useUrlOptionsParams,
            'combinationDataOptionChanged' => $this->combinationDataOptionChanged,
            'addOptionsToProductLink' => $this->addOptionsToProductLink,
            'showGridFirst' => $this->showGridFirst,
            'showGridImageValues' => $this->showGridImageValues,
            'showAsGridQuantityPlugin' => $this->showAsGridQuantityPlugin,
            'showAsGridUniqueDimension' => $this->showAsGridUniqueDimension,
            'showGridAvailabilityImage' => $this->showGridAvailabilityImage,
            'showGridAvailabilityName' => $this->showGridAvailabilityName,
            'gridData' => $this->gridData,
            'showTaxText' => $this->showTaxText,
            'priceWithTaxes' => $this->priceWithTaxes,
            'isAlternativePrice' => $this->isAlternativePrice,
            'taxText' => $this->taxText,
            'appliedTaxes' => $this->appliedTaxes,
            'purchasableWithoutStock' => $this->purchasableWithoutStock,
            'showOrderBox' => $this->showOrderBox,
            'useFilePlugin' => $this->useFilePlugin,
            'attachmentAcceptAttribute' => $this->attachmentAcceptAttribute
        ];
    }
}
