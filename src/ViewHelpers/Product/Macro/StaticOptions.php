<?php

namespace FWK\ViewHelpers\Product\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use SDK\Dtos\Catalog\Product\Product;
use SDK\Dtos\User\OptionReference;

/**
 * This is the StaticOptions class, a macro class for the productViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the product's StaticOptions.
 *
 * @see StaticOptions::getViewParameters()
 *
 * @package FWK\ViewHelpers\Product\Macro
 */
class StaticOptions {

    public ?array $optionReferences = [];

    public ?Product $product = null;

    public bool $showImageValues = true;

    /**
     * Constructor method for StaticOptions class.
     * 
     * @see ElementCollection
     * 
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        ViewHelper::mergeArguments($this, $arguments);
        if (is_null($this->optionReferences)) {
            $this->optionReferences = [];
        }
    }

    /**
     * This method returns all calculated arguments and new parameters for ProductViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        if (is_null($this->product) or !$this->product instanceof Product) {
            throw new CommerceException("The value of product argument is required and must be an isntance of " . Product::class . " in " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }
        foreach ($this->optionReferences as $optionReference) {
            if (!($optionReference instanceof OptionReference)) {
                throw new CommerceException('Each element of optionReferences must be a instance of ' . OptionReference::class . '. ' . ' Instance of ' . get_class($optionReference) . ' sended.', CommerceException::VIEW_HELPER_INVALID_ARGUMENT);
            }
        }

        return $this->getProperties();
    }

    private function getOptionValues() {
        $options = [];
        $productOptions = [];
        foreach ($this->product->getOptions() as $option) {
            $productOptions[$option->getId()] = $option;
        }
        foreach ($this->optionReferences as $optionReference) {
            $productOption = [];
            $productOption['reference'] = $optionReference;
            $productOption['option'] = $productOptions[$optionReference->getId()];
            $options[] = $productOption;
        }
        return $options;
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        return [
            'productOptions' => $this->getOptionValues(),
            'optionReferences' => $this->optionReferences,
            'product' => $this->product,
            'showImageValues' => $this->showImageValues,
        ];
    }
}
