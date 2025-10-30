<?php

namespace FWK\Core\Resources\Session\BasketGridProduct;

class OptionSummary implements \JsonSerializable {
    public string $name;
    public array $valueIds;

    public function __construct(string $name, array $valueIds) {
        $this->name = $name;
        $this->valueIds = $valueIds;
    }

    public function jsonSerialize(): array {
        return [
            'name' => $this->name,
            'valueIds' => $this->valueIds
        ];
    }
}
