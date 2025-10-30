<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Element;
use SDK\Core\Dtos\Traits\ElementTrait;

/**
 * This is the 'CommerceLockedStock' class, a DTO class for the theme configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see Element
 *
 * @package FWK\Core\Theme\Dtos
 */
class CommerceLockedStock extends Element {
    use ElementTrait;

    public const EXTEND_BY_ROUTE_VISITED = 'extendByRouteVisited';

    public const EXTEND_BY_ROUTE_VISITED_RESET_COUNTER = 'extendByRouteVisitedResetCounter';

    public const EXTEND_BY_ROUTE_VISITED_TIME = 'extendByRouteVisitedTime';

    public const EXTEND_BY_BASKET_CHANGE = 'extendByBasketChange';

    public const EXTEND_BY_BASKET_CHANGE_RESET_COUNTER = 'extendByBasketChangeResetCounter';

    public const EXTEND_BY_BASKET_CHANGE_TIME = 'extendByBasketChangeTime';

    public const EXTEND_BY_PAYMENT_GATEWAY_VISITED = 'extendByPaymentGatewayVisited';

    public const EXTEND_BY_PAYMENT_GATEWAY_VISITED_RESET_COUNTER = 'extendByPaymentGatewayVisitedResetCounter';

    public const EXTEND_BY_PAYMENT_GATEWAY_VISITED_TIME = 'extendByPaymentGatewayVisitedTime';

    public const SHOW_EXTEND_BUTTON = 'showExtendButton';

    public const WARNING_POPUP = 'warningPopup';

    public const WARNING_POPUP_TIME_THRESHOLD = 'warningPopupTimeThreshold';

    public const EXPIRED_POPUP = 'expiredPopup';

    protected bool $extendByRouteVisited = false;

    protected bool $extendByRouteVisitedResetCounter = false;

    protected int $extendByRouteVisitedTime = 0;

    protected bool $extendByBasketChange = false;

    protected bool $extendByBasketChangeResetCounter = false;

    protected int $extendByBasketChangeTime = 0;

    protected bool $extendByPaymentGatewayVisited = false;

    protected bool $extendByPaymentGatewayVisitedResetCounter = false;

    protected int $extendByPaymentGatewayVisitedTime = 0;

    protected bool $showExtendButton = false;

    protected bool $warningPopup = false;

    protected int $warningPopupTimeThreshold = 0;

    protected bool $expiredPopup = false;

    /**
     * This method returns if the stock locked time should be extended by route visited
     *
     * @return bool
     */
    public function getExtendByRouteVisited(): bool {
        return $this->extendByRouteVisited;
    }

    /**
     * This method returns if the stock locked time should be extended to the initial value when the route is visited
     *
     * @return bool
     */
    public function getExtendByRouteVisitedResetCounter(): bool {
        return $this->extendByRouteVisitedResetCounter;
    }


    /**
     * This method returns the time in minutes to extend the stock locked time by route visited
     *
     * @return int
     */
    public function getExtendByRouteVisitedTime(): int {
        return $this->extendByRouteVisitedTime;
    }


    /**
     * This method returns if the stock locked time should be extended by basket change
     *
     * @return bool
     */
    public function getExtendByBasketChange(): bool {
        return $this->extendByBasketChange;
    }


    /**
     * This method returns if the stock locked time should be extended to the initial value when the basket is changed
     *
     * @return bool
     */
    public function getExtendByBasketChangeResetCounter(): bool {
        return $this->extendByBasketChangeResetCounter;
    }


    /**
     * This method returns the time in minutes to extend the stock locked time by basket change
     *
     * @return int
     */
    public function getExtendByBasketChangeTime(): int {
        return $this->extendByBasketChangeTime;
    }


    /**
     * This method returns if the stock locked time should be extended by payment gateway visited
     *
     * @return bool
     */
    public function getExtendByPaymentGatewayVisited(): bool {
        return $this->extendByPaymentGatewayVisited;
    }


    /**
     * This method returns if the stock locked time should be extended to the initial value when the payment gateway is visited
     *
     * @return bool
     */
    public function getExtendByPaymentGatewayVisitedResetCounter(): bool {
        return $this->extendByPaymentGatewayVisitedResetCounter;
    }


    /**
     * This method returns the time in minutes to extend the stock locked time by payment gateway visited
     *
     * @return int
     */
    public function getExtendByPaymentGatewayVisitedTime(): int {
        return $this->extendByPaymentGatewayVisitedTime;
    }


    /**
     * This method returns if the extend button should be shown
     *
     * @return bool
     */
    public function getShowExtendButton(): bool {
        return $this->showExtendButton;
    }


    /**
     * This method returns if the warning popup should be shown 
     *
     * @return bool
     */
    public function getWarningPopup(): bool {
        return $this->warningPopup;
    }


    /**
     * This method returns the warning popup time threshold in Time
     *
     * @return bool
     */
    public function getWarningPopupTimeThreshold(): bool {
        return $this->warningPopupTimeThreshold;
    }


    /**
     * This method returns if the expired popup should be shown
     *
     * @return bool
     */
    public function getExpiredPopup(): bool {
        return $this->expiredPopup;
    }
}
