<?php

namespace FWK\ViewHelpers\Basket\Macro;

use SDK\Dtos\Basket\Basket;
use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use FWK\Dtos\Basket\Basket as FWKBasket;
use SDK\Dtos\Basket\AppliedDiscount;
use SDK\Enums\BasketRowType;

/**
 * This is the DeleteDiscountCodes class, a macro class for the basket viewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the basket's voucher form.
 *
 * @see DeleteDiscountCodes::getViewParameters()
 *
 * @package FWK\ViewHelpers\Basket\Macro
 */
class DeleteDiscountCodes {

    public ?Basket $basket = null;

    private array $appliedDiscount = [];

    /**
     * Constructor method for DeleteDiscountCodes class.
     *
     * @see DeleteDiscountCodes
     *
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        ViewHelper::mergeArguments($this, $arguments);
    }

    /**
     * This method returns all calculated arguments and new parameters for BasketViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        if (is_null($this->basket)) {
            throw new CommerceException("The value of [basket] argument: '" . $this->basket . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }
        $this->basket = FWKBasket::fillFromParent($this->basket);

        $this->loadAppliedDiscounts();

        return $this->getProperties();
    }

    private function loadAppliedDiscounts() {
        foreach ($this->basket->getItems() as $item) {
            if ($item->getType() === BasketRowType::PRODUCT) {
                foreach ($item->getAppliedDiscounts() as $appliedDiscount) {
                    $this->addAppliedDiscount($appliedDiscount);
                }
            }
        }
        foreach ($this->basket->getAppliedDiscounts() as $appliedDiscount) {
            $this->addAppliedDiscount($appliedDiscount);
        }
        if (!is_null($this->basket->getDelivery())) {
            foreach ($this->basket->getDelivery()->getShipments() as $shipment) {
                if (!is_null($shipment->getShipping()) && !is_null($shipment->getShipping()->getAppliedDiscounts())) {
                    foreach ($shipment->getShipping()->getAppliedDiscounts() as $appliedDiscount) {
                        $this->addAppliedDiscount($appliedDiscount);
                    }
                }
            }
        }
        foreach ($this->basket->getVouchers()->getDiscountCodes() as $discountCode) {
            if (isset($this->appliedDiscount[$discountCode->getDiscountId()])) {
                $discountCode->setAppliedDiscount($this->appliedDiscount[$discountCode->getDiscountId()]);
            }
        }
    }

    private function addAppliedDiscount(AppliedDiscount $appliedDiscount) {
        $appliedDiscountClass = str_replace('SDK', 'FWK', $appliedDiscount::class);
        $appliedDiscount = $appliedDiscountClass::fillFromParent($appliedDiscount);
        if (isset($this->appliedDiscount[$appliedDiscount->getDiscountId()])) {
            $this->appliedDiscount[$appliedDiscount->getDiscountId()]->addTotalValue($appliedDiscount->getValue());
        } else {
            $this->appliedDiscount[$appliedDiscount->getDiscountId()] = $appliedDiscount;
            $this->appliedDiscount[$appliedDiscount->getDiscountId()]->setTotalValue($appliedDiscount->getValue());
        }
    }

    protected function getProperties(): array {
        return [
            'vouchers' => $this->basket->getVouchers()
        ];
    }
}
