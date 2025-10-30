<?php

namespace FWK\Dtos\Documents\RichPrices;

use SDK\Core\Dtos\Traits\ElementTrait;

/**
 * This is the Document Row Item rich prices container class.
 *
 * @see DocumentRow::getProductPriceWithTaxes
 * @see DocumentRow::getOptionsPrice
 * @see DocumentRow::getOptionsPriceWithTaxes
 * @see DocumentRow::getTotalDiscountsValue
 * @see DocumentRow::getTotalDiscountsValueWithTaxes
 * @see DocumentRow::getTotalWithDiscounts
 * @see DocumentRow::getTotalWithDiscountsWithTaxes
 * 
 * @see DocumentRow
 * @see ElementTrait
 *
 * @package FWK\Dtos\Documents\RichPrices
 */
class DocumentRowItem extends DocumentRow {
    use ElementTrait;

    protected float $productPrice = 0;

    protected float $productPriceWithTaxes = 0;

    protected float $optionsPrice = 0;

    protected float $optionsPriceWithTaxes = 0;

    protected float $totalDiscountsValue = 0;

    protected float $totalDiscountsValueWithTaxes = 0;

    protected float $totalWithDiscounts = 0;

    protected float $totalWithDiscountsWithTaxes = 0;


    /**
     * Returns the productPrice value.
     *
     * @return float
     */
    public function getProductPrice(): float {
        return $this->productPrice;
    }

    /**
     * Returns the productPriceWithTaxes value.
     *
     * @return float
     */
    public function getProductPriceWithTaxes(): float {
        return $this->productPriceWithTaxes;
    }

    /**
     * Returns the optionsPrice value.
     *
     * @return float
     */
    public function getOptionsPrice(): float {
        return $this->optionsPrice;
    }

    /**
     * Returns the optionsPriceWithTaxes value.
     *
     * @return float
     */
    public function getOptionsPriceWithTaxes(): float {
        return $this->optionsPriceWithTaxes;
    }

    /**
     * Returns the totalDiscountsValue value.
     *
     * @return float
     */
    public function getTotalDiscountsValue(): float {
        return $this->totalDiscountsValue;
    }

    /**
     * Returns the totalDiscountsValueWithTaxes value.
     *
     * @return float
     */
    public function getTotalDiscountsValueWithTaxes(): float {
        return $this->totalDiscountsValueWithTaxes;
    }

    /**
     * Returns the totalWithDiscounts value.
     *
     * @return float
     */
    public function getTotalWithDiscounts(): float {
        return $this->totalWithDiscounts;
    }

    /**
     * Returns the totalWithDiscountsWithTaxes value.
     *
     * @return float
     */
    public function getTotalWithDiscountsWithTaxes(): float {
        return $this->totalWithDiscountsWithTaxes;
    }
}
