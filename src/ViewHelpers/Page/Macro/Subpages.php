<?php

namespace FWK\ViewHelpers\Page\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;

/**
 * This is the Subpages class, a macro class for the PageViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the user's Subpages.
 *
 * @see Subpages::getViewParameters()
 *
 * @package FWK\ViewHelpers\Page\Macro
 */
class Subpages {

    public array $subpages = [];

    /**
     * Constructor method for Subpages.
     * 
     * @see Subpages
     * 
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        ViewHelper::mergeArguments($this, $arguments);
    }

    /**
     * This method returns all calculated arguments and new parameters for PageViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        if (is_null($this->subpages)) {
            throw new CommerceException("The value of [form] argument: '" . $this->subpages . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
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
            'subpages' => $this->subpages
        ];
    }
}
