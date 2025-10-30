<?php

declare(strict_types=1);

namespace FWK\Core\Interceptors;

use FWK\Core\Resources\Session;
use SDK\Core\Dtos\Element;
use SDK\Core\Resources\Interceptor;
use SDK\Dtos\Basket\Basket;
use SDK\Dtos\Basket\BasketLockedStockTimers;
use SDK\Dtos\Basket\ResponseAddFillProductCollection;
use SDK\Dtos\Basket\ResponseSetRows;
use SDK\Dtos\User\User;

class ServiceResponseElement extends Interceptor {

    public function execute(Element $element): void {
        if ($element instanceof User) {
            if (is_null($element->getError())) {
                Session::getInstance()->setUser($element);
            }
        } elseif ($element instanceof Basket) {
            if (is_null($element->getError())) {
                Session::getInstance()->setBasket($element);
            }
        } elseif ($element instanceof ResponseAddFillProductCollection or $element instanceof ResponseSetRows) {
            if (is_null($element->getError())) {
                Session::getInstance()->setBasket($element->getBasket());
            }
        } elseif ($element instanceof BasketLockedStockTimers) {
            if (is_null($element->getError())) {
                Session::getInstance()->updateLockedStocksAggregateData($element);
            }
        }
    }
}
