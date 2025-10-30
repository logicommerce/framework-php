<?php

namespace FWK\Core\Resources\Session\BasketGridProduct;

class OptionValueSummary implements \JsonSerializable {
    public int $id;
    public string $value;
    public string $image;
    public int $optionId;

    public function __construct(int $id, string $value, string $image, int $optionId) {
        $this->id = $id;
        $this->value = $value;
        $this->image = $image;
        $this->optionId = $optionId;
    }

    public function jsonSerialize(): array {
        return [
            'id' => $this->id,
            'value' => $this->value,
            'image' => $this->image,
            'optionId' => $this->optionId
        ];
    }
}
