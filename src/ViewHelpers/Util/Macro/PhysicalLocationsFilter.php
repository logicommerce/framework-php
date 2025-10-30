<?php

namespace FWK\ViewHelpers\Util\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Form\Elements\Input;
use FWK\Core\Form\Elements\Inputs\InputText;
use FWK\Core\Form\Elements\Option;
use FWK\Core\Form\Elements\Select;
use FWK\Core\Form\FormFactory;
use FWK\Core\Form\FormItem;
use FWK\Core\Resources\Language;
use FWK\Core\Resources\Session;
use FWK\Enums\LanguageLabels;
use FWK\Enums\Parameters;
use SDK\Application;
use SDK\Core\Dtos\ElementCollection;
use SDK\Dtos\Country;

/**
 * This is the PhysicalLocationsFilter class, a macro class for the utilViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the util's pagination.
 *
 * @see PhysicalLocationsFilter::getViewParameters()
 *
 * @package FWK\ViewHelpers\Util\Macro
 */
class PhysicalLocationsFilter {

    public array $physicalLocationsFilter = [];

    public ?ElementCollection $countries = null;

    public string $defaultCountry = '';

    private ?FormItem $countrySelector = null;

    private ?FormItem $postalCodeInput = null;

    private ?Language $languageSheet = null;

    /**
     * Constructor method for PhysicalLocationsFilter
     *
     * @see PhysicalLocationsFilter
     *
     * @param array $arguments
     * @param Language $languageSheet
     */
    public function __construct(array $arguments, Language $languageSheet) {
        $this->languageSheet = $languageSheet;
        ViewHelper::mergeArguments($this, $arguments);
    }

    /**
     * This method returns all calculated arguments and new parameters for UtilViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {

        if (!is_null($this->countries)) {
            if (!($this->countries instanceof ElementCollection)) {
                throw new CommerceException('The value of countries argument must be a instance of ' . ElementCollection::class . '. ' . ' Instance of ' . get_class($this->countries) . ' sended.', CommerceException::VIEW_HELPER_INVALID_ARGUMENT);
            }
            foreach ($this->countries->getItems() as $country) {
                if (!($country instanceof Country)) {
                    throw new CommerceException('Each element of countries must be a instance of ' . Country::class . '. ' . ' Instance of ' . get_class($country) . ' sended.', CommerceException::VIEW_HELPER_INVALID_ARGUMENT);
                }
            }
        } else {
            $this->countries = Application::getInstance()->getCountriesSettings(Session::getInstance()->getGeneralSettings()->getLanguage());
        }

        if (!strlen($this->defaultCountry)) {
            $this->defaultCountry = Session::getInstance()->getGeneralSettings()->getCountry();
        }

        foreach ($this->countries->getItems() as $country) {
            $options[] = (new Option($country->getName()))->setValue($country->getCode())->setData($country);
        }

        $this->countrySelector = new FormItem(
            Parameters::COUNTRY,
            (new Select($options, null, $this->defaultCountry))
                ->setData(['physicalLocationsFilter' => $this->physicalLocationsFilter])
                ->setLabelFor($this->languageSheet->getLabelValue(LanguageLabels::COUNTRY))
                ->setClass(FormFactory::CLASS_WILDCARD)
                ->setAttributeWildcard(FormFactory::ATTRIBUTE_WILDCARD)
                ->setAutocomplete(Input::AUTOCOMPLETE_OFF)
        );

        $this->postalCodeInput = new FormItem(
            Parameters::POSTAL_CODE,
            (new InputText())
                ->setMaxlength(50)
                ->setLabelFor($this->languageSheet->getLabelValue(LanguageLabels::POSTAL_CODE))
                ->setId('postalCodeProviderInput')
                ->setClass(FormFactory::CLASS_WILDCARD)->setAttributeWildcard(FormFactory::ATTRIBUTE_WILDCARD)
                ->setRequired(true)
        );

        return $this->getProperties();
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        return [
            'countrySelector' => $this->countrySelector,
            'postalCodeInput' => $this->postalCodeInput
        ];
    }
}
