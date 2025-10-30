<?php

namespace FWK\Core\ViewHelpers\Macros\Product\Macro;

/**
 * This is the BundleGrouping class, a macro class for the productViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the product's buttonClearProductsFilter.
 *
 * @package FWK\ViewHelpers\Product\Macro
 */
class BundleGrouping {

    public bool $showBundleDefinition = true;

    public bool $showBundleDefinitionName = true;

    public bool $showBundleDefinitionDescription = true;

    public bool $showUniqueUnit = false;

    public array $buyFormOptionsArgs = [];

    public array $buyBundleForm = [];

    public array $shoppingListButtonArgs = [];

    public array $buttonRecommendArgs = [];

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        return [
            'showBundleDefinition' => $this->showBundleDefinition,
            'showBundleDefinitionName' => $this->showBundleDefinitionName,
            'showBundleDefinitionDescription' => $this->showBundleDefinitionDescription,
            'showUniqueUnit' => $this->showUniqueUnit,
            'buyFormOptionsArgs' => $this->buyFormOptionsArgs,
            'buyBundleForm' => $this->buyBundleForm,
            'shoppingListButtonArgs' => $this->shoppingListButtonArgs,
            'buttonRecommendArgs' => $this->buttonRecommendArgs
        ];
    }
}
