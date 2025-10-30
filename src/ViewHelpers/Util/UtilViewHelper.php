<?php

namespace FWK\ViewHelpers\Util;

use FWK\Core\Resources\Loader;
use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Enums\Services;
use FWK\ViewHelpers\Util\Macro\AgencyLogo;
use FWK\ViewHelpers\Util\Macro\Breadcrumb;
use FWK\ViewHelpers\Util\Macro\CoreJsEnvironmentVariables;
use FWK\ViewHelpers\Util\Macro\CountriesLinksForm;
use FWK\ViewHelpers\Util\Macro\ExpressCheckout;
use FWK\ViewHelpers\Util\Macro\FillDataFunction;
use FWK\ViewHelpers\Util\Macro\Kimera;
use FWK\ViewHelpers\Util\Macro\LcCommerceData;
use FWK\ViewHelpers\Util\Macro\LcCommerceSession;
use FWK\ViewHelpers\Util\Macro\Pagination;
use FWK\ViewHelpers\Util\Macro\PhysicalLocations;
use FWK\ViewHelpers\Util\Macro\PhysicalLocationsFilter;
use FWK\ViewHelpers\Util\Macro\PluginsAssets;
use FWK\ViewHelpers\Util\Macro\PrintableContent;
use FWK\ViewHelpers\Util\Macro\SearchForm;
use FWK\ViewHelpers\Util\Macro\Trackers;
use FWK\ViewHelpers\Util\Macro\ViewSection;
use FWK\ViewHelpers\Util\Macro\PhysicalLocationsForm;
use FWK\ViewHelpers\Util\Macro\RouteWarningAlertModal;
use FWK\ViewHelpers\Util\Macro\Webhook;
use SDK\Enums\PluginConnectorType;

/**
 * This is the UtilViewHelper class.
 * The purpose of this class is to facilitate to Twig the generation of view output (providing some useful methods).
 * <br>This class extends ViewHelper, see this class.
 *
 * @see ViewHelper
 *
 * @see UtilViewHelper::agencyLogoMacro()
 * @see UtilViewHelper::breadcrumbMacro()
 * @see UtilViewHelper::coreJsEnvironmentVariables()
 * @see UtilViewHelper::countriesLinksFormMacro() 
 * @see UtilViewHelper::fillDataFunction()
 * @see UtilViewHelper::getPriceDifference()
 * @see UtilViewHelper::lcCommerceData()
 * @see UtilViewHelper::lcCommerceSession()
 * @see UtilViewHelper::paginationMacro()
 * @see UtilViewHelper::physicalLocationsForm()
 * @see UtilViewHelper::physicalLocationsFilter()
 * @see UtilViewHelper::physicalLocations()
 * @see UtilViewHelper::pluginsAssetsMacro()
 * @see UtilViewHelper::printableContentMacro()
 * @see UtilViewHelper::routeWarningAlertModalMacro()
 * @see UtilViewHelper::searchFormMacro()
 * @see UtilViewHelper::trackersMacro()
 * @see UtilViewHelper::viewSection()
 *
 * @package FWK\ViewHelpers\Util
 */
class UtilViewHelper extends ViewHelper {

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the agencyLogo macro.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>color</li>
     * <li>showOnlyText</li>
     * <li>linkRel</li>
     * <li>name</li>
     * <li>id</li>
     * <li>class</li>
     * <li>classLink</li>
     * <li>link</li>
     * <li>logo</li>
     * <li>folder</li>
     * <li>logoImgSrc</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *
     * @return array
     */
    public function agencyLogoMacro(array $arguments = []): array {
        $agencyLogo = new AgencyLogo($arguments);
        return $agencyLogo->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the breadcrumb macro.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>showHome</li>
     * <li>showArea</li>
     * <li>maxLevels</li>
     * <li>tag</li>
     * <li>data</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *
     * @return array
     */
    public function breadcrumbMacro(array $arguments = []): array {
        $breadcrumb = new Breadcrumb($arguments);
        return $breadcrumb->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the pagination macro.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>pagination</li>
     * <li>pagerParameters</li>
     * <li>paginationItems</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *
     * @return array
     */
    public function paginationMacro(array $arguments = []): array {
        $pagination = new Pagination($arguments);
        return $pagination->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the searchForm macro.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>form</li>
     * <li>showLabel</li>
     * <li>showPlaceholder</li>
     * <li>minCharacters</li>
     * <li>searchProducts</li>
     * <li>searchCategories</li>
     * <li>searchBlog</li>
     * <li>searchPages</li>
     * <li>searchNews</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *
     * @return array
     */
    public function searchFormMacro(array $arguments = []): array {
        $searchForm = new SearchForm($arguments);
        return $searchForm->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the lcCommerceData macro.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>route</li>
     * <li>pageProduct</li>
     * <li>pageCategory</li>
     * <li>pageCategoryProducts</li>
     * <li>pageProducts</li>
     * <li>order</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *
     * @return array
     */
    public function lcCommerceDataMacro(array $arguments = []): array {
        $lcCommerceData = new LcCommerceData($arguments);
        return $lcCommerceData->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the lcCommerceSession macro.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>session</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *
     * @return array
     */
    public function lcCommerceSessionMacro(array $arguments = []): array {
        $lcCommerceSession = new LcCommerceSession($arguments);
        return $lcCommerceSession->getViewParameters();
    }

    /**
     * This method gets the price difference between the given basePrice and the given retailPrice and returns it in an array with these keys:
     * <ul>
     * <li>priceDifference</li>
     * <li>percentDifference</li>
     * <li>roundedPercentDifference</li>
     * </ul>
     *
     * @param float $basePrice
     * @param float $retailPrice
     * @param int $roundDecimals
     *
     * @return array
     */
    public function getPriceDifference(float $basePrice, float $retailPrice, int $roundDecimals = 0): array {
        $priceDifference = 0;
        $percentDifference = 0;
        $roundedPercentDifference = 0;

        $lowestPrice = 0;
        $highestPrice = 0;

        if ($basePrice > $retailPrice) {
            $lowestPrice = $retailPrice;
            $highestPrice = $basePrice;
        } else {
            $lowestPrice = $basePrice;
            $highestPrice = $retailPrice;
        }
        $priceDifference = $highestPrice - $lowestPrice;

        if ($priceDifference > 0) {
            $percentDifference = $priceDifference * 100 / $highestPrice;
        }

        if ($roundDecimals > 0) {
            $roundedPercentDifference = number_format($percentDifference, $roundDecimals);
        } else {
            $roundedPercentDifference = round($percentDifference);
        }

        return [
            'priceDifference' => $priceDifference,
            'percentDifference' => $percentDifference,
            'roundedPercentDifference' => $roundedPercentDifference
        ];
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the trackers macro.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>trackers</li>
     * <li>pageType</li>
     * <li>ambience</li>
     * <li>position</li>
     * <li>type</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *
     * @return array
     */
    public function trackersMacro(array $arguments = []): array {
        $trackers = new Trackers($arguments);
        return $trackers->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the kimera macro.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>kimera</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *
     * @return array
     */
    public function kimeraMacro(array $arguments = []): object {
        return new Kimera($arguments);
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the pluginsAssets macro.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>assets</li>
     * <li>pageType</li>
     * <li>ambience</li>
     * <li>position</li>
     * <li>type</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *
     * @return array
     */
    public function pluginsAssetsMacro(array $arguments = []): array {
        $pluginsAssets = new PluginsAssets($arguments);
        return $pluginsAssets->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the printableContent macro.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>windowAttributes</li>
     * <li>content</li>
     * <li>hrefType</li>
     * <li>title</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *
     * @return array
     */
    public function printableContentMacro(array $arguments = []): array {
        $printableContent = new PrintableContent($arguments);
        return $printableContent->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the printableContent macro.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>route</li>
     * <li>showCountriesLinksForm</li>
     * <li>countriesLinksFormClass</li>
     * <li>countriesLinksFormAcceptRouteWarning</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *
     * @return array
     */
    public function routeWarningAlertModalMacro(array $arguments = []): array {
        $routeWarningAlertModal = new RouteWarningAlertModal($arguments);
        return $routeWarningAlertModal->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the fillDataFunction macro.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>user</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *
     * @return array
     */
    public function fillDataFunction(array $arguments = []): array {
        $fillDataFunction = new FillDataFunction($arguments);
        return $fillDataFunction->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the coreJsEnvironmentVariables macro.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>mobileAgents</li>
     * <li>languageCode</li>
     * <li>countryCode</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *
     * @return array
     */
    public function coreJsEnvironmentVariables(?array $arguments = []): array {
        $coreJsEnvironmentVariables = new CoreJsEnvironmentVariables(!is_null($arguments) ? $arguments : []);
        return $coreJsEnvironmentVariables->getViewParameters();
    }


    /**
     * This method merges the given arguments, calculates and returns the view parameters for the countriesLinksForm macro.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>class</li>
     * <li>countriesLinksForm</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *
     * @return array
     */
    public function countriesLinksFormMacro(?array $arguments = []): array {
        $countriesLinksMacro = new CountriesLinksForm(!is_null($arguments) ? $arguments : []);
        return $countriesLinksMacro->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the expressCheckout macro.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>class</li>
     * <li>showTitle</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *
     * @return array
     */
    public function expressCheckoutMacro(?array $arguments = []): array {
        $expressCheckoutMacro = new ExpressCheckout(!is_null($arguments) ? $arguments : []);
        return $expressCheckoutMacro->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the webhook macro.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>class</li>
     * <li>showTitle</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *
     * @return array
     */
    public function webhookMacro(?array $arguments = []): array {
        $webhookMacro = new Webhook(!is_null($arguments) ? $arguments : []);
        return $webhookMacro->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the viewSection macro.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>user</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *
     * @return array
     */
    public function viewSection(array $arguments = []): array {
        $viewSection = new ViewSection($arguments);
        return $viewSection->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the physicalLocationsForm macro.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>form</li>
     * <li>levels</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *
     * @return array
     */
    public function physicalLocationsForm(array $arguments = []): array {
        $physicalLocationsForm = new PhysicalLocationsForm($arguments);
        return $physicalLocationsForm->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the physicalLocationsFilter macro.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>physicalLocationsFilter</li>
     * <li>pickupPointProviders</li>
     * <li>countries</li>
     * <li>defaultCountry</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *
     * @return array
     */
    public function physicalLocationsFilter(array $arguments = []): array {
        $physicalLocationsFilter = new PhysicalLocationsFilter($arguments, $this->languageSheet);
        return $physicalLocationsFilter->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the physicalLocations macro.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>changeOptionFunction</li>
     * <li>physicalLocations</li>
     * <li>addCountrySelector</li>
     * <li>addStateSelector</li>
     * <li>addCitySelector</li>
     * <li>addPostalCodeSelector</li>
     * <li>addAllOption</li>
     * <li>showInMap</li>
     * <li>mapsApiKey</li>
     * <li>optionName</li>
     * <li>optionClass</li>
     * <li>defaultPhysicalLocationId</li>
     * <li>showDirections</li>
     * <li>physicalLocationFields</li>
     * <li>showPlacesAutocomplete</li>
     * <li>showAllMapMarkersButton</li>
     * <li>showPickupPointProviderMapMarkers</li>
     * <li>filterByPhysicalLocations</li>
     * <li>searchResultZoom</li>
     * <li>searchMinResults</li>
     * <li>searchMaxResults</li>
     * <li>physicalLocationItemsCheckVisibility</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *
     * @return array
     */
    public function physicalLocations(array $arguments = []): array {
        $physicalLocations = new PhysicalLocations($arguments);
        return $physicalLocations->getViewParameters();
    }
}
