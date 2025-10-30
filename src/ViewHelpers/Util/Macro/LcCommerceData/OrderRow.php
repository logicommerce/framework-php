<?php

declare(strict_types=1);

namespace FWK\ViewHelpers\Util\Macro\LcCommerceData;

use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Dtos\Documents\Rows\DocumentRow;
use SDK\Enums\BasketRowType;

/**
 * This is the OrderRow class, a DTO class for the utilViewHelper LcCommerceData.
 *
 * @package FWK\ViewHelpers\Util\Macro\LcCommerceData
 */
abstract class OrderRow {
    use ElementTrait;

    protected string $hash = '';

    protected string $type = '';

    protected int $id = 0;

    protected string $name = '';

    protected int $quantity = 0;

    protected ?OrderRowPrices $prices = null;

    /**
     * Constructor method for OrderRow
     *
     * @param OrderRowDto $OrderRow
     */
    public function __construct(DocumentRow $orderRow) {
        $this->type = $orderRow->getType();
        $this->id = $orderRow->getItemId();
        $this->hash = $orderRow->getHash();
        $this->name = $orderRow->getName();
        $this->quantity = $orderRow->getQuantity();
        if ($orderRow->getType() === BasketRowType::BUNDLE) {
            $this->prices = new OrderRowPrices($orderRow->getRichPrices());
        } else {
            $this->prices = new OrderRowProductPrices($orderRow->getRichPrices());
        }
    }
}
