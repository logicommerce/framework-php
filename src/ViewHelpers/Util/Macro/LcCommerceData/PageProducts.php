<?php

declare(strict_types=1);

namespace FWK\ViewHelpers\Util\Macro\LcCommerceData;

use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Core\Dtos\ElementCollection;

/**
 * This is the PageProducts class, a DTO class for the utilViewHelper LcCommerceData.
 *
 * @package FWK\ViewHelpers\Util\Macro\LcCommerceData
 */
class PageProducts {
    use ElementTrait;

    private int $id = 0;

    private string $pId = '';

    private string $name = '';

    private array $products = [];

    /**
     * 
     * Constructor method for PageProducts
     *
     * @param ?ElementCollection $products
     */
    public function __construct(?ElementCollection $products = null) {
        foreach ($products as $product) {
            $this->products[] = new Product($product);
        }
    }
}
