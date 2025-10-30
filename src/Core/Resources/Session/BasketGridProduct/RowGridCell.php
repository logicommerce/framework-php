<?php

namespace FWK\Core\Resources\Session\BasketGridProduct;

use FWK\Core\Resources\Utils;
use SDK\Dtos\Basket\BasketRowPrices;
use SDK\Enums\BasketRowType;

class RowGridCell implements \JsonSerializable {
    public int $combinationId = 0;
    public int $quantity = 0;
    public string $hash = '';
    public array $buyByOptions = [];
    public ?BasketRowPrices $prices = null;
    public ?BasketRowPrices $pricesWithTaxes = null;
    public array $appliedDiscounts = [];

    public function getQuantityOutput($quantityPlugin, $selectableBoxRows, $editable): string {
        $quantity = $this->quantity;
        $nameAttr = 'quantity' . 'Grid' .  $this->combinationId;
        $output = '<span class="basketTextQuantity">' . $quantity . '</span>';
        $buyByOptions = Utils::outputJsonHtmlString($this->buyByOptions);
        if ($editable) {
            if ($quantityPlugin) {
                $output = '<input type="text" class="{{className}} basketQuantity validate-integer" name="' . $nameAttr . '" value="' . $quantity . '" data-lc-row-options=\'' . $buyByOptions . '\' data-lc-row-type="' . BasketRowType::PRODUCT . '" data-lc-grid-combination-id="' . $this->combinationId . '" data-lc-quantity="quantity" min=0 max=99999999 autocomplete="off" >';
            } elseif ($selectableBoxRows > 0) {
                // El select no tiene en cuenta el min/max/multiple de quantity Deprecated.
                $output = '<select class="{{className}} basketQuantity quantitySelect" name="' . $nameAttr . '" data-lc-row-options=\'' . $buyByOptions . '\' data-lc-row-type="' . BasketRowType::PRODUCT . '" data-lc-grid-combination-id="' . $this->combinationId . '" autocomplete="off" >';
                for ($optionIndex = 0; $optionIndex <= $selectableBoxRows; $optionIndex += 1) {
                    $selectedAttr = '';
                    if ($quantity === $optionIndex) {
                        $selectedAttr = 'selected="selected"';
                    }
                    $output .= '<option value="' . $optionIndex . '" ' . $selectedAttr . '>' . $optionIndex . '</option>';
                }
                $output .= '</select>';
            } else {
                $output = '<input type="text" class="{{className}} basketQuantity validate-integer" name="' . $nameAttr . '" data-lc-row-options=\'' . $buyByOptions . '\' data-lc-row-type="' . BasketRowType::PRODUCT . '" data-lc-grid-combination-id="' . $this->combinationId . '" value="' . $quantity . '" autocomplete="off" >';
            }
        }
        return $output;
    }

    public function jsonSerialize(): array {
        return [
            'combinationId' => $this->combinationId,
            'quantity' => $this->quantity,
            'hash' => $this->hash,
            'buyByOptions' => $this->buyByOptions,
            'prices' => $this->prices,
            'pricesWithTaxes' => $this->pricesWithTaxes,
            'appliedDiscounts' => $this->appliedDiscounts
        ];
    }
}
