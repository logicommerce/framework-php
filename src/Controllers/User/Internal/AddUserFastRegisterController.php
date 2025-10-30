<?php

namespace FWK\Controllers\User\Internal;

use FWK\Core\Controllers\SetUserController;
use FWK\Core\Form\FormFactory;
use FWK\Enums\RouteType;
use FWK\Core\Resources\RoutePaths;

/**
 * This is the AddUserFastRegisterController controller class.
 * This class extends FWK\Core\Controllers\SetUserController, see this class.
 *
 * @see SetUserController
 *
 * @package FWK\Controllers\User\Internal
 */
class AddUserFastRegisterController extends SetUserController {

    /**
     *
     * @see \FWK\Core\Controllers\SetUserController::getTypeForm()
     */
    protected function getTypeForm(): string {
        return FormFactory::SET_USER_TYPE_ADD_USER_FAST_REGISTER;
    }

    /**
     *
     * @see \FWK\Core\Controllers\SetUserController::getUrlRedirect()
     */
    protected function getUrlRedirect(): string {
        return RoutePaths::getPath(RouteType::USER);
    }
}
