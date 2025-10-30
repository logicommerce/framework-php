<?php

namespace FWK\ViewHelpers\Basket\Macro;

use FWK\Core\Exceptions\CommerceException;
use FWK\Core\ViewHelpers\ViewHelper;
use SDK\Dtos\Basket\Basket;
use FWK\Core\Resources\Utils;
use SDK\Core\Dtos\ErrorFields;

/**
 * This is the DeniedOrderMessage class, a macro class for the basket viewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the basket's DeniedOrderMessage.
 *
 * @see DeniedOrderMessage::getViewParameters()
 *
 * @package FWK\ViewHelpers\Basket\Macro
 */
class DeniedOrderMessage {

    public ?Basket $basket = null;

    public string $errorCode = '';

    public ?ErrorFields $errorFields = null;

    /**
     * Constructor method for Buttons class.
     *
     * @see Buttons
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
        return $this->getProperties();
    }

    public function getFieldsLabels(): string {

        $errorCode = 'INVALID_REQUEST_BODY';
        $fieldsInErrorMessage = 0;
        $errorMessageLabels = '';
        if ($this->errorFields) {
            Utils::generateErrorMessages($this->errorFields->getItems(), '', $errorCode, $fieldsInErrorMessage, $errorMessageLabels);
            $errorMessageLabels .= ($fieldsInErrorMessage > 0) ? '</ul>' : '';
        }

        return $errorMessageLabels;
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        return [
            'basket' => $this->basket,
            'errorCode' => $this->errorCode,
            'errorFields' => $this->errorFields,
            'fieldsToDisplay' => $this->getFieldsLabels()
        ];
    }
}
