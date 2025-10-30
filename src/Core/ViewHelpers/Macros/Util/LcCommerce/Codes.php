<?php

declare(strict_types=1);

namespace FWK\Core\ViewHelpers\Macros\Util\LcCommerce;

use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Dtos\Common\Codes as CodesDto;

/**
 * This is the Codes class, a DTO class for the utilViewHelpers LcCommerce.
 *
 * @see LcCommerceData::getViewParameters()
 *
 * @package FWK\Core\ViewHelpers\Macros\Util\LcCommerce
 */
class Codes {
    use ElementTrait;

    protected string $sku = '';

    protected string $jan = '';

    protected string $isbn = '';

    protected string $ean = '';

    protected string $upc = '';

    /**
     * Constructor method for Codes
     * 
     * @param CodesDto $codes
     */
    public function __construct(CodesDto $codes) {
        $this->sku = $codes->getSku();
        $this->jan = $codes->getJan();
        $this->isbn = $codes->getIsbn();
        $this->ean = $codes->getEan();
        $this->upc = $codes->getUpc();
    }
}
