<?php

namespace FWK\Controllers\User;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\Controllers\Traits\AddDefaultCountryAndLocationsTrait;
use FWK\Core\Form\FormFactory;
use FWK\Enums\Services;
use FWK\Core\Resources\Loader;
use SDK\Core\Resources\BatchRequests;
use FWK\Core\Resources\Response;
use FWK\Core\Resources\RoutePaths;
use FWK\Core\Resources\Utils;
use FWK\Enums\RouteType;
use SDK\Application;
use SDK\Core\Resources\Cookie;
use SDK\Dtos\Common\Route;
use SDK\Dtos\User\User;

/**
 * This is the user create account controller.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @controllerData: 
 *  <p>self::CREATE_ACCOUNT_FORM: \FWK\Core\Form\FormFactory::setUser(FormFactory::SET_USER_TYPE_ADD_USER, $this->getSession()->getUser(), $this->getControllerData(self::USER_CUSTOM_TAGS))</p>
 *  <p>self::USER_CUSTOM_TAGS: \SDK\Core\Dtos\ElementCollection of \SDK\Core\Dtos\CustomTag</p>
 *  <p>self::DEFAULT_SELECTED_COUNTRY: \SDK\Dtos\Settings\CountrySettings</p>
 *  <p>self::DEFAULT_SELECTED_COUNTRY_LOCATIONS: array of \SDK\Dtos\CountryLocation</p>
 * 
 * @twigContent: \themes\{{themeName}}\{{version}}\Content\User\CreateAccount\default.html.twig
 * 
 * @RouteType: \SDK\Enums\RouteType::USER_CREATE_ACCOUNT
 * 
 * @see AddDefaultCountryAndLocationsTrait
 *
 * @package FWK\Controllers\User
 */
class CreateAccountController extends BaseHtmlController {
    use AddDefaultCountryAndLocationsTrait;

    public const CREATE_ACCOUNT_FORM = 'createAccountForm';

    public const USER_CUSTOM_TAGS = 'userCustomTags';

    public const DEFAULT_SELECTED_COUNTRY = 'defaultSelectedCountry';

    public const DEFAULT_SELECTED_COUNTRY_LOCATIONS = 'defaultSelectedCountryLocations';

    /**
     * Constructor method.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        if (Utils::isSessionLoggedIn($this->getSession())) {
            Response::redirect(RoutePaths::getPath(RouteType::USER));
        }
    }

    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
        $userService = Loader::service(Services::USER);
        $accountService = Loader::service(Services::ACCOUNT);
        if (Cookie::exist('basketToken')) {
            if (!Application::getInstance()->getEcommerceSettings()->getAccountRegisteredUsersSettings()->getCardinalityPlus()) {
                $userService->addGetUser($requests, self::CONTROLLER_ITEM);
            } else {
                $accountService->addGetSession($requests, self::CONTROLLER_ITEM, true);
            }
        } else {
            $this->setDataValue(self::CONTROLLER_ITEM, new User());
        }
        $userService->addGetCustomTags($requests, self::USER_CUSTOM_TAGS, self::getTheme()->getConfiguration()->getUser()->getUserCustomTagsParametersGroup());
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
        $this->setDataValue(self::CREATE_ACCOUNT_FORM, FormFactory::setUser(FormFactory::SET_USER_TYPE_ADD_USER, $this->getSession()->getUser(), $this->getControllerData(self::USER_CUSTOM_TAGS)));
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
}
