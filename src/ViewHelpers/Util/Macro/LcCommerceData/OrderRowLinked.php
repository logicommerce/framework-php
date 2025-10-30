<?php

declare(strict_types=1);

namespace FWK\ViewHelpers\Util\Macro\LcCommerceData;

use SDK\Dtos\Documents\Rows\TransactionDocumentRowLinked;
use SDK\Core\Dtos\Traits\ElementTrait;
use FWK\Core\ViewHelpers\Macros\Util\LcCommerce\ProductCodes;

/**
 * This is the OrderRowLinked class, a DTO class for the utilViewHelper LcCommerceData.
 *
 * @package FWK\ViewHelpers\Util\Macro\LcCommerceData
 */
class OrderRowLinked extends OrderRow {
    use ElementTrait;

    protected string $image = '';

    protected string $link = '';

    protected ?ProductCodes $codes = null;

    protected string $brand = '';

    protected array $options = [];

    /**
     * Constructor method for OrderRowLinked
     *
     * @see DocumentRow
     *
     * @param TransactionDocumentRowLinked $documentRow
     */
    public function __construct(TransactionDocumentRowLinked $documentRow) {
        parent::__construct($documentRow);
        $this->image = $documentRow->getImage();
        $this->link = $documentRow->getLink();
        $this->codes = new ProductCodes($documentRow->getCodes());
        $this->brand = $documentRow->getBrandName();
        foreach ($documentRow->getOptions() as $option) {
            $this->options[] = new OrderRowOption($option);
        }
    }
}
