<?php

namespace FWK\Controllers\Checkout;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\Controllers\Traits\AddDefaultCountryAndLocationsTrait;
use FWK\Core\Controllers\Traits\AddPluginPaymentSystemTrait;
use FWK\Core\Controllers\Traits\CheckoutRedirectTrait;
use FWK\Core\Controllers\Traits\SetPhysicalLocationsFromDeliveries;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Form\FormFactory;
use FWK\Enums\Services;
use FWK\Core\Resources\Loader;
use FWK\Core\Resources\Utils;
use FWK\Services\AccountService;
use SDK\Core\Resources\BatchRequests;
use SDK\Core\Resources\Cookie;

use FWK\Services\BasketService;
use FWK\Services\PluginService;
use FWK\Services\SettingsService;
use FWK\Services\UserService;
use SDK\Core\Dtos\ElementCollection;
use SDK\Dtos\Common\Route;
use SDK\Enums\MasterType;
use SDK\Services\Parameters\Groups\Document\PickupPointProvidersParametersGroup;

/**
 * This is the checkout controller.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @see BaseHtmlController
 * @see AddDefaultCountryAndLocationsTrait
 * @See AddPluginPaymentSystemsTrait
 *
 * @package FWK\Controllers\Checkout
 */
class CheckoutController extends BaseHtmlController {
    use AddDefaultCountryAndLocationsTrait, SetPhysicalLocationsFromDeliveries, AddPluginPaymentSystemTrait;

    use CheckoutRedirectTrait {
        __construct as __constructCheckoutRedirectTrait;
    }

    protected const BASKET = 'basket';

    protected const COMPANY_ROLE = 'companyRole';

    protected const DELIVERIES = 'deliveries';

    protected const PICKING_DELIVERIES = 'pickingDeliveries';

    protected const SHIPPING_DELIVERIES = 'shippingDeliveries';

    protected const PICKUP_POINT_PROVIDERS = 'pickupPointProviders';

    protected const SELECTED_PROVIDER_PICKUP_POINT = 'selectedProviderPickupPoint';

    protected const PAYMENT_SYSTEMS = 'paymentSystems';

    protected const USER_FORM = 'userForm';

    protected const CUSTOMER_FORM = 'customerForm';

    protected const BILLING_ADDRESSES = 'billingAddresses';

    protected const SHIPPING_ADDRESSES = 'shippingAddresses';

    protected const DEFAULT_SELECTED_COUNTRY = 'defaultSelectedCountry';

    public const DEFAULT_SELECTED_COUNTRY_LOCATIONS = 'defaultSelectedCountryLocations';

    public const PHYSICAL_LOCATIONS = 'physicalLocations';

    public const DEFAULT_PHYSICAL_LOCATION_ID = 'defaultPhysicalLocationId';

    public const USER_CUSTOM_TAGS = 'userCustomTags';

    private ?PluginService $pluginService = null;

    private ?ElementCollection $paymentSystemPlugins = null;

    private ?BasketService $basketService = null;

    private ?UserService $userService = null;

    private ?AccountService $accountService = null;

    private ?SettingsService $settingsService = null;

    private bool $useOSCAsync = false;

    protected ?PickupPointProvidersParametersGroup $pickupPointProvidersParametersGroup = null;

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        $this->__constructCheckoutRedirectTrait($route);
        $this->basketService = Loader::service(Services::BASKET);
        $this->userService = Loader::service(Services::USER);
        $this->accountService = Loader::service(Services::ACCOUNT);
        $this->settingsService = Loader::service(Services::SETTINGS);
    }

    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
        $this->useOSCAsync = $this->getTheme()->getConfiguration()->getCommerce()->getUseOSCAsync();
        if (Cookie::exist('basketToken') && !$this->useOSCAsync) {
            $this->basketService->addGetPhysicalLocationPickingDeliveries($requests, self::PICKING_DELIVERIES);
            $this->basketService->addGetShippingDeliveries($requests, self::SHIPPING_DELIVERIES);
            $this->basketService->addGetPaymentSystems($requests, self::PAYMENT_SYSTEMS);
            $this->basketService->addGetProviderPickupPointPickingDeliveriesSelectedPickupPoint($requests, self::SELECTED_PROVIDER_PICKUP_POINT);
            $this->getAddPluginsPaymentSystems($requests);

            $selectedCountryId = Utils::getSelectedCountryId();
            if ($selectedCountryId !== null && $selectedCountryId !== '') {
                $this->pickupPointProvidersParametersGroup = new PickupPointProvidersParametersGroup();
                $this->pickupPointProvidersParametersGroup->setCountryCode($selectedCountryId);
                Loader::service(Services::ORDER)->addGetPickupPointProviders($requests, self::PICKUP_POINT_PROVIDERS, $this->pickupPointProvidersParametersGroup);
            } else {
                Loader::service(Services::ORDER)->addGetPickupPointProviders($requests, self::PICKUP_POINT_PROVIDERS);
            }
        } else {
            $this->setDataValue(self::DELIVERIES, null);
            $this->setDataValue(self::PAYMENT_SYSTEMS, null);
            $this->setDataValue(self::PHYSICAL_LOCATIONS, new ElementCollection());
            $this->setDataValue(self::PICKUP_POINT_PROVIDERS, new ElementCollection());
        }
        if (!$this->settingsService->getBasketStockLockingSettings()->getActive()) {
            $this->basketService->addGetBasket($requests, self::BASKET);
        }

        $this->userService->addGetCustomTags($requests, self::USER_CUSTOM_TAGS, self::getTheme()->getConfiguration()->getUser()->getUserCustomTagsParametersGroup());
        if (Utils::isSessionLoggedIn($this->getSession())) {
            $this->userService->addGetBillingAddresses($requests, self::BILLING_ADDRESSES);
            $this->userService->addGetShippingAddresses($requests, self::SHIPPING_ADDRESSES);
            if ($this->getSession()?->getBasket()?->getAccountRegisteredUser()?->getType() === MasterType::EMPLOYEE) {
                $roleId = $this->getSession()?->getBasket()?->getAccountRegisteredUser()?->getRole()?->getId() ?? 0;
                if ($roleId !== 0) {
                    $this->accountService->addGetCompanyRole($requests, self::COMPANY_ROLE, $roleId);
                }
            }
        }
    }

    /**
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     */
    final protected function setControllerBaseData(): void {
        if ($this->settingsService->getBasketStockLockingSettings()->getActive()) {
            $basket = Loader::service(Services::BASKET)->recalculate();
            if (!is_null($basket->getError())) {
                $this->breakControllerProcess('Missing data on the Service response', CommerceException::CONTROLLER_UNDEFINED_CRITICAL_DATA);
            }
            $this->setDataValue(self::BASKET, $basket);
        }
        $deliveries = $this->getControllerData(self::DELIVERIES);
        $paymentSystems = $this->getControllerData(self::PAYMENT_SYSTEMS);
        if (Cookie::exist('basketToken') && !$this->useOSCAsync) {
            $this->checkCriticalServiceLoaded($paymentSystems);
            $deliveries = $this->getControllerData(self::PICKING_DELIVERIES);
            if (!is_null($deliveries)) {
                $deliveries->merge($this->getControllerData(self::SHIPPING_DELIVERIES));
            } else {
                $deliveries = $this->getControllerData(self::SHIPPING_DELIVERIES);
            }
            $this->setPhysicalLocationsFromDeliveries($deliveries, self::PHYSICAL_LOCATIONS, self::DEFAULT_PHYSICAL_LOCATION_ID);
            $deliveries->merge($this->getControllerData(self::SELECTED_PROVIDER_PICKUP_POINT));
            $this->setDataValue(self::DELIVERIES, $deliveries);
        }

        if (Utils::isSessionLoggedIn($this->getSession())) {
            $billingAddresses = $this->getControllerData(self::BILLING_ADDRESSES);
            $shippingAddresses = $this->getControllerData(self::SHIPPING_ADDRESSES);
            $thisAccountUpdatePermissions = $this->getControllerData(self::COMPANY_ROLE)?->getPermissions()?->getThisAccountUpdate() ?? true;
        } else {
            $billingAddresses = null;
            $shippingAddresses = null;
            $thisAccountUpdatePermissions = true;
        }

        $this->getAddPluginsPaymentProperties($paymentSystems);

        $this->setDataValue(
            self::CONTROLLER_ITEM,
            [
                self::DELIVERIES => $deliveries,
                self::PAYMENT_SYSTEMS => $paymentSystems,
                'addresses' => [
                    self::BILLING_ADDRESSES => $billingAddresses,
                    self::SHIPPING_ADDRESSES => $shippingAddresses
                ],
                self::CUSTOMER_FORM => FormFactory::setUser(FormFactory::SET_USER_TYPE_ADD_CUSTOMER, $this->getSession()->getUser(), $this->getControllerData(self::USER_CUSTOM_TAGS), true, true, $thisAccountUpdatePermissions),
                self::DEFAULT_SELECTED_COUNTRY => $this->getDefaultCountry(),
                self::DEFAULT_SELECTED_COUNTRY_LOCATIONS => $this->getDefaultCountryLocations(),
                self::USER_FORM => FormFactory::setUser(FormFactory::SET_USER_TYPE_ADD_CUSTOMER, $this->getSession()->getUser(), $this->getControllerData(self::USER_CUSTOM_TAGS), true, true, $thisAccountUpdatePermissions),
                self::PICKUP_POINT_PROVIDERS => $this->getControllerData(self::PICKUP_POINT_PROVIDERS)
            ]
        );
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
}
