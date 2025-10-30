<?php

namespace FWK\ViewHelpers\Document\Macro;

use SDK\Dtos\Documents\Document;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\ViewHelpers\ViewHelper;

/**
 * This is the PickingInformation class, a macro class for the document viewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the document's PickingInformation.
 *
 * @see PickingInformation::getViewParameters()
 *
 * @package FWK\ViewHelpers\Document\Macro
 */
class PickingInformation {

    public const FIELDS = ["name", "address", "city", "state", "country", "phone", "email"];

    public ?Document $document = null;

    public array $fields = self::FIELDS;

    /**
     * Constructor method for PickingInformation class.
     *
     * @see PickingInformation
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
            'fields' => $this->fields
        ];
    }
}
