<?php

declare(strict_types=1);

namespace FWK\ViewHelpers\Util\Macro\LcCommerceSession;

use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Dtos\Basket\BasketRow as BasketRowDto;

/**
 * This is the BasketRow class, a DTO class for the utilViewHelper LcCommerceSession.
 *
 * @package FWK\ViewHelpers\Util\Macro\LcCommerceSession
 */
abstract class BasketRow {
    use ElementTrait;

    protected string $hash = '';

    protected string $type = '';

    protected int $id = 0;

    protected string $name = '';

    protected int $quantity = 0;

    protected ?Prices $prices = null;

    protected ?Prices $pricesWithTaxes = null;

    /**
     * Constructor method for BasketRow
     *
     * @param BasketRowDto $basketRow
     */
    public function __construct(BasketRowDto $basketRow) {
        $this->type = $basketRow->getType();
        $this->id = $basketRow->getId();
        $this->hash = $basketRow->getHash();
        $this->name = $basketRow->getName();
        $this->quantity = $basketRow->getQuantity();
        $this->prices = new Prices($basketRow->getPrices());
        $this->pricesWithTaxes = new Prices($basketRow->getPricesWithTaxes());
    }
}
