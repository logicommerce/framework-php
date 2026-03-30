<?php

namespace FWK\Dtos\Common;

use FWK\Dtos\Basket\PaymentSystem;
use FWK\Dtos\Documents\RichPrices\Payment;
use SDK\Core\Dtos\PluginProperties;
use SDK\Dtos\Common\Plugin as SDKPlugin;

/**
 * This is the Plugin class
 *
 * @see Plugin::getProperties()
 * @see Plugin::setProperties()
 *
 * @package FWK\Dtos\Common
 */
class PluginExpressCheckout extends SDKPlugin {

    protected ?PluginProperties $properties = null;

    protected array $paymentSystems = [];

    /**
     * Returns the Properties
     *
     * @return PluginProperties
     */
    public function getProperties(): PluginProperties {
        return $this->properties;
    }

    /**
     * Sets the Properties
     *
     * @param PluginProperties 
     */
    public function setProperties(PluginProperties $properties): void {
        $this->properties = $properties;
    }

    /**
     * Returns the payment systems
     *
     * @return array
     */
    public function getPaymentSystems(): array {
        return $this->paymentSystems;
    }

    /**
     * Sets the payment systems
     *
     * @param array $paymentSystems
     */
    public function setPaymentSystems(array $paymentSystems): void {
        $this->paymentSystems = $paymentSystems;
    }
}
