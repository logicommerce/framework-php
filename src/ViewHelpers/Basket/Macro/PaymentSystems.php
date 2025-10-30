<?php

namespace FWK\ViewHelpers\Basket\Macro;

use FWK\Core\Dtos\ElementCollection;
use SDK\Dtos\Basket\Basket;
use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Resources\Session;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Resources\Utils;
use FWK\Dtos\Basket\PaymentSystem;

/**
 * This is the PaymentSystems class, a macro class for the basket viewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the basket's payment systems.
 *
 * @see PaymentSystems::getViewParameters()
 *
 * @package FWK\ViewHelpers\Basket\Macro
 */
class PaymentSystems {

    public ?Basket $basket = null;

    public $paymentSystems = null;

    public ?bool $showTaxIncluded = null;

    public bool $showTitle = true;

    public bool $showZeroPrice = true;

    public bool $showDescription = true;

    public bool $showImage = false;

    private bool $basketNeedsPayment = true;

    private ?Session $session = null;

    /**
     * Constructor method for PaymentSystems class.
     * 
     * @see PaymentSystems
     * 
     * @param array $arguments
     */
    public function __construct(array $arguments, Session $session) {
        $this->showTaxIncluded = ViewHelper::getApplicationTaxesIncluded();
        ViewHelper::mergeArguments($this, $arguments);
        $this->session = $session;
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
        if (is_null($this->paymentSystems)) {
            throw new CommerceException("The value of [paymentSystems] argument: '" . $this->paymentSystems . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }

        $this->setBasketNeedsPayment();
        $this->setSelected();

        return $this->getProperties();
    }

    /**
     * Set selected item property
     *
     * @return void
     */
    private function setSelected(): void {
        $isExpressCheckout = Utils::isExpressCheckout($this->basket);
        $basketPaymentsystem = $this->basket->getPaymentSystem();
        $this->paymentSystems = ElementCollection::fillFromParentCollection($this->paymentSystems, PaymentSystem::class);
        if (!is_null($basketPaymentsystem)) {
            foreach ($this->paymentSystems->getItems() as $key => $paymentSystem) {
                if ($paymentSystem->getId() === $basketPaymentsystem->getId()) {
                    $paymentSystem->setSelected(true);
                    foreach ($paymentSystem->getProperties() as $paymentSystemProperty) {
                        foreach ($basketPaymentsystem->getProperties() as $basketPaymentSystemProperty) {
                            if ($paymentSystemProperty->getName() === $basketPaymentSystemProperty->getName()) {
                                $paymentSystemProperty->setSelected($basketPaymentSystemProperty->getSelected());
                                break;
                            }
                        }
                    }
                } elseif ($isExpressCheckout) {
                    $this->paymentSystems->remove($key, false);
                }
            }
        }
        $this->paymentSystems->resetIndex();
    }

    /**
     * Set if payment system is required for basket,
     * basketNeedsPayment property enable/disable macro output.
     *
     * @return void
     */
    private function setBasketNeedsPayment(): void {
        $totals = $this->basket->getTotals();

        if (!is_null($totals)) {
            if ($totals->getTotal() <= 0.01) {
                $this->basketNeedsPayment = false;
            }
        }
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        return [
            'paymentSystems' => $this->paymentSystems,
            'showTaxIncluded' => $this->showTaxIncluded,
            'showTitle' => $this->showTitle,
            'showZeroPrice' => $this->showZeroPrice,
            'showImage' => $this->showImage,
            'showDescription' => $this->showDescription,
            'basketNeedsPayment' => $this->basketNeedsPayment,
            'basket' => $this->basket
        ];
    }
}
