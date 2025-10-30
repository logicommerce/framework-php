<?php

namespace FWK\Controllers\Checkout\Internal;

use SDK\Core\Resources\BatchRequests;
use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\Controllers\Traits\SetPhysicalLocationsFromDeliveries;
use FWK\Core\Resources\Loader;
use FWK\Core\Resources\Utils;
use FWK\Enums\Services;
use FWK\Services\BasketService;
use FWK\Services\OrderService;
use SDK\Services\Parameters\Groups\Document\PickupPointProvidersParametersGroup;

/**
 * This is the OSC internal shippings controller.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @see BaseHtmlController
 *
 * @package FWK\Controllers\Checkout\Internal
 */
class OscShippingsController extends BaseHtmlController {
    use SetPhysicalLocationsFromDeliveries;

    public const DELIVERIES = 'deliveries';

    protected const PICKING_DELIVERIES = 'pickingDeliveries';

    protected const SHIPPING_DELIVERIES = 'shippingDeliveries';

    protected const PICKUP_POINT_PROVIDERS = 'pickupPointProviders';

    protected const SELECTED_PROVIDER_PICKUP_POINT = 'selectedProviderPickupPoint';

    public const PHYSICAL_LOCATIONS = 'physicalLocations';

    public const DEFAULT_PHYSICAL_LOCATION_ID = 'defaultPhysicalLocationId';

    protected ?PickupPointProvidersParametersGroup $pickupPointProvidersParametersGroup = null;

    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
        /** @var BasketService $basketService */
        $basketService = Loader::service(Services::BASKET);
        $basketService->addGetPhysicalLocationPickingDeliveries($requests, self::PICKING_DELIVERIES);
        $basketService->addGetShippingDeliveries($requests, self::SHIPPING_DELIVERIES);
        $basketService->addGetProviderPickupPointPickingDeliveriesSelectedPickupPoint($requests, self::SELECTED_PROVIDER_PICKUP_POINT);

        /** @var OrderService $orderService */
        $orderService = Loader::service(Services::ORDER);

        $selectedCountryId = Utils::getSelectedCountryId();
        if ($selectedCountryId !== null && $selectedCountryId !== '') {
            $this->pickupPointProvidersParametersGroup = new PickupPointProvidersParametersGroup();
            $this->pickupPointProvidersParametersGroup->setCountryCode($selectedCountryId);
            $orderService->addGetPickupPointProviders($requests, self::PICKUP_POINT_PROVIDERS, $this->pickupPointProvidersParametersGroup);
        } else {
            $orderService->addGetPickupPointProviders($requests, self::PICKUP_POINT_PROVIDERS);
        }
    }

    /**
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     */
    final protected function setControllerBaseData(): void {
        $deliveries = $this->getControllerData(self::PICKING_DELIVERIES);
        if (!is_null($deliveries)) {
            $deliveries->merge($this->getControllerData(self::SHIPPING_DELIVERIES));
        } else {
            $deliveries = $this->getControllerData(self::SHIPPING_DELIVERIES);
        }
        $this->setPhysicalLocationsFromDeliveries($deliveries, self::PHYSICAL_LOCATIONS, self::DEFAULT_PHYSICAL_LOCATION_ID);
        $deliveries->merge($this->getControllerData(self::SELECTED_PROVIDER_PICKUP_POINT));
        $this->setDataValue(self::DELIVERIES, $deliveries);
        $this->setDataValue(self::CONTROLLER_ITEM, [
            self::DELIVERIES => $deliveries,
            self::PICKUP_POINT_PROVIDERS => $this->getControllerData(self::PICKUP_POINT_PROVIDERS)
        ]);
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
