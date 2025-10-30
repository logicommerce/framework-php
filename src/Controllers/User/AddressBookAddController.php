<?php

namespace FWK\Controllers\User;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\Controllers\Traits\AddDefaultCountryAndLocationsTrait;
use FWK\Core\Resources\Loader;
use FWK\Enums\Services;
use SDK\Core\Resources\BatchRequests;
use FWK\Core\Form\FormFactory;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Enums\Parameters;
use FWK\Services\UserService;

/**
 * This is the user address book add controller.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @controllerData: 
 *  <p>self::ADDRESS_FORM: \FWK\Core\Form\FormFactory::getAddress($type)</p>
 *  <p>self::PREFIX: string $type</p>
 *  <p>self::DEFAULT_SELECTED_COUNTRY: \SDK\Dtos\Settings\CountrySettings</p>
 *  <p>self::DEFAULT_SELECTED_COUNTRY_LOCATIONS: array of \SDK\Dtos\CountryLocation</p>
 * 
 * @twigContent: \themes\{{themeName}}\{{version}}\Content\User\AddressBookAdd\default.html.twig
 * 
 * @RouteType: \SDK\Enums\RouteType::USER_ADDRESS_BOOK_ADD
 * 
 * @see AddDefaultCountryAndLocationsTrait
 * 
 * @package FWK\Controllers\User
 */
class AddressBookAddController extends BaseHtmlController {
    use AddDefaultCountryAndLocationsTrait;

    public const ADDRESS_FORM = 'addressForm';

    public const PREFIX = 'prefix';

    public const DEFAULT_SELECTED_COUNTRY = 'defaultSelectedCountry';

    public const DEFAULT_SELECTED_COUNTRY_LOCATIONS = 'defaultSelectedCountryLocations';

    private ?UserService $userService = null;

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FilterInputFactory::getAddressTypeParameter();
    }

    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
        $this->userService = Loader::service(Services::USER);
    }

    /**
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     *
     */
    protected function setControllerBaseData(): void {
        $type = $this->getRequestParam(Parameters::TYPE, true);
        $this->setDataValue(self::PREFIX, $type);
        $this->setDataValue(self::ADDRESS_FORM, FormFactory::getAddress($type));
        $this->setDataValue(self::DEFAULT_SELECTED_COUNTRY, $this->getDefaultCountry());
        $this->setDataValue(self::DEFAULT_SELECTED_COUNTRY_LOCATIONS, $this->getDefaultCountryLocations());
    }

    /**
     * This method runs after the batch requests (defined in the setBatchData methods) are resolved,
     * so here you can work with the response of the batch requests and calculate and set more needed data.
     *
     * @param array $additionalData
     *              Set additiona data to the controller data
     * 
     * @return void
     */
    protected function setData(array $additionalData = []): void {
    }

    /**
     * This method is the one in charge of defining all the data batch requests that
     * are needed for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     * @return void
     */
    protected function setBatchData(BatchRequests $request): void {
    }
}
