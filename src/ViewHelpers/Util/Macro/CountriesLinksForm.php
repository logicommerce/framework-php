<?php

namespace FWK\ViewHelpers\Util\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Form\Form;

/**
 * This is the CountriesLinksForm class, a macro class for the utilViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the util's CountriesLinksForm.
 *
 * @see CountriesLinks::getViewParameters()
 *
 * @package FWK\ViewHelpers\Util\Macro
 */
class CountriesLinksForm {

    public string $class = '';

    public ?Form $countriesLinksForm = null;

    public bool $acceptRouteWarning = false;

    /**
     * Constructor method for CountriesLinksForm
     *
     * @see CountriesLinks
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
        if (is_null($this->countriesLinksForm)) {
            throw new CommerceException("The value of [countriesLinksForm] argument: '" . $this->countriesLinksForm . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
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
            'class' => $this->class,
            'countriesLinksForm' => $this->countriesLinksForm,
            'acceptRouteWarning' => $this->acceptRouteWarning
        ];
    }
}
