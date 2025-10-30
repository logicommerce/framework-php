<?php

namespace FWK\Core\Resources\Session;

use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Dtos\Common\Route;
use FWK\Core\Theme\Theme;

/**
 * This is the SessionGeneralSettings class.
 * The SessionGeneralSettings items will be stored in that class and will remain immutable (only get methods are available)
 *
 * @see SessionGeneralSettings::getLocale()
 * @see SessionGeneralSettings::getCurrency()
 * @see SessionGeneralSettings::getCountry()
 * @see SessionGeneralSettings::getLanguage()
 * @see SessionGeneralSettings::getDefaultRoute()
 * @see SessionGeneralSettings::getDefaultTheme()
 *
 * @see ElementTrait
 *
 * @package FWK\Core\Resources
 */
class SessionGeneralSettings {
    use ElementTrait;

    public const LOCALE = 'locale';

    public const CURRENCY = 'currency';

    public const COUNTRY = 'country';

    public const LANGUAGE = 'language';

    public const STORE_URL = 'storeURL';

    public const DEFAULT_THEME = 'defaultTheme';

    public const DEFAULT_ROUTE = 'defaultRoute';

    public const DEFAULT_AVAILABLE_LANGUAGES = 'defaultAvailableLanguages';

    private string $locale = '';

    private string $currency = '';

    private string $country = '';

    private string $language = '';

    private string $storeURL = '';

    private ?Route $defaultRoute = null;

    private ?Theme $defaultTheme = null;

    private array $defaultAvailableLanguages = [];

    /**
     * Returns the sessionGeneralSettings locale value.
     *
     * @return string
     */
    public function getLocale(): string {
        return $this->locale;
    }

    /**
     * Returns the sessionGeneralSettings currency value.
     *
     * @return string
     */
    public function getCurrency(): string {
        return $this->currency;
    }

    /**
     * Returns the sessionGeneralSettings country value.
     *
     * @return string
     */
    public function getCountry(): string {
        return $this->country;
    }

    /**
     * Returns the sessionGeneralSettings language value.
     *
     * @return string
     */
    public function getLanguage(): string {
        return $this->language;
    }

    /**
     * Returns the sessionGeneralSettings storeURL value.
     *
     * @return string
     */
    public function getStoreURL(): string {
        return $this->storeURL;
    }

    /**
     * Returns the sessionGeneralSettings defaultRoute value.
     *
     * @return NULL|Route
     */
    public function getDefaultRoute(): ?Route {
        return $this->defaultRoute;
    }

    /**
     * Returns the sessionGeneralSettings defaultTheme value.
     *
     * @return NULL|Theme
     */
    public function getDefaultTheme(): ?Theme {
        return $this->defaultTheme;
    }

    /**
     * Returns the sessionGeneralSettings defaultAvailableLanguages value.
     *
     * @return array
     */
    public function getDefaultAvailableLanguages(): array {
        return $this->defaultAvailableLanguages;
    }
}
