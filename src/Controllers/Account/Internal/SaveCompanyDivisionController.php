<?php

namespace FWK\Controllers\Account\Internal;

use FWK\Controllers\Account\AccountRegisteredUserCreateController;
use FWK\Core\Controllers\Traits\AddDefaultCountryAndLocationsTrait;
use FWK\Core\Form\FormFactory;
use FWK\Core\Resources\Loader;
use FWK\Enums\Services;
use FWK\Services\AccountService;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Enums\Parameters;
use FWK\Services\LmsService;
use SDK\Dtos\Common\Route;
use SDK\Services\Parameters\Groups\Account\CompanyDivisionsParametersGroup;

class SaveCompanyDivisionController extends AccountRegisteredUserCreateController {
    use AddDefaultCountryAndLocationsTrait;

    public const SAVE_COMPANY_DIVISION_FORM = "saveCompanyDivisionForm";
    public const SELECTED_COUNTRY = "selectedCountry";
    public const SELECTED_COUNTRY_LOCATIONS = "selectedCountryLocations";
    public const LOCATION_MODE = "locationMode";
    public const ACCOUNT_ID = "accountId";

    protected ?AccountService $accountService = null;

    protected ?CompanyDivisionsParametersGroup $companyDivisionsParametersGroup = null;
    
    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->accountService = Loader::service(Services::ACCOUNT);
        $this->companyDivisionsParametersGroup = new CompanyDivisionsParametersGroup();
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FormFactory::getAccountCompanyDivisionCreate()->getInputFilterParameters();
    }

    protected function getOriginParams() {
        return FilterInputHandler::PARAMS_FROM_GET;
    }

    /**
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     */
    protected function setControllerBaseData(): void {
        $aux[self::SAVE_COMPANY_DIVISION_FORM] = FormFactory::getAccountCompanyDivisionCreate(
            $this->companyDivisionsParametersGroup,
            $this->getRequestParam(Parameters::ID, true),
            $this->getControllerData(self::COMPANY_ROLES)->getItems()
        );
        $aux[self::SELECTED_COUNTRY] = $this->getDefaultCountry();
        $aux[self::SELECTED_COUNTRY_LOCATIONS] = $this->getDefaultCountryLocations();
        $aux[self::LOCATION_MODE] = $this->getLocationMode();
        $aux[self::ACCOUNT_ID] = $this->getRequestParam(Parameters::ID, true);

        $this->setDataValue(self::CONTROLLER_ITEM, $aux);
    }

    /**
     * Get location mode based on available LMS licenses
     * 
     * @return string
     */
    private function getLocationMode(): string {
        if (LmsService::getLocationSearchZipCityLicense()) {
            return LmsService::LOCATION_SEARCH_ZIP_CITY;
        } elseif (LmsService::getLocationSearchCityLicense()) {
            return LmsService::LOCATION_SEARCH_CITY;
        }
        return '';
    }
}
