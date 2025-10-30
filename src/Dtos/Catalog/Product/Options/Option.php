<?php

namespace FWK\Dtos\Catalog\Product\Options;

use SDK\Dtos\Catalog\Product\Options\Option as SDKOption;

/**
 * This is the Option class
 *
 * @see Option::getSelectedOptionValueId()
 * @see Option::setSelectedOptionValueId()
 *
 * @see SDK\Dtos\Catalog\Product\Options\Option
 * 
 * @package FWK\Dtos\Catalog\Product\Options
 */
class Option extends SDKOption {

    protected array|int|bool|null $selectedOptionValueId = null;

    protected mixed $optionReferenceValue = '';

    /**
     * Returns the selectedOptionValueId
     *
     * @return array|int|bool|null
     */
    public function getSelectedOptionValueId(): array|int|bool|null {
        return $this->selectedOptionValueId;
    }

    /**
     * Returns the optionReferenceValue
     *
     * @return mixed
     */
    public function getOptionReferenceValue(): mixed {
        return $this->optionReferenceValue;
    }

    /**
     * Sets the selectedOptionValueId
     *
     * @param array|int|bool|null 
     */
    public function setSelectedOptionValueId(array|int|bool|null $selectedOptionValueId): void {
        $this->selectedOptionValueId = $selectedOptionValueId;
    }

    /**
     * Sets the optionReferenceValue
     *
     * @param mixed 
     */
    public function setOptionReferenceValue(mixed $optionReferenceValue): void {
        $this->optionReferenceValue = $optionReferenceValue;
    }

    protected function setValues(array $values): void {
        $this->values = $this->setArrayField($values, OptionValue::class);
    }
}
