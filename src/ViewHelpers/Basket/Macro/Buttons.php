<?php

namespace FWK\ViewHelpers\Basket\Macro;

use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Theme\Dtos\Configuration;
use FWK\Core\Theme\Theme;
use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Enums\RouteType;
use FWK\Enums\RouteTypes\InternalCheckout;
use SDK\Dtos\Basket\Basket;
use SDK\Enums\BasketRowType;
use SDK\Enums\BasketWarningCode;
use SDK\Enums\BasketWarningSeverity;

/**
 * This is the Buttons class, a macro class for the basket viewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the basket's buttons.
 *
 * @see Buttons::getViewParameters()
 *
 * @package FWK\ViewHelpers\Basket\Macro
 */
class Buttons {

    public ?Basket $basket = null;

    public bool $showRecalculate = true;

    public bool $showClear = true;

    public bool $showContinue = true;

    public string $submitAction = RouteType::CHECKOUT_END_ORDER;

    public string $classList = '';

    public string $backLocation = '';

    public bool $forceDisabled = false;

    public string $errorCode = '';

    private string $routeType = '';

    private array $validRouteTypes = [
        RouteType::CHECKOUT,
        InternalCheckout::OSC_BUTTONS,
        RouteType::CHECKOUT_BASKET,
        RouteType::CHECKOUT_CONFIRM_ORDER,
        RouteType::CHECKOUT_CUSTOMER,
        RouteType::CHECKOUT_DENIED_ORDER,
        RouteType::CHECKOUT_PAYMENT_AND_SHIPPING
    ];

    private bool $validOutput = false;

    private bool $productsAreValid = true;

    private bool $basketNeedsShipping = true;

    private bool $basketNeedsPayment = true;

    private bool $basketAddressError = true;

    private static ?Configuration $themeConfiguration = null;

    protected static function getConfiguration(): Configuration {
        if (is_null(self::$themeConfiguration)) {
            self::$themeConfiguration = Theme::getInstance()->getConfiguration();
        }
        return self::$themeConfiguration;
    }

    /**
     * Constructor method for Buttons class.
     *
     * @see Buttons
     *
     * @param array $arguments
     * @param string $routeType
     */
    public function __construct(array $arguments, string $routeType) {
        ViewHelper::mergeArguments($this, $arguments);
        $this->routeType = $routeType;
    }

    /**
     * This method returns all calculated arguments and new parameters for BasketViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        if (is_null($this->basket) && $this->routeType !== RouteType::CHECKOUT_DENIED_ORDER) {
            throw new CommerceException("The value of [basket] argument: '" . $this->basket . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }

        $this->getValidOutput();
        $this->getProductsAreValid();
        $this->getClassList();
        $this->getBasketNeedsShipping();
        $this->getBasketNeedsPayment();
        $this->getBasketAddressError();
        $this->getSubmitAction();
        return $this->getProperties();
    }

    /**
     * Sets validOutput property if basket has items and route of output is valid
     *
     * @return void
     */
    private function getValidOutput(): void {
        if (!is_null($this->basket)) {
            if (!empty($this->basket->getItems()) && in_array($this->routeType, $this->validRouteTypes, true)) {
                $this->validOutput = true;
            }
        }
        if ($this->routeType === RouteType::CHECKOUT_DENIED_ORDER) {
            $this->validOutput = true;
        }
    }

    /**
     * Sets productsAreValid property if basket items not contains error warnings
     *
     * @return void
     */
    private function getProductsAreValid(): void {
        if (!is_null($this->basket)) {
            foreach ($this->basket->getItems() as $basketRow) {

                $basketRowWarnings = [];

                if ($basketRow->getType() === BasketRowType::PRODUCT) {
                    $basketRowWarnings = $basketRow->getBasketWarnings();
                } else if ($basketRow->getType() === BasketRowType::BUNDLE) {
                    foreach ($basketRow->getItems() as $item) {
                        $basketRowWarnings = [...$basketRowWarnings, ...$item->getBasketWarnings()];
                    }
                }

                foreach ($basketRowWarnings as $warning) {
                    if ($warning->getSeverity() === BasketWarningSeverity::ERROR) {
                        $this->productsAreValid = false;
                    }
                }
            }
        }
    }

    /**
     * Sets submitAction property the submit url form
     *
     * @return void
     */
    private function getSubmitAction(): void {
        if ($this->routeType === RouteType::CHECKOUT_BASKET) {
            $this->submitAction = RouteType::CHECKOUT_CUSTOMER;
        } elseif ($this->routeType === RouteType::CHECKOUT_CUSTOMER) {
            $this->submitAction = RouteType::CHECKOUT_PAYMENT_AND_SHIPPING;
        } elseif ($this->routeType === RouteType::CHECKOUT_PAYMENT_AND_SHIPPING) {
            $this->submitAction = RouteType::CHECKOUT_END_ORDER;
        } elseif ($this->routeType === RouteType::CHECKOUT_DENIED_ORDER) {
            if (self::getConfiguration()->getCommerce()->getUseOneStepCheckout()) {
                $this->submitAction = RouteType::CHECKOUT;
            } else {
                if ($this->errorCode === 'A01000-ENDORDER' && !($this->basketNeedsShipping || $this->basketNeedsPayment)) {
                    $this->submitAction = RouteType::CHECKOUT_BASKET;
                } else {
                    $this->submitAction = RouteType::CHECKOUT_PAYMENT_AND_SHIPPING;
                }
            }
        }
    }

    /**
     * Sets classList property for html container
     *
     * @return void
     */
    private function getClassList(): void {
        if ($this->routeType === RouteType::CHECKOUT_CONFIRM_ORDER) {
            $this->classList = 'confirmOrderButtons';
        } elseif ($this->routeType === RouteType::CHECKOUT_DENIED_ORDER) {
            $this->classList = 'deniedOrderButtons';
        }
    }

    /**
     * Sets basketNeedsShipping if basket has delivery
     *
     * @return void
     */
    private function getBasketNeedsShipping(): void {
        $this->basketNeedsShipping = false;
        if (!is_null($this->basket)) {
            foreach ($this->basket->getBasketWarnings() as $item) {
                if ($item->getCode() === BasketWarningCode::NEEDS_DELIVERY) {
                    $this->basketNeedsShipping = true;
                    break;
                }
            }
        }
    }

    /**
     * Sets basketNeedsPayment if basket has payment system
     *
     * @return void
     */
    private function getBasketNeedsPayment(): void {
        $this->basketNeedsPayment = false;
        if (!is_null($this->basket)) {
            foreach ($this->basket->getBasketWarnings() as $item) {
                if ($item->getCode() === BasketWarningCode::NEEDS_PAYMENTSYSTEM) {
                    $this->basketNeedsPayment = true;
                    break;
                }
            }
        }
    }

    /**
     * Sets basketAddressError if billing or shipping address are invalid
     *
     * @return void
     */
    public function getBasketAddressError(): void {
        $this->basketAddressError = false;
        if (!is_null($this->basket)) {
            foreach ($this->basket->getBasketWarnings() as $item) {
                if ($item->getCode() === BasketWarningCode::INVALID_BILLING_ADDRESS || $item->getCode() === BasketWarningCode::INVALID_SHIPPING_ADDRESS) {
                    $this->basketAddressError = true;
                    break;
                }
            }
        }
        if ($this->basketAddressError == false && $this->errorCode == 'ALLOW_DIFFERENT_COUNTRIES_ON_BILLING_AND_SHIPPING_ADDRESS_COUNTRY_ERROR') {
            $this->basketAddressError = true;
        }
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        return [
            'basket' => $this->basket,
            'showRecalculate' => $this->showRecalculate,
            'showContinue' => $this->showContinue,
            'showClear' => $this->showClear,
            'submitAction' => $this->submitAction,
            'classList' => $this->classList,
            'backLocation' => $this->backLocation,
            'forceDisabled' => $this->forceDisabled,
            'routeType' => $this->routeType,
            'validOutput' => $this->validOutput,
            'productsAreValid' => $this->productsAreValid,
            'basketNeedsShipping' => $this->basketNeedsShipping,
            'basketNeedsPayment' => $this->basketNeedsPayment,
            'basketAddressError' => $this->basketAddressError,
            'errorCode' => $this->errorCode
        ];
    }
}
