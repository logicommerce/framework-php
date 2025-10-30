<?php

namespace FWK\ViewHelpers\Document\Macro\Output;

use SDK\Dtos\Documents\Document as SDKDocument;
use FWK\ViewHelpers\Document\Macro\Document;
use SDK\Enums\TaxType;

/**
 * This is the Disclosure class, a macro class for the document viewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the document's disclosure output.
 *
 * @see Disclosure::getData()
 *
 * @package FWK\ViewHelpers\Document\Macro\Output
 */
class Disclosure {

    private const TABLE_COLUMNS = [
        'subtotal',
        'discounts',
        'taxBase',
        'taxes',
        'VAT',
        'RE',
        'total'
    ];

    private ?SDKDocument $document = null;

    private ?Document $documentOutput = null;

    private bool $hasDiscounts = false;

    private bool $applyTax = false;

    private bool $applyRE = false;

    private bool $showTaxesZeroTotalIncrement = false;

    /**
     * Constructor method for Disclosure class.
     *
     * @see Disclosure
     *
     * @param Output $documentOutput
     */
    public function __construct(Document $documentOutput) {
        $this->documentOutput = $documentOutput;
        $this->document = $documentOutput->document;
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
        if (!empty($this->document->getTaxes())) {
            $this->applyTax = true;
        }
        if (method_exists($this->document, 'getDiscounts')) {
            foreach ($this->document->getDiscounts() as $discount) {
                if ($discount->getValue() > 0) {
                    $this->hasDiscounts = true;
                    break;
                }
            }
        }
        foreach ($this->document->getTaxes() as $tax) {
            if ($tax->getType() === TaxType::LOGICOMMERCE && $tax->getRERate() > 0) {
                $this->applyRE = true;
            }
            if ($tax->getDiscount() > 0) {
                $this->hasDiscounts = true;
            }
            if ($this->applyRE && $this->hasDiscounts) {
                break;
            }
        }

        return [
            'applyRE' => $this->applyRE,
            'applyTax' => $this->applyTax,
            'hasDiscounts' => $this->hasDiscounts,
            'colspan' => Document::colspanCalculator($this->getTableBodyColumns()),
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
        if (!$this->documentOutput->showTaxName) {
            $columns -= 1;
        }
        return $columns;
    }
}
