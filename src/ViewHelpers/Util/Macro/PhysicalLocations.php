<?php

namespace FWK\ViewHelpers\Util\Macro;

use FWK\Core\Dtos\ElementCollection as DtosElementCollection;
use FWK\Dtos\Catalog\PhysicalLocation;
use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Form\Elements\Input;
use FWK\Core\Form\Elements\Inputs\InputHidden;
use FWK\Core\Form\Elements\Inputs\InputRadio;
use FWK\Core\Form\Elements\Inputs\InputText;
use FWK\Core\Form\Elements\Option;
use FWK\Core\Form\Elements\Select;
use FWK\Core\Form\FormFactory;
use FWK\Core\Form\FormItem;
use FWK\Core\Resources\Language;
use FWK\Core\Resources\Loader;
use FWK\Core\Resources\Session;
use FWK\Core\Resources\Utils;
use FWK\Enums\LanguageLabels;
use FWK\Enums\Parameters;
use FWK\Enums\Services;
use SDK\Application;
use SDK\Core\Dtos\ElementCollection;
use SDK\Dtos\Catalog\PhysicalLocation as DTOPhysicalLocation;
use SDK\Enums\PickingDeliveryType;

/**
 * This is the PhysicalLocations class, a macro class for the utilViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the util's pagination.
 *
 * @see PhysicalLocations::getViewParameters()
 *
 * @package FWK\ViewHelpers\Util\Macro
 */
class PhysicalLocations {

    public ?ElementCollection $physicalLocations = null;

    public ?ElementCollection $pickupPointProviders = null;

    public string $changeOptionFunction = '';

    public string $optionName = 'physicalLocation';

    public string $optionClass = '';

    public bool $addCountrySelector = false;

    public bool $addAllOption = false;

    public bool $addStateSelector = false;

    public bool $addCitySelector = false;

    public bool $addPostalCodeSelector = false;

    public bool $showInMap = false;

    public ?string $mapsApiKey = null;

    public ?int $defaultPhysicalLocationId = 0;

    public bool $showDirections = true;

    public array $physicalLocationFields = ['address', 'postalCode', 'city', 'state', 'country', 'phone', 'email', 'information'];

    public bool $showPlacesAutocomplete = false;

    public bool $showAllMapMarkersButton = false;

    public bool $showPickupPointProviderMapMarkers = false;

    public bool $filterByPhysicalLocations = true;

    public int $searchResultZoom = 11;

    public int $searchMinResults = 0;

    public int $searchMaxResults = 0;

    public bool $physicalLocationItemsCheckVisibility = false;

    private ?FormItem $optionsPhysicalLocation = null;

    private ?FormItem $countrySelector = null;

    private ?FormItem $stateSelector = null;

    private ?FormItem $citySelector = null;

    private ?FormItem $postalCodeSelector = null;

    private ?FormItem $postalCodeInput = null;

    private ?FormItem $placesAutocomplete = null;

    private ?FormItem $pickupPointProvidersSelector = null;

    private ?Language $language = null;

    private string $seletedCountryId = '';

    private string $seletedCountryName = '';

    /**
     * Constructor method for PhysicalLocations
     *
     * @see PhysicalLocations
     *
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        ViewHelper::mergeArguments($this, $arguments);
        $this->language = Language::getInstance();
    }

    /**
     * This method returns all calculated arguments and new parameters for UtilViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        if (is_null($this->physicalLocations)) {
            $this->physicalLocations = new ElementCollection();
            // throw new CommerceException("The value of [physicalLocations] argument: '" . $this->physicalLocations . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }

        if (!($this->physicalLocations instanceof ElementCollection)) {
            throw new CommerceException('The value of physicalLocations argument must be a instance of ' . ElementCollection::class . '. ' . ' Instance of ' . get_class($this->physicalLocations) . ' sended.', CommerceException::VIEW_HELPER_INVALID_ARGUMENT);
        }

        $session = Session::getInstance();
        $sessionGeneralSettings = $session->getGeneralSettings();
        $seletecAddress = $session->getUser()->getAddress($session->getUser()->getSelectedBillingAddressId());
        if (Utils::isSessionLoggedIn($session) && !is_null($seletecAddress?->getLocation())) {
            $this->seletedCountryId = $seletecAddress->getLocation()->getGeographicalZone()->getCountryCode();
        } else {
            $this->seletedCountryId = $sessionGeneralSettings->getCountry();
        }
        $this->seletedCountryName = Utils::getCountryNameByCountryCode($this->seletedCountryId);

        if (is_null($this->pickupPointProviders)) {
            if ($this->addCountrySelector) {
                $this->countrySelector = $this->getSelector(Parameters::COUNTRY, $this->language->getLabelValue(LanguageLabels::COUNTRY));
            }
            if ($this->addStateSelector) {
                $this->stateSelector = $this->getSelector(Parameters::STATE, $this->language->getLabelValue(LanguageLabels::STATE));
            }
            if ($this->addCitySelector) {
                $this->citySelector = $this->getSelector(Parameters::CITY, $this->language->getLabelValue(LanguageLabels::CITY));
            }
            if ($this->addPostalCodeSelector) {
                $this->postalCodeSelector = $this->getSelector(Parameters::POSTAL_CODE, $this->language->getLabelValue(LanguageLabels::POSTAL_CODE));
            }
        } else {
            if (count($this->pickupPointProviders->getItems()) > 0) {
                $options = [];
                foreach ($this->pickupPointProviders->getItems() as $pickupPointProvider) {
                    $options[] = (new Option($pickupPointProvider->getName()))->setValue($pickupPointProvider->getId())->setData($pickupPointProvider);
                };
                $this->pickupPointProvidersSelector = new FormItem(
                    Parameters::PICKUP_POINT_PROVIDER_ID,
                    (new Select($options, null, $this->seletedCountryId))
                        ->setLabelFor($this->language->getLabelValue(LanguageLabels::PROVIDERS_PICKUP_POINTS))
                        ->setClass(FormFactory::CLASS_WILDCARD)
                        ->setAttributeWildcard(FormFactory::ATTRIBUTE_WILDCARD)
                        ->setAutocomplete(Input::AUTOCOMPLETE_OFF)
                        ->setData(['showPickupPointProviderMapMarkers' => $this->showPickupPointProviderMapMarkers])
                );
            }
            $options = [];
            foreach (Application::getInstance()->getCountriesSettings($sessionGeneralSettings->getLanguage(), $this->seletedCountryId) as $country) {
                $options[] = (new Option($country->getName()))->setValue($country->getCode())->setData($country);
            }
            $this->countrySelector = new FormItem(
                Parameters::COUNTRY,
                (new Select($options, null, $this->seletedCountryId))
                    ->setLabelFor($this->language->getLabelValue(LanguageLabels::COUNTRY))
                    ->setClass(FormFactory::CLASS_WILDCARD)
                    ->setAttributeWildcard(FormFactory::ATTRIBUTE_WILDCARD)
                    ->setAutocomplete(Input::AUTOCOMPLETE_OFF)
            );
            $this->postalCodeInput = new FormItem(
                Parameters::POSTAL_CODE,
                (new InputText())
                    ->setLabelFor($this->language->getLabelValue(LanguageLabels::POSTAL_CODE))
                    ->setClass(FormFactory::CLASS_WILDCARD)
                    ->setAttributeWildcard(FormFactory::ATTRIBUTE_WILDCARD)
                    ->setAutocomplete(Input::AUTOCOMPLETE_OFF)
            );
        }

        if (is_null($this->defaultPhysicalLocationId)) {
            $this->defaultPhysicalLocationId = 0;
        }

        $dataOptions = [];
        $options = [];
        $this->physicalLocations = DtosElementCollection::fillFromParentCollection($this->physicalLocations, PhysicalLocation::class);
        foreach ($this->physicalLocations as $physicalLocation) {
            if (!($physicalLocation instanceof DTOPhysicalLocation)) {
                throw new CommerceException('Each element of physicalLocations must be a instance of ' . DTOPhysicalLocation::class . '. ' . ' Instance of ' . get_class($physicalLocation) . ' sended.', CommerceException::VIEW_HELPER_INVALID_ARGUMENT);
            }
            $options[$physicalLocation->getId()] = '{{option_' . $physicalLocation->getId() . '}}';
            $physicalLocation->setShowInMap($this->showInMap);
            $dataOptions[$physicalLocation->getId()] = $physicalLocation->toArray();
            $dataOptions[$physicalLocation->getId()][Parameters::COUNTRY] = Utils::getCountryNameByCountryCode($physicalLocation->getLocation()->getGeographicalZone()->getCountryCode());

            if (!is_null($physicalLocation->getDelivery())) {
                $dataOptions[$physicalLocation->getId()][Parameters::MODE] = $physicalLocation->getDelivery()->getMode()->getType();
                if ($physicalLocation->getDelivery()->getMode()->getType() == PickingDeliveryType::PROVIDER_PICKUP_POINT) {
                    $dataOptions[$physicalLocation->getId()][Parameters::ADDITIONAL_DATA] = $physicalLocation->getDelivery()->getMode()->getProviderPickupPoint();
                }
            }
        }
        $this->optionsPhysicalLocation = new FormItem(
            $this->optionName,
            (new InputRadio($options, null, $this->defaultPhysicalLocationId))
                ->setData($dataOptions)
                ->setId($this->optionName)
                ->setClass($this->optionClass . ' ' . FormFactory::CLASS_WILDCARD)
                ->setAttributeWildcard(FormFactory::ATTRIBUTE_WILDCARD)
        );

        if ($this->showInMap && is_null($this->mapsApiKey)) {
            $mapsProperties = Loader::service(Services::PLUGIN)->getMapsPluginProperties();
            if (!is_null($mapsProperties)) {
                foreach ($mapsProperties->getProperties() as $property) {
                    if ($property->getName() == "apiKey") {
                        $this->mapsApiKey = $property->getValue();
                    }
                }
            }
        }

        if ($this->showPlacesAutocomplete && !is_null($this->mapsApiKey)) {
            $this->placesAutocomplete =
                new FormItem(
                    'placesAutocomplete',
                    (new InputText())
                        ->setLabelFor($this->language->getLabelValue(LanguageLabels::PLACES_AUTOCOMPLETE))
                        ->setClass('placesAutocompleteInput ' . FormFactory::CLASS_WILDCARD)
                        ->setAttributeWildcard(FormFactory::ATTRIBUTE_WILDCARD)
                        ->setPlaceHolder($this->language->getLabelValue(LanguageLabels::PLACES_AUTOCOMPLETE_PLACEHOLDER))
                );
        }

        return $this->getProperties();
    }

    /**
     * Return Select form item
     *
     * @return FormItem
     */
    protected function getSelector(string $name, string $label): FormItem {
        $selectorItems = [];
        $selectorIds = [];

        foreach ($this->physicalLocations as $physicalLocation) {
            $parent = $this->getParent($name, $physicalLocation);
            if ($name === Parameters::COUNTRY) {
                $countryName = Utils::getCountryNameByCountryCode($physicalLocation->getLocation()->getGeographicalZone()->getCountryCode());
                $itemKey = $countryName;
                $value = $countryName;
                $selectorItems[$itemKey]['countryCode'] = $physicalLocation->getLocation()->getGeographicalZone()->getCountryCode();
            } else {
                $getFunction = "get" . ucwords($name);
                $value = $physicalLocation->$getFunction();
                $itemKey = $parent . $value;
            }
            $selectorItems[$itemKey]['value'] = $value;
            $selectorItems[$itemKey]['ids'][] = $this->optionName . $physicalLocation->getId();
            $selectorItems[$itemKey]['parent'] = $parent;
            $selectorIds[] = $this->optionName . $physicalLocation->getId();
        }
        $options = [];
        if ($this->addAllOption && $this->filterByPhysicalLocations) {
            $selectorItem['ids'] = $selectorIds;
            $selectorItem['parent'] = '';
            $languageLabel = (new \ReflectionClassConstant(LanguageLabels::class, 'PHYSICAL_LOCATION_OPTION_ALL_' . Utils::getSnakeFromCamel($name)))->getValue();
            $options[] = (new Option($this->language->getLabelValue($languageLabel)))
                ->setValue('ALL')
                ->setData($selectorItem);
        }
        ksort($selectorItems);
        foreach ($selectorItems as $itemKey => $selectorItemValue) {
            $options[] = (new Option($selectorItemValue['value']))
                ->setValue($name . '_' . $itemKey)
                ->setData($selectorItemValue);
        }
        return new FormItem(
            $name,
            (new Select($options, null, $name === Parameters::COUNTRY ? ('country_' . $this->seletedCountryName) : ''))
                ->setLabelFor($label)
                ->setClass(FormFactory::CLASS_WILDCARD)
                ->setAttributeWildcard(FormFactory::ATTRIBUTE_WILDCARD)
                ->setAutocomplete(Input::AUTOCOMPLETE_OFF)
        );
    }

    private function getParent(string $name, DTOPhysicalLocation $physicalLocation): string {
        $parnetOrder = [Parameters::POSTAL_CODE, Parameters::CITY, Parameters::STATE, Parameters::COUNTRY];
        $parent = '';
        for ($i = array_search($name, $parnetOrder) + 1; $i < count($parnetOrder); $i++) {
            $property = 'add' . ucwords($parnetOrder[$i]) . 'Selector';
            if ($this->$property) {
                $parent = $parnetOrder[$i];
                break;
            }
        }
        if ($parent === Parameters::COUNTRY) {
            return Parameters::COUNTRY . '_' . Utils::getCountryNameByCountryCode($physicalLocation->getLocation()->getGeographicalZone()->getCountryCode());
        } else if ($parent === Parameters::STATE) {
            return Parameters::STATE . '_' . $physicalLocation->getState();
        } else if ($parent === Parameters::CITY) {
            return Parameters::CITY . '_' . $physicalLocation->getCity();
        } else {
            return '';
        }
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        return [
            'physicalLocations' => $this->physicalLocations,
            'optionsPhysicalLocation' => $this->optionsPhysicalLocation,
            'changeOptionFunction' => $this->changeOptionFunction,
            'countrySelector' => $this->countrySelector,
            'stateSelector' => $this->stateSelector,
            'citySelector' => $this->citySelector,
            'postalCodeSelector' => $this->postalCodeSelector,
            'postalCodeInput' => $this->postalCodeInput,
            'showInMap' => $this->showInMap,
            'mapsApiKey' => $this->mapsApiKey,
            'optionName' => $this->optionName,
            'optionClass' => $this->optionClass,
            'defaultPhysicalLocationId' => $this->defaultPhysicalLocationId,
            'showDirections' => $this->showDirections,
            'physicalLocationFields' => $this->physicalLocationFields,
            'placesAutocomplete' => $this->placesAutocomplete,
            'showAllMapMarkersButton' => $this->showAllMapMarkersButton,
            'pickupPointProviders' => $this->pickupPointProviders,
            'pickupPointProvidersSelector' => $this->pickupPointProvidersSelector,
            'showPickupPointProviderMapMarkers' => $this->showPickupPointProviderMapMarkers,
            'filterByPhysicalLocations' => $this->filterByPhysicalLocations,
            'seletedCountryId' => $this->seletedCountryId,
            'searchResultZoom' => $this->searchResultZoom,
            'searchMinResults' => $this->searchMinResults,
            'searchMaxResults' => $this->searchMaxResults,
            'physicalLocationItemsCheckVisibility' => $this->physicalLocationItemsCheckVisibility
        ];
    }
}
