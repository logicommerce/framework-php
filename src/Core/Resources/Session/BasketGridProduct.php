<?php

namespace FWK\Core\Resources\Session;

use FWK\Core\Resources\Session\BasketGridProduct\Combination;
use FWK\Core\Resources\Session\BasketGridProduct\OptionSummary;
use FWK\Core\Resources\Session\BasketGridProduct\OptionValueSummary;
use FWK\Core\Resources\Session\BasketGridProduct\RowGrid;
use FWK\Core\Resources\Session\BasketGridProduct\RowGridCell;
use SDK\Core\Dtos\Element;
use SDK\Dtos\Basket\BasketRows\Product as BasketRowsProduct;
use SDK\Dtos\Catalog\Product\Product;
use SDK\Enums\OptionType;

/**
 * This is the BasketGridProduct class.
 * The BasketGridProduct items will be stored in that class and will remain immutable (only get methods are available)
 *
 * @see BasketGridProduct::getDefaultOneId()
 *
 * @package FWK\Core\Resources\Session
 */
class BasketGridProduct implements \JsonSerializable {

    public const AVAILABLE_PRODUCT_GRID_OPTION_TYPES = [OptionType::SINGLE_SELECTION, OptionType::SELECTOR, OptionType::SINGLE_SELECTION_IMAGE];

    private array $combinations = [];

    private array $optionValueIds = [];

    private array $optionIds = [];

    private array $gridOptionIds = [];

    private array $gridOptionValueIds = [];

    private array $noCombinableOptions = [];

    private array $noCombinableOptionsBuyByOptions = [];

    private array $rows = [];

    /**
     * Constructor for the class.
     *
     * @param Product $product The product object.
     */
    public function __construct(Product $product) {
        $optionsAsGrid = 0;
        foreach ($product->getOptions() as $option) {
            if (in_array($option->getType(), self::AVAILABLE_PRODUCT_GRID_OPTION_TYPES) && $option->getShowAsGrid()) {
                $optionsAsGrid++;
            }
        }
        if ($optionsAsGrid >= 0 && $optionsAsGrid <= 2) {
            $this->setOptionsSummary($product);
            $this->setCombinations($product);
        }
    }

    private function setOptionsSummary(Product $product) {
        foreach ($product->getOptions() as $option) {
            $valueIds = [];
            foreach ($option->getValues() as $value) {
                $valueIds[] = $value->getId();
                $this->optionValueIds[$value->getId()] = new OptionValueSummary(
                    $value->getId(),
                    $value->getLanguage()->getValue(),
                    $value->getImages()->getSmallImage(),
                    $option->getId()
                );
                if (in_array($option->getType(), self::AVAILABLE_PRODUCT_GRID_OPTION_TYPES) && $option->getShowAsGrid()) {
                    $this->gridOptionValueIds[] = $value->getId();
                }
            }
            $this->optionIds[$option->getId()] = new OptionSummary(
                $option->getLanguage()->getName(),
                $valueIds
            );
            if (in_array($option->getType(), self::AVAILABLE_PRODUCT_GRID_OPTION_TYPES) && $option->getShowAsGrid()) {
                $this->gridOptionIds[] = $option->getId();
            }
        }
    }

    private function setCombinations(Product $product) {
        foreach ($product->getCombinations() as $combination) {
            $combinationObject = new Combination();
            $combinationValueIds = array_map(function ($combinationValue) {
                return $combinationValue->getProductOptionValueId();
            }, $combination->getValues());
            $combinationObject->gridOptionValueIds = array_intersect($combinationValueIds, $this->gridOptionValueIds);
            $combinationObject->noGridCombinableOptionValueIds = array_diff($combinationValueIds, $this->gridOptionValueIds);
            foreach ($combinationValueIds as $combinationValueId) {
                $combinationObject->combinableOptionsBuyByOptions[] = [
                    'id' => $this->optionValueIds[$combinationValueId]->optionId,
                    'values' => [
                        ['value' => strval($combinationValueId)]
                    ]
                ];
            }
            $this->combinations[$combination->getId()] = $combinationObject;
        }
        $this->resetCombinations();
    }

    /**
     * Resets all combinations in the object.
     *
     */
    public function resetCombinations() {
        foreach ($this->combinations as $combination) {
            $combination->prices = null;
            $combination->pricesWithTaxes = null;
            $combination->quantity = 0;
            $combination->hash = '';
        }
        $this->noCombinableOptions = [];
        $this->noCombinableOptionsBuyByOptions = [];
    }

    /**
     * Updates the combination for a given BasketRowsProduct.
     *
     * @param BasketRowsProduct $basketRow The BasketRowsProduct to update the combination for.
     */
    public function updateCombination(BasketRowsProduct $basketRow) {
        if (empty($this->noCombinableOptions)) {
            foreach ($basketRow->getOptions() as $basketRowOption) {
                if (!$basketRowOption->getCombinable()) {
                    $this->noCombinableOptions[] = $basketRowOption;
                    $option = [];
                    $option['id'] = $basketRowOption->getId();
                    $option['values'] = [];
                    switch ($basketRowOption->getType()) {
                        case OptionType::MULTIPLE_SELECTION:
                        case OptionType::MULTIPLE_SELECTION_IMAGE:
                            foreach ($basketRowOption->getValueList() as $value) {
                                $option['values'][] = [
                                    'value' => strval($value->getId())
                                ];
                            }
                            break;
                        case OptionType::SINGLE_SELECTION:
                        case OptionType::SINGLE_SELECTION_IMAGE:
                            $option['values'][] = [
                                'value' => strval($basketRowOption->getValue()->getId())
                            ];
                            break;
                        case OptionType::ATTACHMENT:
                            foreach ($basketRowOption->getValues() as $value) {
                                $option['values'][] = [
                                    'value' => $value
                                ];
                            }
                            break;
                        case OptionType::SHORT_TEXT:
                        case OptionType::LONG_TEXT:
                        case OptionType::BOOLEAN:
                            $option['values'][] = [
                                'value' => strval($basketRowOption->getValue())
                            ];
                            break;
                        case OptionType::DATE:
                            if (!empty($basketRowOption?->getValue())) {
                                $option['values'][] = [
                                    'value' => strval(date_format($basketRowOption->getValue()->getDateTime(), 'Y-m-d H:i:s'))
                                ];
                            }
                            break;
                    }
                    $this->noCombinableOptionsBuyByOptions[] = $option;
                }
            }
        }
        $this->combinations[$basketRow->getCombination()->getId()]->prices = $basketRow->getPrices();
        $this->combinations[$basketRow->getCombination()->getId()]->pricesWithTaxes = $basketRow->getPricesWithTaxes();
        $this->combinations[$basketRow->getCombination()->getId()]->quantity = $basketRow->getQuantity();
        $this->combinations[$basketRow->getCombination()->getId()]->hash = $basketRow->getHash();
        $this->combinations[$basketRow->getCombination()->getId()]->subtotal = $basketRow->getSubtotal();
        $this->combinations[$basketRow->getCombination()->getId()]->total = $basketRow->getTotal();
        $this->combinations[$basketRow->getCombination()->getId()]->appliedDiscounts = $basketRow->getAppliedDiscounts();
    }

    /**
     * Retrieves the rows grids.
     *
     * @return array The rows grids.
     */
    public function setRows() {
        $this->rows = [];

        foreach ($this->combinations as $combinationId => $combination) {
            asort($combination->noGridCombinableOptionValueIds);
            if (!empty($combination->noGridCombinableOptionValueIds)) {
                $rowKey = implode('-', $combination->noGridCombinableOptionValueIds);
            } else {
                $rowKey = '0';
            }
            asort($combination->gridOptionValueIds);
            $gridKey = implode('-', $combination->gridOptionValueIds);
            if (!isset($this->rows[$rowKey])) {
                $this->rows[$rowKey] = new RowGrid();
            }
            $rowGridCell = new RowGridCell();
            $rowGridCell->combinationId = $combinationId;
            $rowGridCell->quantity = $combination->quantity;
            $rowGridCell->prices = $combination->prices;
            $rowGridCell->pricesWithTaxes = $combination->pricesWithTaxes;
            $rowGridCell->hash = $combination->hash;
            $rowGridCell->buyByOptions = array_merge($this->noCombinableOptionsBuyByOptions, $combination->combinableOptionsBuyByOptions);
            $rowGridCell->appliedDiscounts = $combination->appliedDiscounts;

            $this->rows[$rowKey]->addCombination($gridKey, $rowGridCell);
            if ($combination->quantity > 0) {
                $this->rows[$rowKey]->totalPrice->addPrices($combination->prices, $combination->pricesWithTaxes);
                $this->rows[$rowKey]->total += $combination->total;
                $this->rows[$rowKey]->subtotal += $combination->subtotal;
                $this->rows[$rowKey]->mergeDiscounts($combination->appliedDiscounts);
            }
        }
    }

    /**
     * Retrieves the combinations
     *
     * @return array
     */
    public function getCombinations(): array {
        return $this->combinations;
    }

    /**
     * Retrieves the no combinable options
     *
     * @return array
     */
    public function getNoCombinableOptions(): array {
        return $this->noCombinableOptions;
    }

    /**
     * Retrieves the rows grids.
     *
     * @return array The rows grids.
     */
    public function getRows(): array {
        return $this->rows;
    }

    /**
     * Returns the options of the class.
     *
     * @return array The array of options.
     */
    public function getOptions(): array {
        return $this->optionIds;
    }

    /**
     * Retrieves the option values.
     *
     * @return array The option values.
     */
    public function getOptionValues(): array {
        return $this->optionValueIds;
    }

    /**
     * Retrieves the array of grid option IDs.
     *
     * @return array The array of grid option IDs.
     */
    public function getGridOptionIds(): array {
        return $this->gridOptionIds;
    }

    /**
     * Retrieves the value IDs of the grid option.
     *
     * @return array The array of grid option value IDs.
     */
    public function getGridOptionValueIds(): array {
        return $this->gridOptionValueIds;
    }

    public function jsonSerialize(): array {
        return [
            'combinations' => $this->getCombinations(),
            'noCombinableOptions' => $this->getNoCombinableOptions(),
            'rows' => $this->getRows(),
            'optionIds' => $this->getOptions(),
            'optionValueIds' => $this->getOptionValues(),
            'gridOptionIds' => $this->getGridOptionIds(),
            'gridOptionValueIds' => $this->getGridOptionValueIds()
        ];
    }
}
