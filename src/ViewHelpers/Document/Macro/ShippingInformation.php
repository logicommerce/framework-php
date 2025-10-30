<?php

namespace FWK\ViewHelpers\Document\Macro;

use SDK\Dtos\Documents\Document;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\ViewHelpers\ViewHelper;

/**
 * This is the ShippingInformation class, a macro class for the document viewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the document's ShippingInformation.
 *
 * @see ShippingInformation::getViewParameters()
 *
 * @package FWK\ViewHelpers\Document\Macro
 */
class ShippingInformation {

    public ?Document $document = null;

    public array $fields = ["name", "nif", "address", "city", "state", "country", "email", "mobile"];

    public array $pickingFields = PickingInformation::FIELDS;

    /**
     * Constructor method for ShippingInformation class.
     *
     * @see ShippingInformation
     *
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        ViewHelper::mergeArguments($this, $arguments);
    }

    /**
     * This method returns all calculated arguments and new parameters for DocumentViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        if (is_null($this->document)) {
            throw new CommerceException("The value of [document] argument: '" . $this->document . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        } elseif (empty($this->fields)) {
            throw new CommerceException("The value of [fields] argument: '" . $this->document . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
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
            'document' => $this->document,
            'fields' => $this->fields,
            'pickingFields' => $this->pickingFields,
        ];
    }
}
