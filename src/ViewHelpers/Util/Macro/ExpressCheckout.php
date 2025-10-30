<?php

namespace FWK\ViewHelpers\Util\Macro;

use FWK\Core\Resources\Loader;
use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Enums\Services;
use SDK\Core\Dtos\ElementCollection;
use SDK\Enums\PluginConnectorType;

/**
 * This is the ExpressCheckout class, a macro class for the utilViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the ExpressCheckout output.
 *
 * @see ExpressCheckout::getViewParameters()
 *
 * @package FWK\ViewHelpers\Util\Macro
 */
class ExpressCheckout {

    public bool $showTitle = false;

    public string $class = '';

    protected ?ElementCollection $expressCheckoutPlugins = null;

    /**
     * Constructor method for ExpressCheckout
     *
     * @see ExpressCheckout
     *
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        ViewHelper::mergeArguments($this, $arguments);
    }

    /**
     * This method returns all calculated arguments and new parameters for UtilViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        $this->expressCheckoutPlugins = Loader::service(Services::PLUGIN)->getExpressCheckoutPlugins();
        return $this->getProperties();
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        return [
            'expressCheckoutPlugins' => $this->expressCheckoutPlugins,
            'showTitle' => $this->showTitle,
            'class' => $this->class
        ];
    }
}
