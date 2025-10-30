<?php

namespace FWK\ViewHelpers\Document\Macro\Output;

use FWK\Core\Resources\Language;
use FWK\Core\ViewHelpers\Macros\Basket\Macro\BaseOutput;
use FWK\Enums\LanguageLabels;
use FWK\ViewHelpers\Document\Macro\Document;
use SDK\Dtos\Documents\Document as SDKDocument;

/**
 * This is the Footer class, a macro class for the document viewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the document's footer.
 *
 * @see Footer::getRows()
 *
 * @package FWK\ViewHelpers\Document\Macro\Output
 */
class Footer {

    private const CLASS_NAME = 'className';

    private const CELLS = 'cells';

    private array $rows = [];

    private int $colspan = 0;

    private string $classPrefix = 'order';

    private ?SDKDocument $document = null;

    private ?Document $documentOutput = null;

    private ?Language $languageSheet = null;

    /**
     * Constructor method for Footer class.
     *
     * @see Footer
     *
     * @param Output $documentOutput
     * @param Language $languageSheet
     */
    public function __construct(Document $documentOutput, Language $languageSheet) {
        $this->documentOutput = $documentOutput;
        $this->document = $documentOutput->document;
        $this->colspan = Document::colspanCalculator($documentOutput->getTableBodyColumns());
        $this->languageSheet = $languageSheet;
        $this->setSubtotalRows();
        $this->setShippingRows();
        if ($this->document instanceof SDKDocument) {
            $this->setShippingDiscountRows();
            $this->setPaymentRows();
            $this->setDiscountRows();
        }
        $this->setTotalRows();
    }

    /**
     * This method gets all the document corresponding footer rows structure information.
     * Each row of the returned array contains these keys of information:
     * <ul>
     * <li>self::CLASS_NAME: class name to be applied to the row.</li>
     * <li>self::CELLS: array of the row's cells, where each cell contains these keys of information:
     * <ul>
     * <li>value</li>
     * <li>colspan</li>
     * <li>className</li>
     * </ul>
     * </li>
     * </ul>
     *
     * @return array
     */
    public function getRows(): array {
        return $this->rows;
    }

    /**
     * This method pushes a row into footer rows array.
     *
     * @return void
     */
    private function setRow(array $row): void {
        array_push($this->rows, $row);
    }

    /**
     * This method generates a cell assoc array for a row.
     *
     * @return array
     */
    private function createCell(string $value, string $className = ''): array {
        return [
            'value' => $value,
            'colspan' => $this->colspan,
            'className' => $className
        ];
    }

    /**
     * This method sets the subtotal footer rows.
     *
     * @return void
     */
    private function setSubtotalRows(): void {
        $documentFooterClass = $this->classPrefix . ' ' . $this->classPrefix . 'Footer';
        $documentFooterSubtotalClass = $this->classPrefix . 'Footer ' . $this->classPrefix . 'SubtotalFooter';
        $documentFooterPriceClass = $this->classPrefix . ' ' . $this->classPrefix . 'Footer ' . $this->classPrefix . 'Price ' . $this->classPrefix . 'Subtotal';

        if ($this->documentOutput->showPrices) {
            $documentTotals = $this->document->getTotals();
            $subtotalRows = 0;

            if (!is_null($documentTotals)) {
                if ($this->documentOutput->showTaxIncluded) {
                    $subtotalRows = $documentTotals->getTotalRows();
                } else {
                    $subtotalRows = $documentTotals->getSubtotalRows();
                }
            }

            $cells = [
                $this->createCell($this->languageSheet->getLabelValue(LanguageLabels::SUBTOTAL), $documentFooterClass),
                $this->createCell($subtotalRows, $documentFooterPriceClass)
            ];

            $this->setRow([
                self::CLASS_NAME => $documentFooterSubtotalClass,
                self::CELLS => $cells
            ]);
        }
    }

    /**
     * This method sets the shipping footer rows.
     *
     * @return void
     */
    private function setShippingRows(): void {
        $delivery = $this->document->getDelivery();
        if (is_null($delivery))
            return;

        foreach ($delivery->getShipments() as $shipment) {
            $shipping = $shipment->getShipping();

            if (is_null($shipping))
                continue;

            $shippingName = $shipping->getName() . ' ' . $shipping->getShippingTypeName();
            $shippingPrice = $shipping->getPrice();
            if ($this->documentOutput->showTaxIncluded == true) {
                $taxIncrement = 0;
                foreach ($shipping->getTaxes() as $tax) {
                    if ($tax->getApplyTax()) {
                        $taxIncrement += $tax->getTax()->getTaxRate();
                        if ($tax->getApplyRE()) {
                            $taxIncrement += $tax->getTax()->getReRate();
                        }
                    }
                }
                $shippingPrice = $shippingPrice * (1 + ($taxIncrement / 100));
            }
            if ($this->documentOutput->showZeroShipping || $shippingPrice > 0) {
                $cells = [
                    $this->createCell($shippingName, $this->classPrefix . ' ' . $this->classPrefix . 'Footer'),
                    $this->createCell($shippingPrice, $this->classPrefix . ' ' . $this->classPrefix . 'Footer ' . $this->classPrefix . 'Price ' . $this->classPrefix . 'Shipping')
                ];
                $this->setRow([
                    self::CLASS_NAME => $this->classPrefix . 'Footer ' . $this->classPrefix . 'ShippingFooter',
                    self::CELLS => $cells
                ]);
            }
        }
    }


    /**
     * This method sets the shipping footer rows.
     *
     * @return void
     */
    private function setShippingDiscountRows(): void {
        if (!$this->documentOutput->showDiscounts)
            return;

        $delivery = $this->document->getDelivery();
        if (is_null($delivery))
            return;

        foreach ($delivery->getShipments() as $shipment) {
            $shipping = $shipment->getShipping();

            if (is_null($shipping))
                continue;

            foreach ($shipping->getDiscounts() as $discount) {
                $discountValue = $discount->getValue();
                if ($this->documentOutput->showTaxIncluded == true) {
                    $taxIncrement = 0;
                    foreach ($shipping->getTaxes() as $tax) {
                        if ($tax->getApplyTax()) {
                            $taxIncrement += $tax->getTax()->getTaxRate();
                            if ($tax->getApplyRE()) {
                                $taxIncrement += $tax->getTax()->getReRate();
                            }
                        }
                    }
                    $discountValue = $discountValue * (1 + ($taxIncrement / 100));
                }

                if ($this->documentOutput->showZeroDiscount || (!$this->documentOutput->showZeroDiscount && !BaseOutput::isZeroDiscountPrice($discountValue))) {
                    $cells = [
                        $this->createCell($discount->getName(), $this->classPrefix . ' ' . $this->classPrefix . 'Footer'),
                        $this->createCell(-$discountValue, $this->classPrefix . ' ' . $this->classPrefix . 'Footer ' . $this->classPrefix . 'Price ' . $this->classPrefix . 'ShippingDiscount')
                    ];
                    $this->setRow([
                        self::CLASS_NAME => $this->classPrefix . 'Footer ' . $this->classPrefix . 'DiscountFooter',
                        self::CELLS => $cells
                    ]);
                }
            }
        }
    }

    /**
     * This method sets the payment footer rows.
     *
     * @return void
     */
    private function setPaymentRows(): void {
        $paymentSystem = $this->document->getPaymentSystem();

        if (is_null($paymentSystem))
            return;

        if ($this->documentOutput->showTaxIncluded == true) {
            $paymentSystemPrice = $this->document->getTotals()->getTotalPaymentSystem();
        } else {
            $paymentSystemPrice = $this->document->getTotals()->getSubtotalPaymentSystem();
        }
        if ($this->documentOutput->showZeroPayment || $paymentSystemPrice > 0) {
            $paymentSystemName = $paymentSystem->getName();
            $cells = [
                $this->createCell($paymentSystemName, $this->classPrefix . ' ' . $this->classPrefix . 'Footer'),
                $this->createCell($paymentSystemPrice, $this->classPrefix . ' ' . $this->classPrefix . 'Footer ' . $this->classPrefix . 'Price ' . $this->classPrefix . 'Payment ' . $this->classPrefix . 'Subtotal')
            ];
            $this->setRow([
                self::CLASS_NAME => $this->classPrefix . 'Footer ' . $this->classPrefix . 'PaymentSystemFooter',
                self::CELLS => $cells
            ]);
        }
    }

    /**
     * This method sets the discount footer rows.
     *
     * @return void
     */
    private function setDiscountRows(): void {
        if (!$this->showTotalRow() || !$this->documentOutput->showDiscounts)
            return;

        foreach ($this->document->getDiscounts() as $discount) {
            $discountValue = -$discount->getValue();
            if ($this->documentOutput->showZeroDiscount || (!$this->documentOutput->showZeroDiscount && !BaseOutput::isZeroDiscountPrice($discountValue))) {
                $cells = [
                    $this->createCell($discount->getName(), $this->classPrefix . ' ' . $this->classPrefix . 'Footer'),
                    $this->createCell($discountValue, $this->classPrefix . ' ' . $this->classPrefix . 'Footer ' . $this->classPrefix . 'Price ' . $this->classPrefix . 'Subtotal')
                ];
                $this->setRow([
                    self::CLASS_NAME => $this->classPrefix . 'Footer ' . $this->classPrefix . 'DiscountFooter',
                    self::CELLS => $cells
                ]);
            }
        }
    }

    /**
     * This method sets the total footer rows.
     *
     * @return void
     */
    private function setTotalRows(): void {
        if ($this->showTotalRow()) {

            $documentTotals = $this->document->getTotals();
            if (!is_null($documentTotals)) {
                $totalWithoutVouchers = $documentTotals->getTotal() + $documentTotals->getTotalVouchers();
                $totalWithVouchers = $documentTotals->getTotal();

                if ($totalWithoutVouchers !== $totalWithVouchers) {
                    $cells = [
                        $this->createCell($this->languageSheet->getLabelValue(LanguageLabels::TOTAL_WITHOUT_VOUCHERS), $this->classPrefix . ' ' . $this->classPrefix . 'Footer ' . $this->classPrefix . 'TotalWithoutVoucherext'),
                        $this->createCell($totalWithoutVouchers, $this->classPrefix . ' ' . $this->classPrefix . 'Footer ' . $this->classPrefix . 'Price ' . $this->classPrefix . 'TotalWithoutVoucher')
                    ];
                    $this->setRow([
                        self::CLASS_NAME => $this->classPrefix . 'Footer ' . $this->classPrefix . 'DisclosureTotalWithoutVoucher',
                        self::CELLS => $cells
                    ]);
                    $cells = [
                        $this->createCell($this->languageSheet->getLabelValue(LanguageLabels::GIFT_CODE), $this->classPrefix . ' ' . $this->classPrefix . 'Footer ' . $this->classPrefix . 'VoucherText'),
                        $this->createCell(-$documentTotals->getTotalVouchers(), $this->classPrefix . ' ' . $this->classPrefix . 'Footer ' . $this->classPrefix . 'Price ' . $this->classPrefix . 'TotalWithoutVoucher')
                    ];
                    $this->setRow([
                        self::CLASS_NAME => $this->classPrefix . 'Footer ' . $this->classPrefix . 'GiftCodeFooter',
                        self::CELLS => $cells
                    ]);
                }

                // Final Total
                $cells = [
                    $this->createCell($this->languageSheet->getLabelValue(LanguageLabels::TOTAL), $this->classPrefix . ' ' . $this->classPrefix . 'Footer ' . $this->classPrefix . 'TotalText'),
                    $this->createCell($documentTotals->getTotal(), $this->classPrefix . ' ' . $this->classPrefix . 'Footer ' . $this->classPrefix . 'Price ' . $this->classPrefix . 'Total')
                ];
                $this->setRow([
                    self::CLASS_NAME => $this->classPrefix . 'Footer ' . $this->classPrefix . 'TotalFooter',
                    self::CELLS => $cells
                ]);
            }
        }
    }

    /**
     * This method evaluates whether to display the discount and the total row.
     *
     * @return bool
     */
    private function showTotalRow(): bool {
        return $this->documentOutput->mode === Document::MODE_CASH_TICKET && $this->documentOutput->showPrices;
    }
}
