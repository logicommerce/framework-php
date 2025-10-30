<?php

namespace FWK\Dtos\Documents;

use FWK\Core\Dtos\Factories\DocumentRowFactory;
use FWK\Core\Dtos\Traits\FillFromParentTrait;
use FWK\Dtos\Documents\RichPrices\Discount as RichPricesDiscount;
use FWK\Dtos\Documents\RichPrices\DocumentRowBundle as RichPricesDocumentRowBundle;
use FWK\Dtos\Documents\RichPrices\DocumentRowItem as RichPricesDocumentRowItem;
use FWK\Dtos\Documents\RichPrices\Payment as RichPricesPayment;
use FWK\Dtos\Documents\RichPrices\Shipping as RichPricesShipping;
use FWK\Dtos\Documents\RichPrices\Totals as RichPricesTotals;
use SDK\Dtos\Documents\Transactions\Purchases\Order;
use SDK\Dtos\Documents\Rows\DocumentRow;
use SDK\Enums\BasketRowType;
use FWK\Core\Dtos\Factories\DocumentDeliveryFactory;
use SDK\Enums\TaxType;

/**
 * This is the Document class
 *
 * @see Document::richPrices()
 *
 * @see FillFromParentTrait
 *
 * @package FWK\Dtos\Documents
 */
class Document extends Order {
    use FillFromParentTrait;

    /**
     * @see \SDK\Dtos\Documents\Document::__construct()
     */
    public function __construct(array $data = []) {
        parent::__construct($data);
        $this->richPrices();
    }

    protected function setItems(array $items): void {
        $this->items = $this->setArrayField($items, DocumentRowFactory::class);
    }

    protected function setDelivery(array $delivery): void {
        $this->delivery = DocumentDeliveryFactory::getElement($delivery);
    }

    protected function setPaymentSystem(array $paymentSystem): void {
        $this->paymentSystem = new DocumentPaymentSystem($paymentSystem);
    }

    protected function setTotals(array $totals): void {
        $this->totals = new DocumentTotal($totals);
    }

    protected function richPrices() {        
        foreach ($this->getItems() as $documentRow) {            
            if ($documentRow->getType() === BasketRowType::BUNDLE) {
                $this->addRichBundlePrices($documentRow);
                foreach ($documentRow->getItems() as $item) {
                    $this->addRichPrices($item);
                }
            } else {
                $this->addRichPrices($documentRow);
            }
        }
        $this->setRichShippingPrices();
        $this->setRichPaymentPrices();
        $this->setRichTotalPrices();
    }

    private function getTaxRateIncrement(array $taxes): float {
        $taxIncrement = 0;
        foreach ($taxes as $tax) {
            if ($tax->getApplyTax()) {
                if ($tax->getType() === TaxType::LOGICOMMERCE) {
                    $taxIncrement += $tax->getTax()->getTaxRate();
                    if ($tax->getApplyRe()) {
                        $taxIncrement += $tax->getTax()->getReRate();
                    }
                } else {
                    $taxIncrement += $tax->getTaxRate();
                }
            }
        }
        return $taxIncrement;
    }

    private function addRichPrices(DocumentRow $documentRow): void {
        $taxIncrement = 1  + ($this->getTaxRateIncrement($documentRow->getTaxes()) / 100);
        $totalDiscountsValue = 0;
        foreach ($documentRow->getDiscounts() as $discount) {
            $totalDiscountsValue += $discount->getValue();
            $discount->setRichPrices(
                new RichPricesDiscount([
                    'value' => $discount->getValue(),
                    'valueWithTaxes' => $discount->getValueWithTaxes()
                ])
            );
        }
        $documentRow->setRichPrices(
            new RichPricesDocumentRowItem([
                'productPrice' => $documentRow->getPrices()->getProductPrice() / $taxIncrement,
                'productPriceWithTaxes' => $documentRow->getPrices()->getProductPrice(),
                'optionsPrice' => $documentRow->getPrices()->getOptionsPrice() / $taxIncrement,
                'optionsPriceWithTaxes' => $documentRow->getPrices()->getOptionsPrice(),
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

    private function addRichBundlePrices(DocumentRow $documentRow): void {
        $taxIncrement = 1  + ($documentRow->getPrices()->getTotalTaxes() / $documentRow->getPrices()->getTotal());
        $documentRow->setRichPrices(
            new RichPricesDocumentRowBundle([
                'previousPrice' => $documentRow->getPrices()->getPreviousPrice(),
                'previousPriceWithTaxes' => $documentRow->getPrices()->getPreviousPrice() * $taxIncrement,
                'price' => $documentRow->getPrices()->getPrice(),
                'priceWithTaxes' => $documentRow->getPrices()->getPrice() * $taxIncrement,
                'totalTaxesValue' => $documentRow->getPrices()->getTotalTaxes(),
                'total' => $documentRow->getPrices()->getTotal(),
                'totalWithTaxes' => $documentRow->getPrices()->getTotal() * $taxIncrement
            ])
        );
    }

    private function setRichShippingPrices(): void {
        $delivery = $this->getDelivery();
        if (!is_null($delivery)) {
            foreach ($delivery->getShipments() as $shipment) {
                $shipping = $shipment->getShipping();
                if (!is_null($shipping)) {
                    $taxIncrement = $this->getTaxRateIncrement($shipping->getTaxes());
                    $richPrice = [
                        'price' => $shipping->getPrice(),
                        'priceWithTaxes' => $shipping->getPrice() * (1 + ($taxIncrement / 100)),
                        'priceWithDiscounts' => $shipping->getPrice(),
                        'priceWithDiscountsWithTaxes' => $shipping->getPrice() * (1 + ($taxIncrement / 100)),
                    ];
                    foreach ($shipping->getDiscounts() as $discount) {
                        $discount->setRichPrices(
                            new RichPricesDiscount([
                                'value' => $discount->getValue(),
                                'valueWithTaxes' => $discount->getValueWithTaxes()
                            ])
                        );
                        $richPrice['priceWithDiscounts'] -= $discount->getValue();
                        $richPrice['priceWithDiscountsWithTaxes'] -= $discount->getValue() * (1 + ($taxIncrement / 100));
                    }
                    $shipping->setRichPrices(new RichPricesShipping($richPrice));
                }
            }
        }
    }

    private function setRichPaymentPrices(): void {
        $paymentSystem = $this->getPaymentSystem();
        if (!is_null($paymentSystem)) {
            $taxIncrement = $this->getTaxRateIncrement($paymentSystem->getTaxes());
            $paymentSystem->setRichPrices(
                new RichPricesPayment([
                    'price' => $paymentSystem->getPrice(),
                    'priceWithTaxes' => $paymentSystem->getPrice() * (1 + ($taxIncrement / 100))
                ])
            );
        }
    }

    private function setRichTotalPrices(): void {
        $documentTotals = $this->getTotals();
        if (!is_null($documentTotals)) {
            $documentTotals->setRichPrices(
                new RichPricesTotals([
                    'totalRows' => $documentTotals->getSubtotalRows(),
                    'totalRowsWithTaxes'  >= $documentTotals->getTotalRows(),
                    'totalShippingsWithDiscounts'  >= $documentTotals->getSubtotalShippings(),
                    'totalShippingsWithDiscountsWithTaxes'  >= $documentTotals->getTotalShippings(),
                    'totalPaymentSystem'  >= $documentTotals->getSubtotalPaymentSystem(),
                    'totalPaymentSystemWithTaxes'  >= $documentTotals->getTotalPaymentSystem(),
                    'total'  >= $documentTotals->getSubtotal(),
                    'totalWithDiscounts' => $documentTotals->getTotal() - $documentTotals->getTotalTaxes(),
                    'totalTaxesValue'  >= $documentTotals->getTotalTaxes(),
                    'totalWithDiscountsWithTaxes' => $documentTotals->getTotal(),
                    'totalRowsDiscountsValue'  >= $documentTotals->getTotalRowsDiscounts(),
                    'totalBasketDiscountsValue' => $documentTotals->getTotalBasketDiscounts(),
                    'totalShippingDiscountsValue' => $documentTotals->getTotalShippingDiscounts(),
                    'totalVouchers' => $documentTotals->getTotalVouchers(),
                ])
            );
        }
    }
}
