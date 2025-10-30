<?php

declare(strict_types=1);

namespace FWK\ViewHelpers\Util\Macro\LcCommerceData;

use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Dtos\Documents\Rows\TransactionDocumentRowBundle;

/**
 * This is the Document class, a DTO class for the utilViewHelper LcCommerceData.
 *
 * @package FWK\ViewHelpers\Util\Macro\LcCommerceData
 */
class OrderRowBundle extends OrderRow {
    use ElementTrait;

    private array $items = [];

    /**
     * Constructor method for OrderRowBundle
     *
     * @see DocumentRow
     *
     * @param Bundle $DocumentRow
     */
    public function __construct(TransactionDocumentRowBundle $documentRow) {
        parent::__construct($documentRow);
        foreach ($documentRow->getItems() as $item) {
            $this->items[] = new OrderRowBundleItem($item);
        }
    }
}
