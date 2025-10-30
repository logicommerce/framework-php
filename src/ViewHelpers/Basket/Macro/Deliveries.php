<?php

namespace FWK\ViewHelpers\Basket\Macro;

use FWK\Core\Dtos\ElementCollection as DtosElementCollection;
use FWK\Core\Dtos\Factories\BasketDeliveryFactory;
use SDK\Dtos\Basket\Basket;
use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use FWK\Dtos\Basket\Basket as ViewHelpersDtosBasket;
use SDK\Core\Dtos\ElementCollection;
use FWK\ViewHelpers\Basket\BasketViewHelper;
use FWK\Dtos\Basket\BasketRowData;
use SDK\Enums\DeliveryType;
use SDK\Enums\PickingDeliveryType;

/**
 * This is the Deliveries class, a macro class for the basket viewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the basket's deliveries.
 *
 * @see Deliveries::getViewParameters()
 *
 * @package FWK\ViewHelpers\Basket\Macro
 */
class Deliveries {

    public ?Basket $basket = null;

    public ?ElementCollection $deliveries = null;

    public ?ElementCollection $physicalLocations = null;

    public ?ElementCollection $pickupPointProviders = null;

    public bool $showTitle = true;

    public bool $showLogo = false;

    public ?bool $showTaxIncluded = null;

    public bool $showDescription = true;

    public bool $showSelectPickingDescription = false;

    public bool $showPickup = false;

    public bool $showProducts = true;

    public bool $showWarnings = true;

    private array $pickingPoints = [];

    private bool $empty = false;

    /**
     * Constructor method for Deliveries class.
     * 
     * @see Deliveries
     * 
     * @param array $arguments
     * @throws CommerceException
     */
    public function __construct(array $arguments) {
        $this->showTaxIncluded = ViewHelper::getApplicationTaxesIncluded();
        ViewHelper::mergeArguments($this, $arguments);

        if (is_null($this->basket)) {
            throw new CommerceException("[Basket] argument in Deliveries macro is required." . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        } else {
            $this->basket = ViewHelpersDtosBasket::fillFromParent($this->basket);
        }
        if (is_null($this->deliveries)) {
            throw new CommerceException("[Deliveries] argument in Deliveries macro is required." . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        } else {
            $this->deliveries = DtosElementCollection::fillFromParentCollection($this->deliveries, BasketDeliveryFactory::class);
        }
    }

    /**
     * This method returns all calculated arguments and new parameters for BasketViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        $this->setEmpty();
        $this->setBasketRowsData();
        if ($this->showWarnings) {
            $this->setWarningsMessages();
        }
        $this->setSelectedShippings();

        if (!empty($this->pickingPoints)) {
            if (is_null($this->physicalLocations)) {
                throw new CommerceException("[physicalLocations] argument in Deliveries macro is required. " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
            }
            if (!is_null($this->physicalLocations->getError())) {
                throw new CommerceException($this->physicalLocations->getError()->getMessage(), CommerceException::CONTROLLER_UNDEFINED_CRITICAL_DATA);
            }
        }

        return $this->getProperties();
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        return [
            'deliveries' => $this->deliveries,
            'physicalLocations' => $this->physicalLocations,
            'showTitle' => $this->showTitle,
            'showLogo' => $this->showLogo,
            'showTaxIncluded' => $this->showTaxIncluded,
            'showDescription' => $this->showDescription,
            'showPickup' => $this->showPickup,
            'showProducts' => $this->showProducts,
            'showWarnings' => $this->showWarnings,
            'empty' => $this->empty,
            'pickupPointProviders' => $this->pickupPointProviders,
            'showSelectPickingDescription' => $this->showSelectPickingDescription
        ];
    }

    /**
     * Set empty property true if basket isn't deliverable
     *
     * @return void
     */
    private function setEmpty(): void {
        $shippingsTotal = 0;
        foreach ($this->deliveries->getItems() as $delivery) {
            $shipments = $delivery->getShipments();
            foreach ($shipments as $shipment) {
                $shippingsTotal += count($shipment->getShippings());
            }
            if ($delivery->getType() === DeliveryType::PICKING && $delivery->getMode()->getType() == PickingDeliveryType::PICKUP_POINT_PHYSICAL_LOCATION) {
                $this->pickingPoints[] = $delivery->getMode()->getPhysicalLocation()->getId();
            }
        }

        if ($shippingsTotal === 0 && count($this->pickingPoints) === 0) {
            $this->empty = true;
        }
    }

    /**
     * Fill shipments rows (BasketRowData) extra product data from basket
     *
     * @return void
     */
    private function setBasketRowsData(): void {
        foreach ($this->deliveries->getItems() as $delivery) {
            if ($delivery->getType() === DeliveryType::SHIPPING) {
                foreach ($delivery->getShipments() as $shippment) {
                    foreach ($shippment->getRows() as $row) {
                        $basketRowData = $row->getBasketRowData();
                        if (!is_null($basketRowData)) {
                            $basketRowData = $this->setBasketRowData($basketRowData);
                        }
                    }
                }
            } elseif ($delivery->getType() === DeliveryType::PICKING) {
                foreach ($delivery->getDeliveryRows() as $row) {
                    $basketRowData = $row->getBasketRowData();
                    if (!is_null($basketRowData)) {
                        $basketRowData = $this->setBasketRowData($basketRowData);
                    }
                }
            }
        }
    }

    /**
     * Fill delivery warnings with output message and group by severity.
     *
     * @return void
     */
    private function setWarningsMessages(): void {
        foreach ($this->deliveries->getItems() as $delivery) {
            $deliveryRows = $delivery->getDeliveryRows();
            $deliveryWarnings = [];

            foreach ($deliveryRows as $deliveryRow) {
                $basketWarnings = $deliveryRow->getBasketWarnings();

                // Set message and group warnings
                foreach ($basketWarnings as $baskeWarning) {
                    $baskeWarning = BasketViewHelper::setBasketWarningMessage($baskeWarning, $this->basket);
                    $deliveryWarnings[] = $baskeWarning;
                }
            }
            $delivery->setOutputWarnings(BasketViewHelper::groupBasketWarnings($deliveryWarnings));
        }
    }

    /**
     * Add basket row extra data into BasketRowData from Basket object
     *
     * @param BasketRowData $basketRowData
     *
     * @return BasketRowData
     */
    private function setBasketRowData(BasketRowData $basketRowData): BasketRowData {
        $basketRow = $this->basket->getItem($basketRowData->getHash());
        if (!is_null($basketRow)) {
            $basketRowData->setBasketRow($basketRow);
        }
        return $basketRowData;
    }

    /**
     * Add [selected] property into Shipping comparing Basket Shipping
     *
     * @return void
     */
    private function setSelectedShippings(): void {
        $basketDelivery = $this->basket->getDelivery();
        if (!is_null($basketDelivery)) {
            foreach ($this->deliveries as $delivery) {
                if ($delivery->getHash() === $basketDelivery->getHash()) {
                    foreach ($delivery->getShipments() as $shipment) {
                        foreach ($basketDelivery->getShipments() as $basketShipment) {
                            if (!is_null($basketShipment) &&  $shipment->getHash() === $basketShipment->getHash()) {
                                foreach ($shipment->getShippings() as $shipping) {
                                    if (!is_null($basketShipment->getShipping()) && $shipping->getHash() === $basketShipment->getShipping()->getHash()) {
                                        $shipping->setSelected(true);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
