<?php

namespace FWK\Controllers\Checkout;

use FWK\Core\Controllers\Traits\CheckoutRedirectTrait;
use FWK\Core\Form\FormFactory;
use FWK\Core\Resources\Loader;
use FWK\Enums\Services;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Common\Route;
use FWK\Enums\RouteType;
use FWK\Core\Resources\Response;
use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\Controllers\Traits\AddDefaultCountryAndLocationsTrait;
use FWK\Core\Resources\RoutePaths;
use FWK\Core\Resources\Session;
use FWK\Core\Resources\Utils;

/**
 * This is the checkout create user account controller.
 * This class extends UserCreateAccountController (FWK\Controllers\User\CreateAccountController), see this class.
 *
 * @see UserCreateAccountController
 * @see AddDefaultCountryAndLocationsTrait
 *
 * @package FWK\Controllers\Checkout
 */
class CreateAccountController extends BaseHtmlController {
    use CheckoutRedirectTrait {
        __construct as __constructCheckoutRedirectTrait;
    }
    use AddDefaultCountryAndLocationsTrait;

    public const CREATE_ACCOUNT_FORM = 'createAccountForm';

    public const DEFAULT_SELECTED_COUNTRY = 'defaultSelectedCountry';

    public const DEFAULT_SELECTED_COUNTRY_LOCATIONS = 'defaultSelectedCountryLocations';

    public const USER_CUSTOM_TAGS = 'userCustomTags';

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        $this->__constructCheckoutRedirectTrait($route);
        $sessionUser = $this->getSession()->getUser();
        // is guest user in session
        if ($sessionUser->getId() === 0 && strlen($sessionUser->getEmail()) > 0) {
            Response::redirect(RoutePaths::getPath(RouteType::CHECKOUT_GUEST));
        } elseif (Utils::isSessionLoggedIn($this->getSession())) {
            Response::redirect(RoutePaths::getPath(RouteType::CHECKOUT_CUSTOMER));
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
        Loader::service(Services::USER)->addGetCustomTags($requests, self::USER_CUSTOM_TAGS, self::getTheme()->getConfiguration()->getUser()->getUserCustomTagsParametersGroup());
    }

    /**
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     *
     */
    protected function setControllerBaseData(): void {
        if (empty(Session::getInstance()->getBasket()->getItems())) {
            Response::redirect(RoutePaths::getPath(RouteType::CHECKOUT_BASKET));
        }
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

        $this->setDataValue(self::CREATE_ACCOUNT_FORM, FormFactory::setUser(FormFactory::SET_USER_TYPE_ADD_CUSTOMER, $this->getSession()->getUser(), $this->getControllerData(self::USER_CUSTOM_TAGS)));
        $this->setDataValue(self::DEFAULT_SELECTED_COUNTRY, $this->getDefaultCountry());
        $this->setDataValue(self::DEFAULT_SELECTED_COUNTRY_LOCATIONS, $this->getDefaultCountryLocations());
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
