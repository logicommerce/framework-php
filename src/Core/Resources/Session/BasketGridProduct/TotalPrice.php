<?php

namespace FWK\Core\Resources\Session\BasketGridProduct;

use SDK\Dtos\Basket\BasketRowPrices;

class TotalPrice implements \JsonSerializable {
    public ?BasketRowPrices $prices = null;
    public ?BasketRowPrices $pricesWithTaxes = null;

    /**
     * Adds the prices of two BasketRowPrices objects.
     *
     * @param BasketRowPrices $prices The prices to be added.
     * @param BasketRowPrices $pricesWithTaxes The prices with taxes to be added.
     */
    public function addPrices(BasketRowPrices $prices, BasketRowPrices $pricesWithTaxes) {
        $this->prices = $this->addPrice($this->prices, $prices);
        $this->pricesWithTaxes = $this->addPrice($this->pricesWithTaxes, $pricesWithTaxes);
    }

    /**
     * Adds the prices of the given BasketRowPrices objects and returns a new BasketRowPrices object.
     *
     * @param ?BasketRowPrices $orgPrices The original BasketRowPrices object. Can be null.
     * @param BasketRowPrices $prices The BasketRowPrices object to add to the original prices.
     * @return BasketRowPrices The new BasketRowPrices object with the added prices.
     */
    public function addPrice(?BasketRowPrices $orgPrices, BasketRowPrices $prices) {
        if (is_null($orgPrices)) {
            return $prices;
        }

        $newPrices = [];
        $newPrices['unitPrice'] = $orgPrices->getUnitPrice() == $prices->getUnitPrice() ? $prices->getUnitPrice() : 0;
        $newPrices['unitPreviousPrice'] = $orgPrices->getUnitPreviousPrice() == $prices->getUnitPreviousPrice() ? $prices->getUnitPreviousPrice() : 0;
        $newPrices['price'] = $orgPrices->getPrice() + $prices->getPrice();
        $newPrices['previousPrice'] = $orgPrices->getPreviousPrice() + $prices->getPreviousPrice();
        $newPrices['totalDiscounts'] = $orgPrices->getTotalDiscounts() + $prices->getTotalDiscounts();

        return new BasketRowPrices($newPrices);
    }


    public function jsonSerialize(): array {
        return [
            'prices' => $this->prices,
            'pricesWithTaxes' => $this->pricesWithTaxes
        ];
    }
}
