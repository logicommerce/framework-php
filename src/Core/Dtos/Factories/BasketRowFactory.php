<?php

namespace FWK\Core\Dtos\Factories;

use SDK\Core\Dtos\Factories\BasketRowFactory as SDKBasketRowFactory;

/**
 * This class will return the kind of basket row we need.
 *
 * @see SDK\Core\Dtos\Factories\BasketRowFactory
 *
 * @package FWK\Dtos\Factories
 */
abstract class BasketRowFactory extends SDKBasketRowFactory {

    protected const NAMESPACE = 'FWK\Dtos\Basket\BasketRows';

}
