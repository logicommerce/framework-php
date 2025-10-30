<?php

declare(strict_types=1);

namespace FWK\Core\Controllers\Traits;

use FWK\Dtos\User\ShoppingListRow;
use SDK\Core\Dtos\ShoppingListRowsCollection;
use FWK\Dtos\Catalog\BundleGrouping;
use SDK\Dtos\Catalog\Product\Product;
use SDK\Enums\ListRowReferenceType;

/**
 * This is the set delivery trait.
 *
 * @see RichShoppingListRows::getRichShoppingListRows()
 *
 * @package FWK\Core\Controllers\Traits
 */
trait RichShoppingListRows {

    private array $shipments = [];

    /**
     * Returns the response data for the set deliveries actions.
     * 
     * @return ShoppingListRowsCollection|NULL
     */
    protected function getRichShoppingListRows(?ShoppingListRowsCollection $rows): ?ShoppingListRowsCollection {
        if (!is_null($rows) && count($rows->getItems()) > 0) {
            $products = [];
            foreach ($rows->getProducts() as $product) {
                $products[$product->getId()] = $product;
            }
            $bundles = [];
            foreach ($rows->getBundles() as $bundle) {
                $bundles[$bundle->getId()] = $bundle;
            }
            $rowsArray = $rows->toArray();
            $richRows = [];
            foreach ($rowsArray['items'] as $row) {
                $richRow = new ShoppingListRow($row);
                $addRichRow = true;
                if (!is_null($richRow->getReference())) {
                    if ($richRow->getReference()->getType() === ListRowReferenceType::PRODUCT) {
                        $productId = $richRow->getReference()->getId();
                        if (isset($products[$productId])) {
                            $rowProduct = $products[$productId]->toArray();
                            $rowProduct['combinationData'] = $richRow->getReference()->getCombinationData()->toArray();
                            $richRow->setItem(new Product($rowProduct));
                        } else {
                            $addRichRow = false;
                        }
                    } else {
                        if (isset($bundles[$richRow->getReference()->getId()])) {
                            $rowBundle = $bundles[$richRow->getReference()->getId()]->toArray();
                            if (!is_null($richRow->getReference()->getCombinationData())) {
                                $rowBundle['combinationData'] = $richRow->getReference()->getCombinationData()->toArray();
                            }
                            $bundleProducts = [];
                            foreach ($bundles[$richRow->getReference()->getId()]->getItems() as $bundleItem) {
                                $productId = $bundleItem->getProductId();
                                if ($addRichRow && isset($products[$productId])) {
                                    $bundleProducts[$productId] = $products[$productId];
                                } else {
                                    $addRichRow = false;
                                }
                            }
                            $rowBundle['products'] = $bundleProducts;
                            $richRow->setItem(new BundleGrouping($rowBundle));
                        } else {
                            $addRichRow = false;
                        }
                    }
                }
                if ($addRichRow) {
                    $richRows[] = $richRow;
                }
            }
            $rowsArray['items'] = $richRows;
            return new ShoppingListRowsCollection($rowsArray);
        } else {
            return null;
        }
    }
}
