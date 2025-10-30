<?php

namespace FWK\ViewHelpers\User\Macro;

use FWK\Core\Theme\Dtos\ApplicableFilters;
use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;

/**
 * This is the FilterShoppingListRowsForm class, a macro class for the userViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the user's FilterShoppingListRowsForm.
 *
 * @see FilterShoppingListRowsForm::getViewParameters()
 *
 * @package FWK\ViewHelpers\User\Macro
 */
class FilterShoppingListRowsForm {

    public ?ApplicableFilters $applicableFilters = null;

    public array $appliedFilters = [];

    public array $defaultParametersValues = [];

    public bool $autosubmit = false;

    /**
     * Constructor method for Filter class.
     *
     * @see Filter
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
        if (is_null($this->applicableFilters)) {
            throw new CommerceException("The value of [applicableFilters] argument: '" . $this->applicableFilters . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
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
            'applicableFilters' => $this->applicableFilters,
            'appliedFilters' => $this->appliedFilters,
            'defaultParametersValues' => $this->defaultParametersValues,
            'autosubmit' => $this->autosubmit
        ];
    }
}
