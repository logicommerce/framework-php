<?php

namespace FWK\Core\Resources\Session\BasketGridProduct;

use SDK\Dtos\Basket\BasketRowPrices;

class Combination implements \JsonSerializable {
    public array $gridOptionValueIds = [];
    public array $noGridCombinableOptionValueIds = [];
    public array $combinableOptionsBuyByOptions = [];
    public ?BasketRowPrices $prices = null;
    public ?BasketRowPrices $pricesWithTaxes = null;
    public int $quantity = 0;
    public string $hash = '';
    public float $subtotal = 0;
    public float $total = 0;
    public array $appliedDiscounts = [];

    public function jsonSerialize(): array {
        return [
            'gridOptionValueIds' => $this->gridOptionValueIds,
            'noGridCombinableOptionValueIds' => $this->noGridCombinableOptionValueIds,
            'combinableOptionsBuyByOptions' => $this->combinableOptionsBuyByOptions,
            'prices' => $this->prices,
            'pricesWithTaxes' => $this->pricesWithTaxes,
            'quantity' => $this->quantity,
            'hash' => $this->hash,
            'subtotal' => $this->subtotal,
            'total' => $this->total,
            'appliedDiscounts' => $this->appliedDiscounts
        ];
    }
}
