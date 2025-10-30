<?php

declare(strict_types=1);

namespace FWK\Core\Controllers\Traits;

use FWK\Core\Resources\Response;
use FWK\Core\Resources\RoutePaths;
use FWK\Core\Theme\Theme;
use SDK\Dtos\Common\Route;
use SDK\Enums\RouteType;

/**
 * This is the CheckoutRedirectTrait trait. It contains a useful method to redirect to the correct checkout for the current commerce using it's configuration in _config.php file.
 *
 * @see FWKCategoryController
 *
 * @package FWK\Core\Controllers\Traits
 */
trait CheckoutRedirectTrait {

    /**
     * 
     * 
     * This overridden constructor will check the navigation checkout and the configured one (in _config.php) and switch (redirect) to the other if needed.
     */
    public function __construct(Route $route) {
        $isOSC = $route->getType() === RouteType::CHECKOUT;
        $useOSC = Theme::getInstance()->getConfiguration()->getCommerce()->getUseOneStepCheckout();
        $isExpressCheckout = $route->getType() === RouteType::EXPRESS_CHECKOUT_CANCEL || $route->getType() === RouteType::EXPRESS_CHECKOUT_RETURN;
        if (($isOSC || $isExpressCheckout) && !$useOSC) {
            Response::redirect(RoutePaths::getPath(RouteType::CHECKOUT_BASKET));
        } else if ((!$isOSC || $isExpressCheckout) && $useOSC) {
            Response::redirect(RoutePaths::getPath(RouteType::CHECKOUT));
        }
        parent::__construct($route);
    }
}
