<?php

namespace FWK\Controllers\Checkout\Internal;

use FWK\Core\Controllers\SetUserController;
use FWK\Core\Form\FormFactory;
use FWK\Core\Resources\RoutePaths;
use FWK\Enums\RouteType;

/**
 * This is the AddCustomerController controller class.
 * This class extends FWK\Core\Controllers\SetUserController, see this class.
 *
 * @see SetUserController
 *
 * @package FWK\Controllers\Checkout\Internal
 */
class AddCustomerController extends SetUserController {

    /**
     *
     * @see \FWK\Core\Controllers\SetUserController::getTypeForm()
     */
    protected function getTypeForm(): string {
        return FormFactory::SET_USER_TYPE_ADD_CUSTOMER;
    }

    /**
     *
     * @see \FWK\Core\Controllers\SetUserController::getUrlRedirect()
     */
    protected function getUrlRedirect(): string {
        return RoutePaths::getPath(RouteType::CHECKOUT_PAYMENT_AND_SHIPPING);
    }
}
