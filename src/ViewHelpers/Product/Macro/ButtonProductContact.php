<?php

namespace FWK\ViewHelpers\Product\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;

/**
 * This is the ButtonProductContact class, a macro class for the productViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the product's contact button.
 *
 * @see ButtonProductContact::getViewParameters()
 *
 * @package FWK\ViewHelpers\Product\Macro
 */
class ButtonProductContact {

    public ?int $id = null;

    public string $class = '';

    public bool $showLabel = true;

    /**
     * Constructor method for ButtonProductContact class.
     * 
     * @see ButtonProductContact
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
            throw new CommerceException("The value of [id] argument: '" . $this->id . "' is required " . self::class, CommerceException::VIEW_HELPER_UNDEFINED_ARGUMENT);
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
            'class' => $this->class,
            'showLabel' => $this->showLabel,
        ];
    }
}
