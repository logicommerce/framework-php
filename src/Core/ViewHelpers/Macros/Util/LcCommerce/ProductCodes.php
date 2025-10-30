<?php

declare(strict_types=1);

namespace FWK\Core\ViewHelpers\Macros\Util\LcCommerce;

use FWK\Core\ViewHelpers\Macros\Util\LcCommerce\Codes;
use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Dtos\Common\Codes as CodesDto;

/**
 * This is the ProductCodes class, a DTO class for the utilViewHelpers LcCommerce.
 *
 * @see LcCommerceData::getViewParameters()
 *
 * @package FWK\ViewHelpers\Util\Macro\LcCommerceData;
 */
class ProductCodes extends Codes {
    use ElementTrait;

    private string $manufacturerSku = '';

    /**
     * Constructor method for ProductCodes
     * 
     * @see Codes
     * 
     * @param CodesDto $codes
     */
    public function __construct(CodesDto $codes) {
        parent::__construct($codes);
        if (method_exists($codes, 'getManufacturerSku')) {
            $this->manufacturerSku = $codes->getManufacturerSku();
        }
    }
}
