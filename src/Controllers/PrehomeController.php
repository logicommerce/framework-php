<?php

namespace FWK\Controllers;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\Form\FormFactory;
use SDK\Application;
use SDK\Core\Resources\BatchRequests;
use SDK\Services\Parameters\Groups\Settings\CountriesLinksParametersGroup;

/**
 * This is the PrehomeController class.
 * This class extends BaseHtmlController, see this class.
 *
 * @controllerData:
 *      <p>self::CONTROLLER_ITEM: array</p>
 *      <p>self::CONTROLLER_ITEM[self::COUNTRIES_LINKS] => SDK\Core\Dtos\ElementCollection of SDK\Dtos\Settings\CountryLink</p>
 *      <p>self::CONTROLLER_ITEM[self::COUNTRIES_LINKS_FORM] => FWK\Core\Form\FormFactory::getCountriesLinks($countriesLinks)</p>
 * 
 * @twigContent: \themes\{{themeName}}\{{version}}\Content\Prehome\default.html.twig
 * 
 * @RouteType: \SDK\Enums\RouteType::PREHOME
 * 
 * @see BaseHtmlController
 *
 * @package FWK\Controllers
 */
class PrehomeController extends BaseHtmlController {

    public const COUNTRIES_LINKS = 'countriesLinks';

    public const COUNTRIES_LINKS_FORM = 'countriesLinksForm';

    protected ?CountriesLinksParametersGroup $countriesLinksParametersGroup = null;

    /**
     * This method initialize applied parameters, runs previously to run preSendControllerBaseBatchData
     *
     */
    protected function initializeAppliedParameters(): void {
        $this->countriesLinksParametersGroup = new CountriesLinksParametersGroup();
        $this->countriesLinksParametersGroup->setLanguageCode($this->getRoute()->getLanguage());
        $this->countriesLinksParametersGroup->setAllCountries(true);
    }

    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
    }

    /**
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     */
    protected function setControllerBaseData(): void {
        $countriesLinks = Application::getInstance()->getCountriesLinks($this->countriesLinksParametersGroup);
        $this->setDataValue(self::CONTROLLER_ITEM, [
            self::COUNTRIES_LINKS => $countriesLinks,
            self::COUNTRIES_LINKS_FORM => FormFactory::getCountriesLinks($countriesLinks)
        ]);
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
