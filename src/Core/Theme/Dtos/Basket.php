<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Element;
use SDK\Core\Dtos\Traits\ElementTrait;

/**
 * This is the 'Basket' class, a DTO class for the theme configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see Element
 *
 * @package FWK\Core\Theme\Dtos
 */
class Basket extends Element {
	use ElementTrait;

	public const RECOVERY_BASKET_ROUTE_TYPE_REDIRECT = 'recoveryBasketRouteTypeRedirect';

	private string $recoveryBasketRouteTypeRedirect = '';

	/**
	 * This method returns the recovery basket RouteType redirect .
	 *
	 * @return string
	 */
	public function getRecoveryBasketRouteTypeRedirect(): string {
	    return $this->recoveryBasketRouteTypeRedirect;
	}

}
