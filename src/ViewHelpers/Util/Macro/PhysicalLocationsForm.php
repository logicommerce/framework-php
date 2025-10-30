<?php

namespace FWK\ViewHelpers\Util\Macro;

use FWK\Core\Form\Form;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\ViewHelpers\ViewHelper;

/**
 * This is the PhysicalLocationsForm class, a macro class for the utilViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the PhysicalLocationsForm output.
 *
 * @see PhysicalLocationsForm::getViewParameters()
 *
 * @package FWK\ViewHelpers\Util\Macro
 */
class PhysicalLocationsForm {

    public ?Form $form = null;

    public int $levels = 1;

    /**
     * Constructor method for PhysicalLocationsForm
     *
     * @see PhysicalLocationsForm
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
        if ($this->levels < 1 || $this->levels > 3) {
            throw new CommerceException("The value of [levels] argument: '" . $this->levels . "' is not valid. The valid values are numbers between 1 and 3 in " . self::class, CommerceException::VIEW_HELPER_INVALID_ARGUMENT);
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
            'form' => $this->form,
            'levels' => $this->levels
        ];
    }
}
