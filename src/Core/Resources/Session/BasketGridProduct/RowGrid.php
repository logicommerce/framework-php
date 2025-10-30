<?php

namespace FWK\Core\Resources\Session\BasketGridProduct;

use SDK\Core\Dtos\Factories\AppliedDiscountFactory;
use SDK\Enums\AmountType;
use SDK\Enums\DiscountType;

class RowGrid implements \JsonSerializable {
    public array $grid = [];
    public ?TotalPrice $totalPrice = null;
    public int $totalQuantity = 0;
    public float $total = 0;
    public float $subtotal = 0;
    public array $discounts = [];

    public function __construct() {
        $this->totalPrice = new TotalPrice();
    }

    public function addCombination($gridKey, RowGridCell $combination) {
        if ($combination->quantity > 0) {
            $this->totalQuantity += $combination->quantity;
        }
        $this->grid[$gridKey] = $combination;
    }

    public function mergeDiscounts(array $appliedDiscounts) {
        foreach ($appliedDiscounts as $appliedDiscount) {
            if (isset($this->discounts[$appliedDiscount->getDiscountId()])) {
                $discount = $this->discounts[$appliedDiscount->getDiscountId()]->toArray();
                foreach ($appliedDiscount->toArray() as $name => $value) {
                    if (
                        in_array($name, ['quantity', 'valueWithTaxes', 'value'])
                        || ($name == 'discountValue'
                            && !(($appliedDiscount->getType() == DiscountType::AMOUNT || $appliedDiscount->getType() == DiscountType::AMOUNT_COMBINATION)
                                && $appliedDiscount->getAmountType() == AmountType::PERCENTAGE)
                        )
                    ) {
                        $discount[$name] += $value;
                    }
                }
                $this->discounts[$appliedDiscount->getDiscountId()] = AppliedDiscountFactory::getAppliedDiscount($discount);
            } else {
                $this->discounts[$appliedDiscount->getDiscountId()] = $appliedDiscount;
            }
        }
    }

    public function jsonSerialize(): array {
        return [
            'grid' => $this->grid,
            'totalPrice' => $this->totalPrice,
            'totalQuantity' => $this->totalQuantity,
            'total' => $this->total,
            'subtotal' => $this->subtotal,
            'discounts' => $this->discounts
        ];
    }
}
