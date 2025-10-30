<?php

namespace FWK\ViewHelpers\Util\Macro;

use FWK\Core\Resources\Session;
use FWK\Core\Resources\Utils;
use FWK\Core\ViewHelpers\ViewHelper;
use SDK\Application;
use SDK\Core\Dtos\ElementCollection;
use SDK\Core\Resources\BatchRequests;
use SDK\Core\Resources\Cookie;
use SDK\Enums\CatalogStockPolicy;

/**
 * This is the CoreJsEnvironmentVariables class, a macro class for the utilViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the trackers output.
 *
 * @see CoreJsEnvironmentVariables::getViewParameters()
 *
 * @package FWK\ViewHelpers\Util\Macro
 */
class CoreJsEnvironmentVariables {

    private ?ElementCollection $countries = null;

    private ?ElementCollection $currencies = null;

    private string $device = '';

    private bool $catalogStockByWarehouse = false;

    private bool $pickup = false;

    private bool $avoidTrackings = false;

    public string $mobileAgents = '';

    public ?string $languageCode = null;

    public ?string $countryCode = null;

    /**
     * This static method adds to a batch request, a need variables. 
     * 
     * @param BatchRequests $request
     */
    public static function setBatchData(BatchRequests $request): void {
    }

    /**
     * Constructor method for CoreJsEnvironmentVariables
     *
     * @see CoreJsEnvironmentVariables
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
        $application = Application::getInstance();
        $session = Session::getInstance();
        // We can use languageCode from session because the cacheHash change for country or language
        $countryCode = !is_null($this->countryCode) ? $this->countryCode : $session->getGeneralSettings()->getCountry();
        $this->countries = $application->getCountriesSettings(!is_null($this->languageCode) ? $this->languageCode : $session->getGeneralSettings()->getLanguage());
        $this->currencies = $application->getCurrenciesSettings($countryCode);
        $this->device = Utils::getDevice();
        $this->catalogStockByWarehouse = $this->getCatalogStockByWarehouse($application);
        $this->pickup = false;
        $this->avoidTrackings = $this->isAvoidTrackings($application) ? 1 : 0;
        return $this->getProperties();
    }

    /*
     * Return if avoid trackings
     *
     * @param Application $applicationInstance
     *
     * @return bool
     */
    private function isAvoidTrackings(Application $applicationInstance): bool {
        $settings = $applicationInstance->getEcommerceSettings()->getLegalSettings();
        $useCookies = true;
        if (!is_null(Cookie::get('useCookies'))) {
            $useCookies = Cookie::get('useCookies');
        } else {
            $useCookies = $settings->getUseCookiesByDefault();
        }
        return $settings->getActiveGDPR() && !$useCookies;
    }

    /*
     * Return if the catalog stock policy is assigned by warehouse
     *
     * @param Application $applicationInstance
     *
     * @return bool
     */
    private function getCatalogStockByWarehouse(Application $applicationInstance): bool {
        $settings = $applicationInstance->getEcommerceSettings()->getStockSettings();
        if ($settings->getCatalogStockPolicy() === CatalogStockPolicy::BY_ASSIGNED_WAREHOUSES) {
            return true;
        }
        return false;
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        return [
            'countries' => $this->countries,
            'currencies' => $this->currencies,
            'device' => $this->device,
            'catalogStockByWarehouse' => $this->catalogStockByWarehouse,
            'pickup' => $this->pickup,
            'avoidTrackings' => $this->avoidTrackings,
            'mobileAgents' => $this->mobileAgents
        ];
    }
}
