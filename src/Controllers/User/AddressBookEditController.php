<?php

namespace FWK\Controllers\User;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\Controllers\ControllersFactory;
use FWK\Core\Controllers\Traits\AddDefaultCountryAndLocationsTrait;
use FWK\Core\Resources\Loader;
use FWK\Enums\Services;
use SDK\Core\Resources\BatchRequests;
use FWK\Core\Form\FormFactory;
use FWK\Enums\Parameters;
use FWK\Services\UserService;
use SDK\Dtos\Common\Route;

/**
 * This is the user address book edit controller.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @controllerData: 
 *  <p>self::ADDRESS_FORM: \FWK\Core\Form\FormFactory::getAddress($type)</p>
 *  <p>self::PREFIX: string $type</p>
 *  <p>self::DEFAULT_SELECTED_COUNTRY: \SDK\Dtos\Settings\CountrySettings</p>
 *  <p>self::DEFAULT_SELECTED_COUNTRY_LOCATIONS: array of \SDK\Dtos\CountryLocation</p>
 * 
 * @twigContent: \themes\{{themeName}}\{{version}}\Content\User\AddressBookEdit\default.html.twig
 * 
 * @RouteType: \SDK\Enums\RouteType::USER_ADDRESS_BOOK_EDIT
 * 
 * @see AddDefaultCountryAndLocationsTrait
 *
 * @package FWK\Controllers\User
 */
class AddressBookEditController extends BaseHtmlController {
    use AddDefaultCountryAndLocationsTrait;

    public const ADDRESS_FORM = 'addressForm';

    public const PREFIX = 'prefix';

    public const DEFAULT_SELECTED_COUNTRY = 'defaultSelectedCountry';

    public const DEFAULT_SELECTED_COUNTRY_LOCATIONS = 'defaultSelectedCountryLocations';

    private ?UserService $userService = null;

    public array $ids = [];

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
        $this->ids = ControllersFactory::extractIdsFromUrl($this->getRoute());
        if (count($this->ids) > 0) {
            $this->userService->addGetAddress($requests, self::CONTROLLER_ITEM, $this->ids[Parameters::ADDRESS_ID]);
        } else {
            $this->userService->addGetAddress($requests, self::CONTROLLER_ITEM, $this->getRoute()->getId());
        }
    }

    /**
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     *
     */
    protected function setControllerBaseData(): void {
        $address = $this->getControllerData(self::CONTROLLER_ITEM);
        $type = strtolower($address->getType());
        $this->setDataValue(self::PREFIX, $type);
        $this->setDataValue(self::ADDRESS_FORM, FormFactory::getAddress($type, $address));
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
