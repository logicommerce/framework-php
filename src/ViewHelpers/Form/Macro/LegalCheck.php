<?php

namespace FWK\ViewHelpers\Form\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;

/**
 * This is the LegalCheck class, a macro class for the userViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the user's legal check.
 *
 * @see LegalCheck::getViewParameters()
 *
 * @package FWK\ViewHelpers\Form\Macro
 */
class LegalCheck {

    public const CHECKBOX_POSITION_BEFORE = 'before';

    public const CHECKBOX_POSITION_AFTER = 'after';

    private const CHECKBOX_POSITION_VALUES = [
        self::CHECKBOX_POSITION_BEFORE,
        self::CHECKBOX_POSITION_AFTER
    ];

    public string $checkboxPosition = self::CHECKBOX_POSITION_BEFORE;

    public bool $showCheck = true;

    /**
     * Constructor method for LegalCheck
     * 
     * @see LegalCheck
     * 
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        ViewHelper::mergeArguments($this, $arguments);
    }

    /**
     * This method returns all calculated arguments and new parameters for UserViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        if (!in_array($this->checkboxPosition, self::CHECKBOX_POSITION_VALUES, true)) {
            throw new CommerceException("The value of [checkboxPosition] argument: '" . $this->checkboxPosition . "' not exists in " . self::class, CommerceException::VIEW_HELPER_UNDEFINED_ARGUMENT);
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
            'checkboxPosition' => $this->checkboxPosition,
            'showCheck' => $this->showCheck
        ];
    }
}