<?php

namespace FWK\Core\Dtos;

use SDK\Core\Dtos\CustomTag as SDKCustomTag;

/**
 * This is the Custom Tag class.
 *
 * @see SDK\Core\Dtos\CustomTag
 *
 * @package FWK\Core\Dtos
 */
class CustomTag extends SDKCustomTag {

    protected string $value = '';

    /**
     * Returns the custom tag value.
     *
     * @return string
     */
    public function getValue(): string {
        return $this->value;
    }

    public function setValue(string $value): void {
        $this->value = $value;
    }
}
