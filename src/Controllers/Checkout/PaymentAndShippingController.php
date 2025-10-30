<?php

namespace FWK\Controllers\Checkout;

use FWK\Core\Controllers\Traits\CheckoutRedirectTrait;
use SDK\Core\Resources\BatchRequests;
use SDK\Enums\RouteType;
use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\Controllers\Traits\SetPhysicalLocationsFromDeliveries;
use FWK\Core\Controllers\Traits\AddPluginPaymentSystemTrait;
use FWK\Core\Resources\Loader;
use FWK\Core\Resources\Response;
use FWK\Enums\Services;
use FWK\Core\Resources\RoutePaths;
use FWK\Core\Resources\Utils;
use FWK\Services\BasketService;
use FWK\Services\PluginService;
use SDK\Core\Dtos\ElementCollection;
use SDK\Core\Resources\Cookie;
use SDK\Dtos\Common\Route;
use SDK\Services\Parameters\Groups\Document\PickupPointProvidersParametersGroup;

/**
 * This is the checkout payment and shipping controller.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @see BaseHtmlController
 *
 * @package FWK\Controllers\Checkout
 */
class PaymentAndShippingController extends BaseHtmlController {
    // Overrride __construct 
    use SetPhysicalLocationsFromDeliveries, AddPluginPaymentSystemTrait;

    use CheckoutRedirectTrait {
        __construct as __constructCheckoutRedirectTrait;
    }

    public const BASKET = 'basket';

    protected const DELIVERIES = 'deliveries';

    protected const PICKING_DELIVERIES = 'pickingDeliveries';

    protected const SHIPPING_DELIVERIES = 'shippingDeliveries';

    protected const PICKUP_POINT_PROVIDERS = 'pickupPointProviders';

    protected const SELECTED_PROVIDER_PICKUP_POINT = 'selectedProviderPickupPoint';

    protected const PAYMENT_SYSTEMS = 'paymentSystems';

    public const PHYSICAL_LOCATIONS = 'physicalLocations';

    public const DEFAULT_PHYSICAL_LOCATION_ID = 'defaultPhysicalLocationId';

    private ?BasketService $basketService = null;

    private ?PluginService $pluginService = null;

    private ?ElementCollection $paymentSystemPlugins = null;

    protected ?PickupPointProvidersParametersGroup $pickupPointProvidersParametersGroup = null;

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        $this->__constructCheckoutRedirectTrait($route);
    }

    /**
     * This method is the one in charge of defining all the data batch requests that are basic for
     * the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
        if (Cookie::exist('basketToken')) {
            $this->basketService = Loader::service(Services::BASKET);
            $this->basketService->addGetPhysicalLocationPickingDeliveries($requests, self::PICKING_DELIVERIES);
            $this->basketService->addGetShippingDeliveries($requests, self::SHIPPING_DELIVERIES);
            $this->basketService->addGetPaymentSystems($requests, self::PAYMENT_SYSTEMS);
            $this->basketService->addGetProviderPickupPointPickingDeliveriesSelectedPickupPoint($requests, self::SELECTED_PROVIDER_PICKUP_POINT);

            $selectedCountryId = Utils::getSelectedCountryId();
            if ($selectedCountryId !== null && $selectedCountryId !== '') {
                $this->pickupPointProvidersParametersGroup = new PickupPointProvidersParametersGroup();
                $this->pickupPointProvidersParametersGroup->setCountryCode($selectedCountryId);
                Loader::service(Services::ORDER)->addGetPickupPointProviders($requests, self::PICKUP_POINT_PROVIDERS, $this->pickupPointProvidersParametersGroup);
            } else {
                Loader::service(Services::ORDER)->addGetPickupPointProviders($requests, self::PICKUP_POINT_PROVIDERS);
            }

            $this->getAddPluginsPaymentSystems($requests);
        } else {
            $this->setDataValue(self::DELIVERIES, null);
            $this->setDataValue(self::PAYMENT_SYSTEMS, null);
        }
        Loader::service(Services::BASKET)->addGetBasket($requests, self::BASKET);
    }

    /**
     * This method is in charge of defining the basic data necessary for the correct operation of
     * the controller.
     */
    final protected function setControllerBaseData(): void {
        $sessionUser = $this->getSession()->getUser();
        $aggregateData = $this->getSession()->getAggregateData();
        if (is_null($aggregateData->getBasket()) || ($aggregateData->getBasket()->getTotalProducts()) === 0) {
            // empty basket
            Response::redirect(RoutePaths::getPath(RouteType::CHECKOUT_BASKET));
        } elseif ($sessionUser->getId() === 0 && strlen($sessionUser->getEmail()) === 0 && !Utils::isExpressCheckout($this->getControllerData(self::BASKET))) {
            // is not logged-in and has no guest data
            Response::redirect(RoutePaths::getPath(RouteType::CHECKOUT_CUSTOMER));
        }
        $deliveries = $this->getControllerData(self::PICKING_DELIVERIES);
        if (!is_null($deliveries)) {
            $deliveries->merge($this->getControllerData(self::SHIPPING_DELIVERIES));
        } else {
            $deliveries = $this->getControllerData(self::SHIPPING_DELIVERIES);
        }
        $this->setPhysicalLocationsFromDeliveries($deliveries, self::PHYSICAL_LOCATIONS, self::DEFAULT_PHYSICAL_LOCATION_ID);
        $deliveries->merge($this->getControllerData(self::SELECTED_PROVIDER_PICKUP_POINT));
        $this->setDataValue(self::DELIVERIES, $deliveries);
        $this->getAddPluginsPaymentProperties($this->getControllerData(self::PAYMENT_SYSTEMS));
    }

    /**
     * This method is the one in charge of defining all the data batch requests that are needed for
     * the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     * @return void
     */
    protected function setBatchData(BatchRequests $request): void {
    }


    /**
     * This method runs after the batch requests (defined in the setBatchData methods) are resolved,
     * so here you can work with the response of the batch requests and calculate and set more
     * needed data.
     *
     * @param array $additionalData
     *              Set additiona data to the controller data
     * 
     * @return void
     */
    protected function setData(array $additionalData = []): void {
    }
}
