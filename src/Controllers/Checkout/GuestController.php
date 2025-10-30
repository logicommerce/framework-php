<?php

namespace FWK\Controllers\Checkout;

use FWK\Core\Form\FormFactory;
use FWK\Core\Resources\Response;
use FWK\Core\Resources\RoutePaths;
use FWK\Core\Resources\Utils;
use SDK\Core\Resources\BatchRequests;
use SDK\Enums\RouteType;

/**
 * This is the checkout guest user controller.
 *
 * @see CustomerController
 *
 * @package FWK\Controllers\Checkout
 */
class GuestController extends CustomerController {

    private const GUEST_FORM = 'guestForm';

    protected function checkRedirect() {
        $sessionUser = $this->getSession()->getUser();
        if (Utils::isSessionLoggedIn($this->getSession())) {
            Response::redirect(RoutePaths::getPath(RouteType::CHECKOUT_CUSTOMER));
        }
    }

    /**
     * This private method sends the batch requests of the controller to the SDK to obtain the data
     * and returns the batch result
     *
     * @return array with the result of the batch request
     */
    protected function setBatchData(BatchRequests $request): void {
    }

    /**
     * This method runs after the batch requests (defined in the setBatchData methods) are resolved,
     * so here you can work with the response of the batch requests and calculate and set more
     * needed data.
     *
     * @param array $additionalData
     *              Set additiona data to the controller data
     * 
     * @return void
     */
    protected function setData(array $additionalData = []): void {
    }

    /**
     * This method is in charge of defining the basic data necessary for the correct operation of
     * the controller.
     */
    protected function setControllerBaseData(): void {
        parent::setControllerBaseData();

        $this->setDataValue(
            self::GUEST_FORM,
            FormFactory::setUser(
                FormFactory::SET_USER_TYPE_ADD_CUSTOMER,
                $this->getSession()->getUser(),
                $this->getControllerData(self::USER_CUSTOM_TAGS)
            )
        );
    }
}
