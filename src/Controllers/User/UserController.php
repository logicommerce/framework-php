<?php

namespace FWK\Controllers\User;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\Controllers\Traits\AddDefaultCountryAndLocationsTrait;
use FWK\Core\Form\FormFactory;
use FWK\Core\Resources\Loader;
use FWK\Core\Resources\Utils;
use FWK\Enums\Services;
use FWK\Services\AccountService;
use FWK\Services\UserService;
use SDK\Application;
use SDK\Core\Resources\BatchRequests;
use SDK\Core\Resources\Cookie;
use SDK\Dtos\Common\Route;
use SDK\Dtos\User\User;

/**
 * This is the base user controller.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @see BaseHtmlController
 * @see AddDefaultCountryAndLocationsTrait
 * 
 * @package FWK\Controllers\User
 */
class UserController extends BaseHtmlController {
    use AddDefaultCountryAndLocationsTrait;

    public const USER_FORM = 'userForm';

    public const USER_CUSTOM_TAGS = 'userCustomTags';

    public const DEFAULT_SELECTED_COUNTRY = 'defaultSelectedCountry';

    public const DEFAULT_SELECTED_COUNTRY_LOCATIONS = 'defaultSelectedCountryLocations';

    public const BASKET = 'basket';

    public const USER_WARNINGS = 'userWarnings';

    private ?UserService $userService = null;

    private ?AccountService $accountService = null;
    /**
     * Constructor method.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->userService = Loader::service(Services::USER);
        $this->accountService = Loader::service(Services::ACCOUNT);
    }

    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
        if (Cookie::exist('basketToken')) {
            if (!Application::getInstance()->getEcommerceSettings()->getAccountRegisteredUsersSettings()->getCardinalityPlus()) {
                $this->userService->addGetUser($requests, self::CONTROLLER_ITEM);
            } else {
                $this->accountService->addGetSession($requests, self::CONTROLLER_ITEM, true);
            }
            // Add basket to the batch requests for reload the basket session
            Loader::service(Services::BASKET)->addGetBasket($requests, self::BASKET);
        } else {
            $this->setDataValue(self::CONTROLLER_ITEM, new User());
        }
        $this->userService->addGetCustomTags($requests, self::USER_CUSTOM_TAGS, self::getTheme()->getConfiguration()->getUser()->getUserCustomTagsParametersGroup());
    }

    /**
     * This method is the one in charge of defining all the data batch requests that
     * are needed for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     * @return void
     */
    protected function setBatchData(BatchRequests $requests): void {
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
    protected function setControllerBaseData(): void {
        $this->setDataValue(self::USER_FORM, FormFactory::setUser(FormFactory::SET_USER_TYPE_ADD_USER, $this->getSession()->getUser(), $this->getControllerData(self::USER_CUSTOM_TAGS)));
        $this->setDataValue(self::DEFAULT_SELECTED_COUNTRY, $this->getDefaultCountry());
        $this->setDataValue(self::DEFAULT_SELECTED_COUNTRY_LOCATIONS, $this->getDefaultCountryLocations());
        $this->setDataValue(self::USER_WARNINGS, Utils::getUserWarnings());
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
