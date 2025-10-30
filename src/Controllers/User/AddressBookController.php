<?php

namespace FWK\Controllers\User;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\Controllers\Traits\AddDefaultCountryAndLocationsTrait;
use FWK\Core\Form\FormFactory;
use FWK\Core\Resources\Loader;
use FWK\Enums\Services;
use SDK\Core\Resources\BatchRequests;
use FWK\Services\UserService;
use SDK\Dtos\Common\Route;

/**
 * This is the user address book controller.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @controllerData: 
 *  <p>self::BILLING_ADDRESSES: \SDK\Core\Dtos\ElementCollection of \SDK\Dtos\User\BillingAddress</p>
 *  <p>self::SHIPPING_ADDRESSES: \SDK\Core\Dtos\ElementCollection of \SDK\Dtos\User\ShippingAddress</p>
 *  <p>self::USER_ADDRESS_BOOK_FORM: \FormFactory::setUser(FormFactory::SET_USER_TYPE_ADD_USER, $this->getSession()->getUser())</p>
 *  <p>self::DEFAULT_SELECTED_COUNTRY: \SDK\Dtos\Settings\CountrySettings</p>
 *  <p>self::DEFAULT_SELECTED_COUNTRY_LOCATIONS: array of \SDK\Dtos\CountryLocation</p>
 * 
 * @twigContent: \themes\{{themeName}}\{{version}}\Content\User\AddressBook\default.html.twig
 * 
 * @RouteType: \SDK\Enums\RouteType::USER_ADDRESS_BOOK
 * 
 * @see AddDefaultCountryAndLocationsTrait
 * 
 * @package FWK\Controllers\User
 */
class AddressBookController extends BaseHtmlController {
    use AddDefaultCountryAndLocationsTrait;

    private const BILLING_ADDRESSES = 'billingAddresses';

    private const SHIPPING_ADDRESSES = 'shippingAddresses';

    private ?UserService $userService = null;

    public const USER_ADDRESS_BOOK_FORM = 'userAddressBookForm';

    public const DEFAULT_SELECTED_COUNTRY = 'defaultSelectedCountry';

    public const DEFAULT_SELECTED_COUNTRY_LOCATIONS = 'defaultSelectedCountryLocations';

    /**
     * Constructor method.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->userService = Loader::service(Services::USER);
    }

    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
        $this->userService->addGetBillingAddresses($requests, self::BILLING_ADDRESSES);
        $this->userService->addGetShippingAddresses($requests, self::SHIPPING_ADDRESSES);
    }

    /**
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     *
     */
    protected function setControllerBaseData(): void {
        $this->setDataValue(self::USER_ADDRESS_BOOK_FORM, FormFactory::setUser(FormFactory::SET_USER_TYPE_ADD_USER, $this->getSession()->getUser()));
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
