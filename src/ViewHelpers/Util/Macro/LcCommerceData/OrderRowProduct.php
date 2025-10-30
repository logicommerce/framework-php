<?php

declare(strict_types=1);

namespace FWK\ViewHelpers\Util\Macro\LcCommerceData;

use FWK\Core\ViewHelpers\Macros\Util\LcCommerce\ProductCodes;
use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Dtos\Documents\Rows\TransactionDocumentRowProduct;

/**
 * This is the OrderRowProduct class, a DTO class for the utilViewHelper LcCommerceData.
 *
 * @package FWK\ViewHelpers\Util\Macro\LcCommerceData
 */
class OrderRowProduct extends OrderRow {
    use ElementTrait;

    protected string $image = '';

    protected string $link = '';

    protected ?ProductCodes $codes = null;

    protected string $brand = '';

    protected array $options = [];

    /**
     * Constructor method for OrderRowProduct
     *
     * @see DocumentRow
     *
     * @param TransactionDocumentRowProduct $documentRow
     */
    public function __construct(TransactionDocumentRowProduct $documentRow) {
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
