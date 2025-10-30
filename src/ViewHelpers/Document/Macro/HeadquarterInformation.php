<?php

namespace FWK\ViewHelpers\Document\Macro;

use SDK\Dtos\Documents\Document;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\ViewHelpers\ViewHelper;

/**
 * This is the Headquarter Information class, a macro class for the document viewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the document's Headquarter Information.
 *
 * @see HeadquarterInformation::getViewParameters()
 *
 * @package FWK\ViewHelpers\Document\Macro
 */
class HeadquarterInformation {

    public ?Document $document = null;

    /**
     * Constructor method for HeadquarterInformation class.
     *
     * @see HeadquarterInformation
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
            'document' => $this->document
        ];
    }
}
