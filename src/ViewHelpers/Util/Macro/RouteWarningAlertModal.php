<?php

namespace FWK\ViewHelpers\Util\Macro;

use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Form\FormFactory;
use SDK\Application;
use FWK\Core\ViewHelpers\ViewHelper;
use SDK\Dtos\Common\Route;
use SDK\Services\Parameters\Groups\Settings\CountriesLinksParametersGroup;

/**
 * This is the RouteWarningAlertModal class, a macro class for the utilViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the RouteWarningAlertModal output.
 *
 * @see RouteWarningAlertModal::getViewParameters()
 *
 * @package FWK\ViewHelpers\Util\Macro
 */
class RouteWarningAlertModal {

    public ?Route $route = null;

    public bool $showCountriesLinksForm = false;

    public bool $countriesLinksFormAcceptRouteWarning = true;

    public string $countriesLinksFormClass = '';

    /**
     * Constructor method for RouteWarningAlertModal
     *
     * @see RouteWarningAlertModal
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
        if (is_null($this->route)) {
            throw new CommerceException("The value of [route] argument: '" . $this->route . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }
        return $this->getProperties();
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        $countriesLinksParametersGroup = new CountriesLinksParametersGroup();
        $countriesLinksParametersGroup->setLanguageCode($this->route->getLanguage());
        $countriesLinksParametersGroup->setAllCountries(true);
        $countriesLinks = Application::getInstance()->getCountriesLinks($countriesLinksParametersGroup);
        $countriesLinksForm = FormFactory::getCountriesLinks($countriesLinks, $this->route->getWarning()?->getCountry(), $this->route->getWarning()?->getLanguage());
        return [
            'route' => $this->route,
            'showCountriesLinksForm' => $this->showCountriesLinksForm,
            'countriesLinksForm' => $countriesLinksForm,
            'countriesLinksFormAcceptRouteWarning' => $this->countriesLinksFormAcceptRouteWarning,
            'countriesLinksFormClass' => $this->countriesLinksFormClass
        ];
    }
}
