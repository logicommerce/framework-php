<?php

namespace FWK\ViewHelpers\Product\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Resources\Loader;
use FWK\Enums\Services;
use SDK\Core\Dtos\ElementCollection;
use SDK\Enums\PluginConnectorType;

/**
 * This is the BuyFormSubmit class, a macro class for the productViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the product's buy form submit.
 *
 * @see BuyFormSubmit::getViewParameters()
 *
 * @package FWK\ViewHelpers\Product\Macro
 */
class BuyFormSubmit {

    private const ADD_SPECIAL_PRODUCT = 'addSpecialProduct';

    private const FORM_BUTTON_HOOK_VALUES = [
        self::ADD_SPECIAL_PRODUCT
    ];

    public ?int $id = null;

    public ?bool $showOrderBox = null;

    public string $class = '';

    public bool $showLabel = true;

    public string $formButtonHook = '';

    public bool $expressCheckout = true;

    protected ?ElementCollection $expressCheckoutPlugins = null;

    /**
     * Constructor method for BuyFormSubmit class.
     * 
     * @see BuyFormSubmit
     * 
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        ViewHelper::mergeArguments($this, $arguments);
    }

    /**
     * This method returns all calculated arguments and new parameters for ProductViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        if (is_null($this->id)) {
            throw new CommerceException("The value of [id] argument: '" . $this->id . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }

        if (is_null($this->showOrderBox)) {
            throw new CommerceException("The value of [showOrderBox] argument: '" . $this->showOrderBox . "' is required! (showOrderBox: product.definition.showOrderBox) " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }

        if (!in_array($this->formButtonHook, self::FORM_BUTTON_HOOK_VALUES, true) && strlen($this->formButtonHook) > 0) {
            throw new CommerceException("The value of [formButtonHook] argument: '" . $this->formButtonHook . "' not exists in " . self::class, CommerceException::VIEW_HELPER_UNDEFINED_ARGUMENT);
        }
        if ($this->expressCheckout) {
            $this->expressCheckoutPlugins = Loader::service(Services::PLUGIN)->getExpressCheckoutPlugins();
        }

        return $this->getProperties();
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        return [
            'id' => $this->id,
            'showOrderBox' => $this->showOrderBox,
            'class' => $this->class,
            'showLabel' => $this->showLabel,
            'formButtonHook' => $this->formButtonHook,
            'expressCheckout' => $this->expressCheckout,
            'expressCheckoutPlugins' => $this->expressCheckoutPlugins
        ];
    }
}
