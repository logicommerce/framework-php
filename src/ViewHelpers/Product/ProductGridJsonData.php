<?php

namespace FWK\ViewHelpers\Product;

use FWK\Core\Resources\Session\BasketGridProduct;
use SDK\Dtos\Catalog\Product\Product;
use SDK\Enums\CombinationDataStatus;

/**
 * This is the ProductGridJsonData class.
 * The purpose of this class is to facilitate to Twig the generation of the products's json output.
 *
 * @see ProductGridJsonData::output()
 *
 * @package FWK\ViewHelpers\Product
 */
class ProductGridJsonData {

    public const SHOW_GRID_PRICE_FIRST_OPTION = 'showGridPriceFirstOption';

    public const SHOW_GRID_PRICE_SECOND_OPTION = 'showGridPriceSecondOption';

    public const SHOW_GRID_PRICE_BOTH_OPTIONS = 'showGridPriceBothOptions';

    public const SHOW_GRID_PRICE_ANY_OPTION = 'showGridPriceAnyOption';

    private ?Product $product;

    private array $gridData = [];

    private array $gridOptionsIds = [];

    private bool $appliedTaxes = false;

    private bool $showAsGrid = false;

    /**
     * Constructor method.
     * 
     * @param Product $product
     * @param bool $appliedTaxes
     * @param bool $showAsGrid
     */
    public function __construct(Product $product = null, $appliedTaxes = false, $showAsGrid = false) {
        $this->product = $product;
        $this->appliedTaxes = $appliedTaxes;
        $this->showAsGrid = $showAsGrid;
        $this->setGridOptions();
        $this->setGridCombinations();
        $this->setGridPrices();
    }

    /**
     * This method returns an array with the entire data of the product.
     * Keys of the returned array:
     * <ul>
     *      <li>id</li>
     *      <li>sku</li>
     *      <li>name</li>
     *      <li>brandName</li>
     *      <li>availabilityId</li>
     *      <li>options</li>
     *      <li>definition -> Array containing these keys:
     *          <ul>
     *          <li>price</li>
     *          <li>basePrice</li>
     *          <li>retailPrice</li>
     *          <li>productBasePrice</li>
     *          <li>productRetailPrice</li>
     *          <li>alternativePrice</li>
     *          <li>alternativeBasePrice</li>
     *          <li>alternativeRetailPrice</li>
     *          <li>productAlternativeBasePrice</li>
     *          <li>productAlternativeRetailPrice</li>
     *          <li>featured</li>
     *          <li>offer</li>
     *          <li>stockManagement</li>
     *          <li>backorder</li>
     *          <li>minOrderQuantity</li>
     *          <li>maxOrderQuantity</li>
     *          <li>multipleOrderQuantity</li>
     *          <li>multipleActsOver</li>
     *          <li>groupQuantityByOptions</li>
     *          <li>onRequest</li>
     *          <li>onRequestDays</li>
     *          </ul>
     *      </li>
     *      <li>combinations</li>
     *      <li>stocks</li>
     *      <li>priceByQuantity</li>
     *      <li>restrictionsMain</li>
     *      <li>stockPrevisions</li>
     *      <li>backorderPrevisions</li>
     *      <li>stockLocks</li>
     *      <li>mainCategory</li>
     *      <li>mainCategoryName</li>
     * </ul>
     *
     * @return array
     */
    public function output(): array {
        return $this->gridData;
    }

    /**
     * Set grid options property
     *
     * @return void
     */
    protected function setGridOptions(): void {
        $this->gridData['options'] = [];

        $gridOptions = [];
        foreach ($this->product->getOptions() as $option) {
            if (
                !$this->showAsGrid ||
                (in_array($option->getType(), BasketGridProduct::AVAILABLE_PRODUCT_GRID_OPTION_TYPES) && $option->getShowAsGrid())
            ) {
                array_push($gridOptions, $option);
                $this->gridOptionsIds[] = $option->getId();
            }
        }

        if (!$this->showAsGrid || (count($gridOptions) > 0 && count($gridOptions) <= 2)) {
            $this->gridData['options'] = $gridOptions;
            $this->setGridCombinations();
            $this->setGridPrices();
        } else {
            $this->gridOptionsIds = [];
        }
    }

    /**
     * set Grid Combinations with the prices and stocks
     *
     * @return void
     */
    protected function setGridCombinations(): void {
        $this->gridData['combinations'] = [];

        $this->gridData['combinations']['values'] = [];
        $this->gridData['combinations']['totalStock'] = 0;
        foreach ($this->product->getCombinations() as $combination) {
            $stock = 0;
            foreach ($combination->getStocks() as $combinationStock) {
                $stock += $combinationStock->getUnits();
                foreach ($combinationStock->getPrevisions() as $combinationStockPrevision) {
                    $stock += $combinationStock->getUnits();
                }
            }
            $this->gridData['combinations']['values'][$combination->getId()] = [
                'optionValueIds' => array_map(function ($combinationValue) {
                    return $combinationValue->getProductOptionValueId();
                }, $combination->getValues()),
                'stock' => $stock,
                'quantity' => 0
            ];
            $this->gridData['combinations']['totalStock'] += $stock;
        }

        $gridOptionsIds = array_map(function ($option) {
            return $option->getId();
        }, $this->gridData['options']);

        $this->gridData['combinations']['status'] = CombinationDataStatus::AVAILABLE;
        $this->gridData['combinations']['optionsValuesWithoutGrid'] = [];
        foreach ($this->product->getCombinationData()->getOptions() as $combinationDataOption) {
            if (!in_array($combinationDataOption->getId(), $gridOptionsIds)) {
                if ($combinationDataOption->getMissed()) {
                    $this->gridData['combinations']['status'] = CombinationDataStatus::SELECT_OPTION;
                }
                foreach ($combinationDataOption->getValues() as $combinationDataOptionValue) {
                    if ($combinationDataOptionValue->getSelected()) {
                        $this->gridData['combinations']['optionsValuesWithoutGrid'][] = $combinationDataOptionValue->getId();
                    }
                }
            }
        }
    }

    /**
     * set Grid Combinations with the prices and stocks
     *
     * @return void
     */
    protected function setGridPrices(): void {
        $this->gridData['prices'] = [];
        $this->gridData['prices']['values'] = [];
        $this->gridData['prices']['showTax'] = $this->appliedTaxes;
        if ($this->showAsGrid) {
            $this->gridData['prices']['showGridPrice'] = self::SHOW_GRID_PRICE_ANY_OPTION;
        }

        $fncGetPrices = 'getPrices';
        if ($this->appliedTaxes) {
            $fncGetPrices .= 'WithTaxes';
        }

        $combinationDataSelectedValues = [];
        foreach ($this->product->getCombinationData()->getOptions() as $combinationDataOption) {
            foreach ($combinationDataOption->getValues() as $combinationDataOptionValue) {
                if ($combinationDataOptionValue->getSelected()) {
                    $combinationDataSelectedValues[] = $combinationDataOptionValue->getId();
                }
            }
        }
        if ($this->product->getDefinition()->getOffer()) {
            $combinationDataPrice = $this->product->getCombinationData()->$fncGetPrices()->getRetailPrice();
        } else {
            $combinationDataPrice = $this->product->getCombinationData()->$fncGetPrices()->getBasePrice();
        }

        $somePriceByQuantity = false;
        $prices = $this->product->$fncGetPrices();
        if ($this->product->getDefinition()->getOffer()) {
            $price = $prices->getPrices()->getRetailPrice();
        } else {
            $price = $prices->getPrices()->getBasePrice();
        }
        $this->gridData['prices']['product']['price'] = $price;
        $this->gridData['prices']['product']['pricesByQuantity'] = [];
        $priceQ = 0;
        foreach ($prices->getPricesByQuantity() as $pricesByQuantity) {
            if ($this->product->getDefinition()->getOffer()) {
                $priceQ = $pricesByQuantity->getPrices()->getRetailPrice();
            } else {
                $priceQ = $pricesByQuantity->getPrices()->getBasePrice();
            }
            if ($priceQ > 0) $somePriceByQuantity = true;
            $this->gridData['prices']['product']['pricesByQuantity'][$pricesByQuantity->getQuantity()] = $priceQ;
        }

        $combinationDataGridOptionsPrice = 0;
        $idxGridOption = 0;
        foreach ($this->product->getOptions() as $option) {
            $hasPrice = false;
            foreach ($option->getValues() as $optionValue) {
                $prices = $optionValue->$fncGetPrices();
                if ($this->product->getDefinition()->getOffer()) {
                    $price = $prices->getPrices()->getRetailPrice();
                } else {
                    $price = $prices->getPrices()->getBasePrice();
                }
                $this->gridData['prices']['values'][$optionValue->getId()]['price'] = $price;
                $this->gridData['prices']['values'][$optionValue->getId()]['pricesByQuantity'] = [];
                $priceQ = 0;
                foreach ($prices->getPricesByQuantity() as $pricesByQuantity) {
                    if ($this->product->getDefinition()->getOffer()) {
                        $priceQ = $pricesByQuantity->getPrices()->getRetailPrice();
                    } else {
                        $priceQ = $pricesByQuantity->getPrices()->getBasePrice();
                    }
                    if ($priceQ > 0) $somePriceByQuantity = true;
                    $this->gridData['prices']['values'][$optionValue->getId()]['pricesByQuantity'][$pricesByQuantity->getQuantity()] = $priceQ;
                }
                $this->gridData['prices']['values'][$optionValue->getId()]['price'] = $price;
                if (!$hasPrice && ($price || $priceQ)) {
                    $hasPrice = true;
                }
                if (in_array($optionValue->getId(), $combinationDataSelectedValues)) {
                    $combinationDataGridOptionsPrice += $price;
                }
            }
            if ($this->showAsGrid && in_array($option->getId(), $this->gridOptionsIds)) {
                if ($idxGridOption == 0 && $hasPrice) {
                    $this->gridData['prices']['showGridPrice'] = self::SHOW_GRID_PRICE_FIRST_OPTION;
                } else if ($hasPrice && $this->gridData['prices']['showGridPrice'] == self::SHOW_GRID_PRICE_FIRST_OPTION) {
                    $this->gridData['prices']['showGridPrice'] = self::SHOW_GRID_PRICE_BOTH_OPTIONS;
                } elseif ($hasPrice) {
                    $this->gridData['prices']['showGridPrice'] = self::SHOW_GRID_PRICE_SECOND_OPTION;
                }
                $idxGridOption++;
            }
        }

        if ($this->showAsGrid && $somePriceByQuantity) {
            $this->gridData['prices']['showGridPrice'] = self::SHOW_GRID_PRICE_BOTH_OPTIONS;
        }

        $this->gridData['prices']['combinationDataGridOptionsPrice'] = $combinationDataGridOptionsPrice;
        $this->gridData['prices']['priceWithoutGrid'] = $combinationDataPrice - $combinationDataGridOptionsPrice;
    }
}
