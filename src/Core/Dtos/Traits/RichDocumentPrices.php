<?php

declare(strict_types=1);

namespace FWK\Core\Dtos\Traits;

use FWK\Dtos\Documents\RichPrices\DocumentRowBundle;
use FWK\Dtos\Documents\RichPrices\DocumentRowItem;
use SDK\Dtos\Documents\Document;
use SDK\Dtos\Documents\Rows\DocumentRow;
use SDK\Dtos\Documents\Transactions\DocumentAppliedTax;
use SDK\Dtos\Documents\Transactions\LogicommerceDocumentAppliedTax;
use SDK\Enums\BasketRowType;
use SDK\Enums\TaxType;

/**
 * This is the Related items trait.
 *
 * @package FWK\Core\Dtos\Traits
 */
trait RichDocumentPrices {

    protected function setDocuemntRowRichPrices(Document &$document): void {
        foreach ($document->getItems() as $documentRow) {
            if ($documentRow->getType() === BasketRowType::BUNDLE) {
                $this->addRichBundlePrices($documentRow);
                foreach ($documentRow->getItems() as $item) {
                    $this->addRichPrices($item);
                }
            } else {
                $this->addRichPrices($documentRow);
            }
        }
    }

    protected function addRichPrices(DocumentRow &$documentRow): void {
        $taxIncrement = 1  + ($this->getTaxesIncrement($documentRow->getTaxes()) / 100);
        $totalDiscountsValue = 0;
        foreach ($documentRow->getDiscounts() as $discount) {
            $totalDiscountsValue += $discount->getValue();
        }
        $documentRow->setRichPrices(
            new DocumentRowItem([
                'productPrice' => $documentRow->getPrices()->getProductPrice() / $taxIncrement,
                'productPriceWithTaxes' => $documentRow->getPrices()->getProductPrice(),
                'optionsPrice' => $documentRow->getPrices()->getOptionsPrice(),
                'optionsPriceWithTaxes' => $documentRow->getPrices()->getOptionsPrice() * $taxIncrement,
                'previousPrice' => $documentRow->getPrices()->getPreviousPrice(),
                'previousPriceWithTaxes' => $documentRow->getPrices()->getPreviousPrice() * $taxIncrement,
                'price' => $documentRow->getPrices()->getPrice(),
                'priceWithTaxes' => $documentRow->getPrices()->getPrice() * $taxIncrement,
                'totalTaxesValue' => $documentRow->getPrices()->getTotalTaxes(),
                'totalDiscountsValue' => $totalDiscountsValue,
                'totalDiscountsValueWithTaxes' => $totalDiscountsValue * $taxIncrement,
                'total' => $documentRow->getPrices()->getTotal(),
                'totalWithTaxes' => $documentRow->getPrices()->getTotal() * $taxIncrement,
                'totalWithDiscounts' => $documentRow->getPrices()->getTotal() - $totalDiscountsValue,
                'totalWithDiscountsWithTaxes' => ($documentRow->getPrices()->getTotal() - $totalDiscountsValue) * $taxIncrement
            ])
        );
    }

    protected function addRichBundlePrices(DocumentRow &$documentRow): void {
        $taxIncrement = 1  + ($documentRow->getPrices()->getTotalTaxes() / $documentRow->getPrices()->getTotal());
        $documentRow->setRichPrices(
            new DocumentRowBundle(
                [
                    'previousPrice' => $documentRow->getPrices()->getPreviousPrice(),
                    'previousPriceWithTaxes' => $documentRow->getPrices()->getPreviousPrice() * $taxIncrement,
                    'price' => $documentRow->getPrices()->getPrice(),
                    'priceWithTaxes' => $documentRow->getPrices()->getPrice() * $taxIncrement,
                    'totalTaxesValue' => $documentRow->getPrices()->getTotalTaxes(),
                    'total' => $documentRow->getPrices()->getTotal(),
                    'totalWithTaxes' => $documentRow->getPrices()->getTotal() * $taxIncrement
                ]
            )
        );
    }

    private function getTaxesIncrement(array $taxes): float {
        $taxIncrement = 0;
        foreach ($taxes as $tax) {
            $taxIncrement += $this->getTaxIncrement($tax);
        }
        return $taxIncrement;
    }

    private function getTaxIncrement(DocumentAppliedTax $tax): float {
        if (!$tax->getApplyTax()) {
            return 0;
        }
        if ($tax->getType() === TaxType::LOGICOMMERCE) {
            /** @var LogicommerceDocumentAppliedTax $lcAppliedTax */
            $lcAppliedTax = $tax;
            $taxIncrement = $lcAppliedTax->getTax()->getTaxRate();
            if ($lcAppliedTax->getApplyRe()) {
                $taxIncrement += $lcAppliedTax->getTax()->getReRate();
            }
            return $taxIncrement;
        } else {
            return $tax->getTaxRate();
        }
    }
}
