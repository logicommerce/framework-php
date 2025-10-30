<?php

namespace FWK\ViewHelpers\Basket\Macro\Output;

use FWK\Core\Resources\Language;
use FWK\Core\ViewHelpers\Macros\Basket\Macro\BaseOutput;
use SDK\Dtos\Basket\AppliedDiscount;
use SDK\Dtos\Basket\Basket;
use SDK\Enums\DiscountType;
use FWK\Enums\LanguageLabels;
use FWK\ViewHelpers\Basket\Macro\BasketContent;
use SDK\Enums\DeliveryType;
use SDK\Enums\PickingDeliveryType;

/**
 * This is the Footer class, a macro class for the basket viewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the basket's footer.
 *
 * @see Footer::getRows()
 *
 * @package FWK\ViewHelpers\Basket\Macro\Output
 */
class Footer {

    private const CLASS_NAME = 'className';

    private const CELLS = 'cells';

    private array $rows = [];

    private int $colspan = 0;

    private ?Basket $basket = null;

    private ?BasketContent $basketOutput = null;

    private ?Language $languageSheet = null;

    /**
     * Constructor method for Footer class.
     *
     * @see Footer
     *
     * @param BasketContent $basketOutput
     * @param Language $languageSheet
     */
    public function __construct(BasketContent $basketOutput, Language $languageSheet) {
        $this->basketOutput = $basketOutput;
        $this->basket = $basketOutput->basket;
        $this->colspan = BasketContent::colspanCalculator($basketOutput->getTableBodyColumns());
        $this->languageSheet = $languageSheet;
        $this->setSubtotalRows();
        $this->setShippingRows();
        $this->setShippingDiscountRows();
        $this->setPaymentRows();
        $this->setDiscountRows();
        $this->setTotalRows();
    }

    /**
     * This method gets all the basket corresponding footer rows structure information.
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
        $cell = [];

        $cell['value'] = $value;
        $cell['colspan'] = $this->colspan;
        $cell['className'] = $className;

        return $cell;
    }

    /**
     * This method sets the subtotal footer rows.
     *
     * @return void
     */
    private function setSubtotalRows(): void {
        $basketFooterClass = 'basket basketFooter';
        $basketFooterSubtotalClass = 'basketFooter basketSubtotalFooter';
        $basketFooterPriceClass = 'basket basketFooter basketPrice basketSubtotal';

        if ($this->basketOutput->showPrices) {
            $basketTotals = $this->basket->getTotals();
            $subtotalRows = 0;

            if (!is_null($basketTotals)) {
                if ($this->basketOutput->showTaxIncluded) {
                    $subtotalRows = $basketTotals->getTotalRows();
                } else {
                    $subtotalRows = $basketTotals->getSubtotalRows();
                }
            }

            $cells = [
                $this->createCell($this->languageSheet->getLabelValue(LanguageLabels::SUBTOTAL), $basketFooterClass),
                $this->createCell($subtotalRows, $basketFooterPriceClass)
            ];

            $this->setRow([
                self::CLASS_NAME => $basketFooterSubtotalClass,
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
        $delivery = $this->basket->getDelivery();

        if (!is_null($delivery)) {
            foreach ($delivery->getShipments() as $shipment) {
                $shipping = $shipment->getShipping();
                if (!is_null($shipping)) {
                    $shippingName = $shipping->getType()->getShipper()->getLanguage()->getName() . ' - ' . $shipping->getType()->getLanguage()->getName();
                    $shippingPrice = $shipping->getPrices()->getPrice();
                    if ($this->basketOutput->showTaxIncluded == true) {
                        $shippingPrice = $shipping->getPricesWithTaxes()->getPrice();
                    }
                    if ($this->basketOutput->showZeroShipping || $shippingPrice > 0) {
                        $cells = [
                            $this->createCell($shippingName, 'basket basketFooter'),
                            $this->createCell($shippingPrice, 'basket basketFooter basketPrice basketShipping')
                        ];
                        $this->setRow([
                            self::CLASS_NAME => 'basketFooter basketShippingFooter',
                            self::CELLS => $cells
                        ]);
                    }
                }
            }
            if (
                $delivery->getType() == DeliveryType::PICKING
                && $delivery->getMode()?->getType() == PickingDeliveryType::PROVIDER_PICKUP_POINT
                && $delivery->getPrice() > 0
            ) {
                $pickingPrice = $delivery->getPrice();
                if ($this->basketOutput->showTaxIncluded == true) {
                    $pickingPrice = $delivery->getPriceWithTaxes();
                }
                $this->setRow([
                    self::CLASS_NAME => 'basketFooter basketPickingFooter',
                    self::CELLS => [
                        $this->createCell($this->languageSheet->getLabelValue(LanguageLabels::PICKING_PRICE), 'basket basketFooter'),
                        $this->createCell($pickingPrice, 'basket basketFooter basketPrice basketPicking')
                    ]
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
        $delivery = $this->basket->getDelivery();

        if (!is_null($delivery)) {
            foreach ($delivery->getShipments() as $shipment) {
                $shipping = $shipment->getShipping();
                if (!is_null($shipping)) {
                    foreach ($shipping->getAppliedDiscounts() as $discount) {
                        if ($this->showDiscountRow($discount, $discount->getType())) {
                            $discountValue = 0;
                            if ($discount->getType() === DiscountType::AMOUNT) {
                                if ($this->basketOutput->showTaxIncluded == true) {
                                    $discountValue = -$discount->getValueWithTaxes();
                                } else {
                                    $discountValue = -$discount->getValue();
                                }
                            }
                            $cells = [
                                $this->createCell($discount->getName(), 'basket basketFooter'),
                                $this->createCell($discountValue, 'basket basketFooter basketPrice basketShippingDiscount')
                            ];
                            $this->setRow([
                                self::CLASS_NAME => 'basketFooter basketShippingFooter basketDiscountFooter',
                                self::CELLS => $cells
                            ]);
                        }
                    }
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
        $paymentSystem = $this->basket->getPaymentSystem();

        if (!is_null($paymentSystem)) {

            if ($this->basketOutput->showTaxIncluded == true) {
                $paymentSystemPrice = $this->basket->getTotals()->getTotalPaymentSystem();
            } else {
                $paymentSystemPrice = $this->basket->getTotals()->getSubtotalPaymentSystem();
            }

            if ($this->basketOutput->showZeroPayment || $paymentSystemPrice > 0) {
                $paymentSystemLang = $paymentSystem->getLanguage();
                $paymentSystemName = $this->languageSheet->getLabelValue(LanguageLabels::PAYMENT_SYSTEM);

                if (!is_null($paymentSystemLang)) {
                    $paymentSystemName = $paymentSystemLang->getName();
                }
                $cells = [
                    $this->createCell($paymentSystemName, 'basket basketFooter'),
                    $this->createCell($paymentSystemPrice, 'basket basketFooter basketPrice basketPayment basketSubtotal')
                ];
                $this->setRow([
                    self::CLASS_NAME => 'basketFooter basketPaymentSystemFooter',
                    self::CELLS => $cells
                ]);
            }
        }
    }

    /**
     * This method sets the discount footer rows.
     *
     * @return void
     */
    private function setDiscountRows(): void {
        if ($this->showTotalRow() && $this->basketOutput->showDiscounts) {
            foreach ($this->basket->getAppliedDiscounts() as $discount) {
                if ($this->showDiscountRow($discount, $discount->getType())) {

                    $discountValue = 0;
                    if ($discount->getType() === DiscountType::AMOUNT || $discount->getType() === DiscountType::REWARD_POINTS) {
                        $discountValue = -$discount->getValue();
                    }

                    $name = $discount->getName();
                    if ($discount->getType() === DiscountType::REWARD_POINTS) {
                        $this->languageSheet->getLabelValue(LanguageLabels::REWARD_POINTS_REDEEMED);
                        $name = str_replace('{{name}}',  $discount->getName(), $this->languageSheet->getLabelValue(LanguageLabels::REWARD_POINTS_REDEEMED));
                        $name = str_replace('{{value}}',  $discount->getDiscountValue(), $name);
                    }

                    $cells = [
                        $this->createCell($name, 'basket basketFooter'),
                        $this->createCell($discountValue, 'basket basketFooter basketPrice basketSubtotal')
                    ];
                    $this->setRow([
                        self::CLASS_NAME => 'basketFooter basketDiscountFooter',
                        self::CELLS => $cells
                    ]);
                }
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

            $basketTotals = $this->basket->getTotals();
            if (!is_null($basketTotals)) {

                $totalWithoutVouchers = $basketTotals->getTotal() + $basketTotals->getTotalVouchers();
                $totalWithVouchers = $basketTotals->getTotal();

                if ($totalWithoutVouchers !== $totalWithVouchers && $totalWithVouchers != 0) {
                    $cells = [
                        $this->createCell($this->languageSheet->getLabelValue(LanguageLabels::TOTAL_WITHOUT_VOUCHERS), 'basket basketFooter basketTotalWithoutVoucherText'),
                        $this->createCell($totalWithoutVouchers, 'basket basketFooter basketPrice basketTotalWithoutVoucher')
                    ];
                    $this->setRow([
                        self::CLASS_NAME => 'basketFooter basketDisclosureTotalWithoutVoucher',
                        self::CELLS => $cells
                    ]);
                    $cells = [
                        $this->createCell($this->languageSheet->getLabelValue(LanguageLabels::GIFT_CODE), 'basket basketFooter basketVoucherText'),
                        $this->createCell(-$basketTotals->getTotalVouchers(), 'basket basketFooter basketPrice basketTotalWithoutVoucher')
                    ];
                    $this->setRow([
                        self::CLASS_NAME => 'basketFooter basketGiftCodeFooter',
                        self::CELLS => $cells
                    ]);
                }

                // Final Total
                $cells = [
                    $this->createCell($this->languageSheet->getLabelValue(LanguageLabels::TOTAL), 'basket basketFooter basketTotalText'),
                    $this->createCell($basketTotals->getTotal(), 'basket basketFooter basketPrice basketTotal')
                ];
                $this->setRow([
                    self::CLASS_NAME => 'basketFooter basketTotalFooter',
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
        if ($this->basketOutput->mode === BasketContent::MODE_CASH_TICKET && $this->basketOutput->showPrices) {
            return true;
        }
        return false;
    }

    /**
     * This method evaluates whether to show basket grouped discounts row.
     *
     * @return bool
     */
    private function showDiscountRow(AppliedDiscount $discount, string $type): bool {
        $show = false;

        if ($type !== DiscountType::GIFT && $type !== DiscountType::SELECTABLE_GIFT) {
            if ($this->basketOutput->showZeroDiscount) {
                $show = true;
            } elseif (!$this->basketOutput->showZeroDiscount && !BaseOutput::isZeroDiscountPrice($discount->getValue())) {
                $show = true;
            } elseif (!$this->basketOutput->showZeroDiscount && $type === DiscountType::AMOUNT && !BaseOutput::isZeroDiscountPrice($discount->getValue())) {
                $show = true;
            }
        }
        return $show;
    }
}
