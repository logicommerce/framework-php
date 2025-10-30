<?php

declare(strict_types=1);

namespace FWK\ViewHelpers\Util\Macro\LcCommerceSession;

use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Dtos\Basket\BasketRows\Option;
use SDK\Enums\OptionType;

/**
 * This is the BasketRowOption class, a DTO class for the utilViewHelper LcCommerceSession.
 *
 * @package FWK\ViewHelpers\Util\Macro\LcCommerceSession
 */
class BasketRowOption {
    use ElementTrait;

    private int $id = 0;

    private string $name = '';

    private string $type = '';

    private mixed $value = '';

    private array $values = [];

    private array $valueList = [];

    /**
     * Constructor method for BasketRowOption
     *
     * @param Option $basket
     */
    public function __construct(Option $option) {
        $this->type = $option->getType();
        $this->id = $option->getId();
        $this->name = $option->getName();
        if (in_array($this->type, [OptionType::BOOLEAN, OptionType::SHORT_TEXT, OptionType::DATE, OptionType::LONG_TEXT])) {
            $this->value = $option->getValue();
        } elseif (in_array($this->type, [OptionType::SINGLE_SELECTION, OptionType::SINGLE_SELECTION_IMAGE, OptionType::SELECTOR])) {
            $this->value = new BasketRowOptionValue($option->getValue());
        } elseif (in_array($this->type, [OptionType::MULTIPLE_SELECTION, OptionType::MULTIPLE_SELECTION_IMAGE])) {
            foreach ($option->getValueList() as $value) {
                $this->valueList[] = new BasketRowOptionValue($value);
            }
        } elseif (in_array($this->type, [OptionType::ATTACHMENT])) {
            foreach ($option->getValues() as $value) {
                $this->values[] = $value;
            }
        }
    }

    private function getObjectProperties(array $data = []): array {
        $properties = get_object_vars($this);
        if (empty($this->valueList)) {
            unset($properties['valueList']);
        } else if (empty($this->values)) {
            unset($properties['values']);
        }
        if (isset($properties['valueList']) || isset($properties['values'])) {
            unset($properties['value']);
        }
        return $properties;
    }
}
