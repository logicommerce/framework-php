<?php

namespace FWK\ViewHelpers\Product\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use SDK\Dtos\Catalog\Product\Product;
use FWK\ViewHelpers\Product\ProductJsonData;

/**
 * This is the BuyForm class, a macro class for the productViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the product's buy form.
 *
 * @see BuyForm::getViewParameters()
 *
 * @package FWK\ViewHelpers\Product\Macro
 */
class BuyForm {

    public ?Product $product = null;

    public string $class = '';

    public string $style = '';

    public string $content = '';

    private array $dataProductObject = [];

    private array $dataWarehousesObject = [];

    public int $sectionId = 0;

    public int $shoppingListRowId = 0;

    /**
     * Constructor method for BuyForm class.
     * 
     * @see BuyForm
     * 
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        ViewHelper::mergeArguments($this, $arguments);
    }

    /**
     * This method returns all calculated arguments and new parameters for ProductViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        if (is_null($this->product)) {
            throw new CommerceException("The value of [product] argument: '" . $this->product . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }

        // Set property data html attribute json
        $dataProductObject = new ProductJsonData($this->product);
        $this->dataProductObject = $dataProductObject->output();

        // Set property data html attribute json
        $this->dataWarehousesObject = $this->getWarehousesObject();
        return $this->getProperties();
    }

    /**
     * Return all product stock warehouses
     *
     * @return array
     */
    private function getWarehousesObject(): array {
        $warehouses = [];
        $warehouseIds = [];

        foreach ($this->product->getCombinations() as $combination) {
            foreach ($combination->getStocks() as $stock) {
                // No duplicade objects control
                if (in_array($stock->getWarehouseId(), $warehouseIds) === false) {
                    $warehouseIds[] = $stock->getWarehouseId();
                    $warehouses[] = [
                        'warehouseId' => $stock->getWarehouseId(), // 15
                        'warehousesStructureId' => 'WH' . $stock->getWarehouseId(), // 'WH32'
                        'offsetDays' => $stock->getOffsetDays(), // 0
                        'discountPriority' => $stock->getPriority() // 1
                    ];
                }
            }
        }
        return $warehouses;
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        return [
            'product' => $this->product,
            'class' => $this->class,
            'style' => $this->style,
            'content' => $this->content,
            'dataProductObject' => $this->dataProductObject,
            'dataWarehousesObject' => $this->dataWarehousesObject,
            'sectionId' => $this->sectionId,
            'shoppingListRowId' => $this->shoppingListRowId
        ];
    }
}
