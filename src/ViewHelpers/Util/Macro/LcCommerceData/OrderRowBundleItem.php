<?php

declare(strict_types=1);

namespace FWK\ViewHelpers\Util\Macro\LcCommerceData;

use FWK\Core\ViewHelpers\Macros\Util\LcCommerce\ProductCodes;
use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Dtos\Documents\Rows\TransactionDocumentRowBundleItem;
use SDK\Dtos\Documents\Rows\TransactionDocumentRowProduct;

/**
 * This is the OrderRowBundleItem class, a DTO class for the utilViewHelper LcCommerceData.
 *
 * @package FWK\ViewHelpers\Util\Macro\LcCommerceData
 */
class OrderRowBundleItem {
    use ElementTrait;

    private int $id = 0;

    private string $name = '';

    private int $quantity = 0;

    private string $image = '';

    private string $link = '';

    private ?ProductCodes $codes = null;

    private string $brand = '';

    private array $options = [];

    /**
     * Constructor method for OrderRowBundleItem
     *
     * @param TransactionDocumentRowBundleItem $bundleItem
     */
    public function __construct(TransactionDocumentRowProduct $bundleItem) {
        $this->id = $bundleItem->getId();
        $this->name = $bundleItem->getName();
        $this->quantity = $bundleItem->getQuantity();
        $this->image = $bundleItem->getImage();
        $this->link = $bundleItem->getLink();
        $this->codes = new ProductCodes($bundleItem->getCodes());
        $this->brand = $bundleItem->getBrandName();
        foreach ($bundleItem->getOptions() as $option) {
            $this->options[] = new OrderRowOption($option);
        }
    }
}
