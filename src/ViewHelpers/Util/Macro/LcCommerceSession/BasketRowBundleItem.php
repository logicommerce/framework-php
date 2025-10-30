<?php

declare(strict_types=1);

namespace FWK\ViewHelpers\Util\Macro\LcCommerceSession;

use FWK\Core\ViewHelpers\Macros\Util\LcCommerce\Combination;
use FWK\Core\ViewHelpers\Macros\Util\LcCommerce\ProductCodes;
use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Dtos\Basket\BasketRows\Bundle\BundleItem;

/**
 * This is the BasketRowBundleItem class, a DTO class for the utilViewHelper LcCommerceSession.
 *
 * @package FWK\ViewHelpers\Util\Macro\LcCommerceSession
 */
class BasketRowBundleItem {
    use ElementTrait;

    private int $id = 0;

    private string $name = '';

    private int $quantity = 0;

    private string $smallImage = '';

    private string $largeImage = '';

    private string $urlSeo = '';

    private bool $stockAvailable = false;

    private ?ProductCodes $codes = null;

    private ?Combination $combination = null;

    private string $brand = '';

    private array $options = [];

    /**
     * Constructor method for BasketRowBundleItem
     *
     * @param BundleItem $bundleItem
     */
    public function __construct(BundleItem $bundleItem) {
        $this->id = $bundleItem->getId();
        $this->name = $bundleItem->getName();
        $this->quantity = $bundleItem->getQuantity();
        $this->smallImage = $bundleItem->getImages()->getSmallImage();
        $this->largeImage = $bundleItem->getImages()->getLargeImage();
        $this->urlSeo = $bundleItem->getUrlSeo();
        $this->stockAvailable = !empty(array_filter($bundleItem->getCombination()->getStocks(), fn ($item) => $item->getUnits() > 0));
        $this->codes = new ProductCodes($bundleItem->getCodes());
        $this->combination = new Combination($bundleItem->getCombination());
        $this->brand = $bundleItem->getBrandName();
        foreach ($bundleItem->getOptions() as $option) {
            $this->options[] = new BasketRowOption($option);
        }
    }
}
