<?php

namespace FWK\Controllers\Checkout;

use FWK\Core\Controllers\Traits\CheckoutRedirectTrait;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Common\Route;
use SDK\Enums\RouteType;
use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\Controllers\Traits\AddDefaultCountryAndLocationsTrait;
use FWK\Core\Form\FormFactory;
use FWK\Core\Resources\Loader;
use FWK\Core\Resources\Response;
use FWK\Enums\Services;
use FWK\Core\Resources\RoutePaths;
use FWK\Core\Resources\Session;
use FWK\Services\UserService;

/**
 * This is the checkout customer controller.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @see BaseHtmlController
 * @see AddDefaultCountryAndLocationsTrait
 *
 * @package FWK\Controllers\Checkout
 */
class CustomerController extends BaseHtmlController {
    use AddDefaultCountryAndLocationsTrait;

    use CheckoutRedirectTrait {
        __construct as __constructCheckoutRedirectTrait;
    }

    private ?UserService $userService = null;

    public const BASKET = 'basket';

    public const CUSTOMER_FORM = 'customerForm';

    public const BILLING_ADDRESSES = 'billingAddresses';

    public const SHIPPING_ADDRESSES = 'shippingAddresses';

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
        $this->checkRedirect();
        $this->userService = Loader::service(Services::USER);
    }

    protected function checkRedirect() {
        $sessionUser = $this->getSession()->getUser();
        // is guest user in session
        if ($sessionUser->getId() === 0 && strlen($sessionUser->getEmail()) > 0) {
            Response::redirect(RoutePaths::getPath(RouteType::CHECKOUT_GUEST));
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
        $this->userService->addGetBillingAddresses($requests, self::BILLING_ADDRESSES);
        $this->userService->addGetShippingAddresses($requests, self::SHIPPING_ADDRESSES);
        $this->userService->addGetCustomTags($requests, self::USER_CUSTOM_TAGS, self::getTheme()->getConfiguration()->getUser()->getUserCustomTagsParametersGroup());
        Loader::service(Services::BASKET)->addGetBasket($requests, self::BASKET);
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
        $this->setDataValue(self::CONTROLLER_ITEM, [
            self::BILLING_ADDRESSES => $this->getControllerData(self::BILLING_ADDRESSES),
            self::SHIPPING_ADDRESSES => $this->getControllerData(self::SHIPPING_ADDRESSES)
        ]);

        $this->setDataValue(self::CUSTOMER_FORM, FormFactory::setUser(FormFactory::SET_USER_TYPE_ADD_CUSTOMER, $this->getSession()->getUser(), $this->getControllerData(self::USER_CUSTOM_TAGS)));
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
