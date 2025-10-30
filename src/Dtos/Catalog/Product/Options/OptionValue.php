<?php

namespace FWK\Dtos\Catalog\Product\Options;

use SDK\Dtos\Catalog\Product\Options\OptionValue as SDKOptionValue;

/**
 * This is the OptionValue class
 *
 * @see OptionValue::getNotAvailable()
 * @see OptionValue::setNotAvailable()
 * 
 * @see SDK\Dtos\Catalog\Product\Options\OptionValue
 * 
 * @package FWK\Dtos\Catalog\Product\Options
 */
class OptionValue extends SDKOptionValue {

    protected bool $notAvailable = false;

    /**
     * Returns the notAvailable
     *
     * @return bool
     */
    public function getNotAvailable(): bool {
        return $this->notAvailable;
    }

    /**
     * Sets the notAvailable
     *
     * @param bool 
     */
    public function setNotAvailable(bool $notAvailable): void {
        $this->notAvailable = $notAvailable;
    }
}
