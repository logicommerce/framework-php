<?php

namespace FWK\ViewHelpers\Product\Macro;

use SDK\Dtos\Catalog\Product\Product;
use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Resources\Language;
use FWK\Enums\LanguageLabels;
use SDK\Dtos\Catalog\Product\PricesByQuantity;

/**
 * This is the PriceByQuantity class, a macro class for the productViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the product's price by quantity.
 *
 * @see PriceByQuantity::getViewParameters()
 *
 * @package FWK\ViewHelpers\Product\Macro
 */
class PriceByQuantity {

    public ?Product $product = null;

    public ?bool $showTaxIncluded = null;

    public ?string $tableClassName = null;

    private ?array $pricesByQuantity = null;

    private ?Language $languageSheet = null;

    /**
     * Constructor method for PriceByQuantity class.
     * 
     * @see PriceByQuantity
     * 
     * @param array $arguments
     * @param Language $languageSheet
     */
    public function __construct(array $arguments, Language $languageSheet) {
        $this->languageSheet = $languageSheet;
        $this->showTaxIncluded = ViewHelper::getApplicationTaxesIncluded();
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

        $this->setPricesByQuantity();
        $this->setPricesByQuantityMessages();

        return $this->getProperties();
    }

    /**
     * Set prices to loop with or without taxes
     *
     * @return void
     */
    private function setPricesByQuantity(): void {
        $pricesKey = 'getPrices';

        if ($this->showTaxIncluded === true) {
            $pricesKey = 'getPricesWithTaxes';
        }

        $pricesByQuantity = [];

        $pricesByQuantity['base'] = [];
        $pricesByQuantity['base'][] = [
            'optionValueId' => 0,
            'basePrice' => $this->product->{$pricesKey}()->getPrices()->getBasePrice(),
            'retailPrice' => $this->product->{$pricesKey}()->getPrices()->getRetailPrice(),
            'from' => 1,
        ];
        foreach ($this->product->{$pricesKey}()->getPricesByQuantity() as $priceByQuantity) {
            $pricesByQuantity['base'][] = $this->getPriceByQuantity($priceByQuantity);
        }

        foreach ($this->product->getOptions() as $options) {
            foreach ($options->getValues() as $value) {
                if (!empty($value->{$pricesKey}()->getPricesByQuantity())) {
                    $key = 'optionValueId' . $value->getId();
                    $pricesByQuantity[$key] = [];
                    $pricesByQuantity[$key][] = [
                        'optionValueId' => $value->getId(),
                        'basePrice' => $value->{$pricesKey}()->getPrices()->getBasePrice(),
                        'retailPrice' => $value->{$pricesKey}()->getPrices()->getRetailPrice(),
                        'from' => 1,
                    ];
                    foreach ($value->{$pricesKey}()->getPricesByQuantity() as $priceByQuantity) {
                        $pricesByQuantity[$key][] = $this->getPriceByQuantity($priceByQuantity, $value->getId());
                    }
                }
            }
        }

        $this->pricesByQuantity = $pricesByQuantity;
    }

    /**
     * Create custom price by quantity item for output loop
     *
     * @param PricesByQuantity $priceByQuantity
     * @param int $optionValueId
     *
     * @return array
     */
    private function getPriceByQuantity(PricesByQuantity $priceByQuantity, int $optionValueId = 0): array {
        return [
            'optionValueId' => $optionValueId,
            'basePrice' => $priceByQuantity->getPrices()->getBasePrice(),
            'retailPrice' => $priceByQuantity->getPrices()->getRetailPrice(),
            'from' => $priceByQuantity->getQuantity(),
        ];
    }

    /**
     * Set output PriceByQuantity items message
     *
     * @return void
     */
    private function setPricesByQuantityMessages(): void {
        if (!is_null($this->pricesByQuantity['base']) && count($this->pricesByQuantity['base']) >= 2) {
            for ($i = 0; $i < count($this->pricesByQuantity['base']); $i++) {
                $label = '';
                if ($i == count($this->pricesByQuantity['base']) - 1) {
                    $label = $this->getPriceByQuantityItemLabel(LanguageLabels::EQUAL_OR_GREATER_N_UNITS, $this->pricesByQuantity['base'][$i]['from']);
                } else if ($i == 0 && $this->pricesByQuantity['base'][$i + 1]['from'] == 2) {
                    $label = $this->getPriceByQuantityItemLabel(LanguageLabels::ONE_UNIT, $this->pricesByQuantity['base'][$i]['from']);
                } else if ($i == 0) {
                    $label = $this->getPriceByQuantityItemLabel(LanguageLabels::EQUAL_OR_GREATER_N_UNITS, 1, $this->pricesByQuantity['base'][$i + 1]['from']);
                } else if ($this->pricesByQuantity['base'][$i]['from'] + 1 == $this->pricesByQuantity['base'][$i + 1]['from']) {
                    $label = $this->getPriceByQuantityItemLabel(LanguageLabels::N_UNITS, $this->pricesByQuantity['base'][$i]['from']);
                } else {
                    $label = $this->getPriceByQuantityItemLabel(LanguageLabels::EQUAL_OR_GREATER_N_UNITS, $this->pricesByQuantity['base'][$i]['from']);
                }
                $this->pricesByQuantity['base'][$i]['message'] = $label;
            }
        }
    }

    /**
     * Return PriceByQuantity item message
     *
     * @param string $label
     *            LanguageLabels enum
     * @param mixed $n
     *            first replace value
     * @param mixed $m
     *            second replace value
     *            
     * @return string
     */
    private function getPriceByQuantityItemLabel(string $label, $n = '', $m = ''): string {
        $result = '';

        $langLabel = $this->languageSheet->getLabelValue($label);
        $result = str_replace('{{n}}', $n, $langLabel);
        $result = str_replace('{{m}}', $m, $result);

        return $result;
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        return [
            'product' => $this->product,
            'pricesByQuantity' => $this->pricesByQuantity,
            'tableClassName' => $this->tableClassName,
        ];
    }
}
