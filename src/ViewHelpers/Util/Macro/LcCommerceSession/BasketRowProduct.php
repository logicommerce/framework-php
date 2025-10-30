<?php

declare(strict_types=1);

namespace FWK\ViewHelpers\Util\Macro\LcCommerceSession;

use FWK\Core\ViewHelpers\Macros\Util\LcCommerce\Combination;
use FWK\Core\ViewHelpers\Macros\Util\LcCommerce\ProductCodes;
use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Dtos\Basket\BasketRows\Product;

/**
 * This is the BasketRowProduct class, a DTO class for the utilViewHelper LcCommerceSession.
 *
 * @package FWK\ViewHelpers\Util\Macro\LcCommerceSession
 */
class BasketRowProduct extends BasketRow {
    use ElementTrait;

    protected string $smallImage = '';

    protected string $largeImage = '';

    protected string $urlSeo = '';

    protected bool $stockAvailable = false;

    protected ?ProductCodes $codes = null;

    protected ?Combination $combination = null;

    protected string $brand = '';

    protected array $options = [];

    /**
     * Constructor method for BasketRowProduct
     * 
     * @see BasketRow
     *  
     * @param Product $basketRow
     */
    public function __construct(Product $basketRow) {
        parent::__construct($basketRow);
        $this->smallImage = $basketRow->getImages()->getSmallImage();
        $this->largeImage = $basketRow->getImages()->getLargeImage();
        $this->urlSeo = $basketRow->getUrlSeo();
        $this->stockAvailable = !empty(array_filter($basketRow->getCombination()->getStocks(), fn ($item) => $item->getUnits() > 0));


        $this->codes = new ProductCodes($basketRow->getCodes());
        $this->combination = new Combination($basketRow->getCombination());

        $this->brand = $basketRow->getBrandName();

        foreach ($basketRow->getOptions() as $option) {
            $this->options[] = new BasketRowOption($option);
        }
    }
}
