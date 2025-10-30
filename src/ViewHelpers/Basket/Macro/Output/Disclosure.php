<?php

namespace FWK\ViewHelpers\Basket\Macro\Output;

use SDK\Enums\DiscountType;
use SDK\Dtos\Basket\Basket;
use FWK\ViewHelpers\Basket\Macro\BasketContent;

/**
 * This is the Disclosure class, a macro class for the basket viewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the basket's disclosure output.
 *
 * @see Disclosure::getData()
 *
 * @package FWK\ViewHelpers\Basket\Macro\Output
 */
class Disclosure {

    private const TABLE_COLUMNS = [
        'subtotal',
        'discounts',
        'taxBase',
        'VAT',
        'RE',
        'total'
    ];

    private ?Basket $basket = null;

    private bool $hasDiscounts = false;

    private bool $applyTax = false;

    private bool $applyRE = false;

    private bool $showTaxesZeroTotalIncrement = false;



    /**
     * Constructor method for Disclosure class.
     * 
     * @see Disclosure
     * 
     * @param BasketContent $basketOutput
     */
    public function __construct(BasketContent $basketOutput) {
        $this->basket = $basketOutput->basket;
    }

    /**
     * This method returns an array containing the view parameters to output the disclosure. 
     * The information keys of this array are the following:
     * <ul>
     * <li>applyRE</li>
     * <li>applyTax</li>
     * <li>hasDiscounts</li>
     * <li>colspan</li>
     * </ul>
     * 
     * @return array
     */
    public function getData(): array {
        if (!empty($this->basket->getTaxes())) {
            $this->applyTax = true;
        }

        foreach ($this->basket->getAppliedDiscounts() as $discount) {
            // AMOUNT group ABSOLUTE and PERCENTAGE modes
            if ($discount->getType() === DiscountType::AMOUNT || $discount->getType() === DiscountType::REWARD_POINTS) {
                $this->hasDiscounts = true;
            }
        }

        // FIXME revisar si la manera de mirar el RE de user es correcte
        foreach ($this->basket->getTaxes() as $tax) {
            if ($tax->getRERate() > 0) {
                $this->applyRE = true;
            }
        }

        return [
            'applyRE' => $this->applyRE,
            'applyTax' => $this->applyTax,
            'hasDiscounts' => $this->hasDiscounts,
            'colspan' => BasketContent::colspanCalculator($this->getTableBodyColumns()),
            'showTaxesZeroTotalIncrement' => $this->showTaxesZeroTotalIncrement
        ];
    }

    /**
     * Based on the maximum possible columns and the disclosure properties,
     * calculate the maximum columns of the table's body.
     *
     * @return int
     */
    private function getTableBodyColumns(): int {
        $columns = count(self::TABLE_COLUMNS);

        if (!$this->hasDiscounts) {
            $columns -= 2;
        }
        if (!$this->applyRE) {
            $columns -= 1;
        }
        if (!$this->applyTax) {
            $columns -= 1;
        }
        return $columns;
    }
}










