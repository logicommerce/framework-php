<?php

namespace FWK\ViewHelpers\Basket\Macro;

use SDK\Dtos\Basket\Basket;
use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;

/**
 * This is the VoucherForm class, a macro class for the basket viewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the basket's voucher form.
 *
 * @see VoucherForm::getViewParameters()
 *
 * @package FWK\ViewHelpers\Basket\Macro
 */
class VoucherForm {

    public ?Basket $basket = null;

    public bool $forceOutput = false;

    private int $basketItems = 0;

    /**
     * Constructor method for VoucherForm class.
     *
     * @see VoucherForm
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

        $this->basketItems = count($this->basket->getItems());

        return $this->getProperties();
    }

    protected function getProperties(): array {
        return [
            'forceOutput' => $this->forceOutput,
            'basketItems' => $this->basketItems
        ];
    }
}