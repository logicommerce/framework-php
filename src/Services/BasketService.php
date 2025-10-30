<?php

namespace FWK\Services;

use DateTime;
use FWK\Core\Resources\Loader;
use FWK\Core\Resources\Registries\RegistryService;
use FWK\Core\Resources\Session;
use FWK\Core\Theme\Dtos\CommerceLockedStock;
use FWK\Enums\Services;
use SDK\Core\Dtos\ElementCollection;
use SDK\Services\BasketService as BasketServiceSDK;
use SDK\Dtos\Basket\BasketRows\Option;
use SDK\Enums\OptionType;
use SDK\Dtos\Basket\BasketRows\Options\OptionValue;
use SDK\Core\Resources\Date;
use SDK\Enums\PluginConnectorType;
use FWK\Services\Traits\ServiceTrait;
use SDK\Services\Parameters\Groups\Basket\UpdateLockedStockTimerParametersGroup;

/**
 * This is the BasketService class.
 * Remember that a service is an extension of a SDK model that allows to add additional
 * actions to a model request or create new methods to simplify some common requests.
 * In this case, the BasketService extends the SDK\Services\BasketService.
 *
 * @see BasketService::parseOptionValue()
 *
 * @see BasketService
 *
 * @package FWK\Services
 */
class BasketService extends BasketServiceSDK {
    use ServiceTrait;

    private const REGISTRY_KEY = RegistryService::BASKET_SERVICE;

    private const ADD_FILTER_INTERVAL_PARAMETERS = [];

    private const ADD_FILTER_ID_VALUE_PARAMETERS = [];

    private const POV_NAME = 'name';

    private const POV_VALUE = 'value';

    private const POV_SHORT_DESCRIPTION = 'shortDescription';

    private const POV_LONG_DESCRIPTION = 'longDescription';

    private const POV_VALUES = 'values';

    private const POV_TYPE = 'type';

    public const POV_SINGLE_VALUE = [
        OptionType::BOOLEAN,
        OptionType::ATTACHMENT,
        OptionType::DATE,
        OptionType::SHORT_TEXT,
        OptionType::SINGLE_SELECTION,
        OptionType::SELECTOR,
        OptionType::LONG_TEXT,
        OptionType::SINGLE_SELECTION_IMAGE
    ];

    public const POV_MULTIPLE_VALUES = [
        OptionType::MULTIPLE_SELECTION,
        OptionType::MULTIPLE_SELECTION_IMAGE
    ];

    private static function getParseOptionValue(Option $option): array {
        $response = [];
        $optionValue = $option->getValue();

        if (!is_null($optionValue)) {

            if ($optionValue instanceof OptionValue) {
                $thisOptionValue = [
                    self::POV_SHORT_DESCRIPTION => $optionValue->getShortDescription(),
                    self::POV_LONG_DESCRIPTION => $optionValue->getLongDescription(),
                    self::POV_VALUE => $optionValue->getValue()
                ];
                $response[] = $thisOptionValue;
            } elseif (is_bool($optionValue) || is_string($optionValue)) {
                $response[] = [
                    self::POV_VALUE => $optionValue
                ];
            } elseif ($optionValue instanceof Date) {
                $response[] = [
                    self::POV_VALUE => $optionValue->getDateTime()
                ];
            }
        }

        return $response;
    }

    private static function getParseOptionValues(Option $option): array {
        $response = [];
        $optionValues = $option->getValueList();

        if (!is_null($optionValues)) {
            foreach ($option->getValueList() as $optionValue) {
                if ($optionValue instanceof OptionValue) {
                    $thisOptionValue = [
                        self::POV_SHORT_DESCRIPTION => $optionValue->getShortDescription(),
                        self::POV_LONG_DESCRIPTION => $optionValue->getLongDescription(),
                        self::POV_VALUE => $optionValue->getValue()
                    ];
                    $response[] = $thisOptionValue;
                }
            }
        }

        return $response;
    }

    /**
     * This method parses the given basket product option and returns it.
     * The return is an array containing up to three values in these keys: self::POV_VALUE, self::POV_SHORT_DESCRIPTION, self::POV_LONG_DESCRIPTION.
     * 
     * @param Option $option
     * 
     * @return array
     */
    public static function parseOptionValue(Option $option): array {
        $response = [];
        if (in_array($option->getType(), self::POV_SINGLE_VALUE)) {
            $response = self::getParseOptionValue($option);
        } elseif (in_array($option->getType(), self::POV_MULTIPLE_VALUES)) {
            $response = self::getParseOptionValues($option);
        }
        return $response;
    }

    /**
     * Merges payment systems with plugin properties.
     *
     * @param ElementCollection $paymentSystems The collection of payment systems to merge with properties.
     * @param array $pluginProperties The array of plugin properties to merge into payment systems.
     */
    public static function mergePaymentSystemsWithPluginProperties(ElementCollection &$paymentSystems, array $pluginProperties): void {
        foreach ($paymentSystems as $paymentSystem) {
            $key = $paymentSystem->getModule() . "_" . $paymentSystem->getPluginId();
            if (method_exists($paymentSystem, 'setPluginPropertiesConnectorItemProperties') && isset($pluginProperties[$key])) {
                foreach ($pluginProperties[$key]->getConnectors() as $connector) {
                    if ($connector->getType() === PluginConnectorType::PAYMENT_SYSTEM) {
                        foreach ($connector->getItems() as $item) {
                            if ($item->getItemId() === $paymentSystem->getId()) {
                                $paymentSystem->setPluginPropertiesConnectorItemProperties($item->getProperties());
                            }
                        }
                    }
                }
            }

            if (method_exists($paymentSystem, 'setPluginProperties') && isset($pluginProperties[$key])) {
                $paymentSystem->setPluginProperties($pluginProperties[$key]->getProperties());
            }

            if (method_exists($paymentSystem, 'setPluginActions') && isset($pluginProperties[$key])) {
                $paymentSystem->setPluginActions($pluginProperties[$key]->getEventsResults());
            }
        }
    }


    /**
     * Merges payment systems with plugin properties.
     *
     * @param string $extendBy Extend by type CommerceLockedStock::EXTEND_BY_BASKET_CHANGE | CommerceLockedStock::EXTEND_BY_ROUTE_VISITED | CommerceLockedStock::EXTEND_BY_PAYMENT_GATEWAY_VISITED
     * @param array $pluginProperties The array of plugin properties to merge into payment systems.
     */
    public function extendLockedStockTimer(string $extendBy, ?CommerceLockedStock $lockedStock): void {
        $settingsService = Loader::service(Services::SETTINGS);
        $extendBy = 'get' . ucfirst($extendBy);
        if ($settingsService->getBasketStockLockingSettings()->getActive()) {
            $lockedStocksAggregateData = Session::getInstance()->getLockedStocksAggregateData();
            if ($lockedStock->$extendBy()) {
                $someUpdate = false;
                foreach ($lockedStocksAggregateData as $lockedStocksAggregateDataItem) {
                    $updateLockedStockTimerParametersGroup = new UpdateLockedStockTimerParametersGroup();
                    $updateLockedStockTimerParametersGroup->setExpiresAtExtendMinutesUponUserRequest(false);
                    $getReset = $extendBy . "ResetCounter";
                    if ($lockedStock->$getReset()) {
                        $currentDate = new DateTime();
                        $interval = $currentDate->diff($lockedStocksAggregateDataItem->getExpiresAt()->getDateTime());
                        $updateMinutes = $settingsService->getBasketStockLockingSettings()->getLockedStockTimerIniLockingMinutes() - (($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i);
                        if ($updateMinutes > 0) {
                            $updateLockedStockTimerParametersGroup->setExpiresAtExtendMinutes($updateMinutes);
                            $this->updateLockedStockTimer($lockedStocksAggregateDataItem->getUid(), $updateLockedStockTimerParametersGroup);
                            $someUpdate = true;
                        }
                    } else {
                        $getTime = $extendBy . "Time";
                        $updateMinutes = $lockedStock->$getTime();
                        if ($updateMinutes > 0) {
                            $updateLockedStockTimerParametersGroup->setExpiresAtExtendMinutes($updateMinutes);
                        }
                        $this->updateLockedStockTimer($lockedStocksAggregateDataItem->getUid(), $updateLockedStockTimerParametersGroup);
                        $someUpdate = true;
                    }
                }
                if ($someUpdate) {
                    $this->recalculate();
                }
            }
        }
    }
}
