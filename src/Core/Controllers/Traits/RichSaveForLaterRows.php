<?php

declare(strict_types=1);

namespace FWK\Core\Controllers\Traits;

use FWK\Dtos\User\SaveForLaterListRow;
use SDK\Core\Dtos\SaveForLaterListRowsCollection;
use FWK\Dtos\Catalog\BundleGrouping;
use SDK\Dtos\Catalog\Product\Product;
use SDK\Enums\ListRowReferenceType;

/**
 * This is the set delivery trait.
 *
 * @see RichSaveForLaterRows::getRichSaveForLaterRows()
 *
 * @package FWK\Core\Controllers\Traits
 */
trait RichSaveForLaterRows {

    private array $shipments = [];

    /**
     * Returns the response data for the set deliveries actions.
     * 
     * @return SaveForLaterListRowsCollection|NULL
     */
    protected function getRichSaveForLaterRows(?SaveForLaterListRowsCollection $rows): ?SaveForLaterListRowsCollection {
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
                $richRow = new SaveForLaterListRow($row);
                if (!is_null($richRow->getReference())) {
                    if ($richRow->getReference()->getType() === ListRowReferenceType::PRODUCT) {
                        $rowProduct = $products[$richRow->getReference()->getId()]->toArray();
                        $rowProduct['combinationData'] = $richRow->getReference()->getCombinationData()->toArray();
                        $richRow->setItem(new Product($rowProduct));
                    } else {
                        $rowBundle = $bundles[$richRow->getReference()->getId()]->toArray();
                        if (!is_null($richRow->getReference()->getCombinationData())) {
                            $rowBundle['combinationData'] = $richRow->getReference()->getCombinationData()->toArray();
                        }
                        $bundleProducts = [];
                        foreach ($bundles[$richRow->getReference()->getId()]->getItems() as $bundleItem) {
                            $bundleProducts[$bundleItem->getProductId()] = $products[$bundleItem->getProductId()];
                        }
                        $rowBundle['products'] = $bundleProducts;
                        $richRow->setItem(new BundleGrouping($rowBundle));
                    }
                }
                $richRows[] = $richRow;
            }
            $rowsArray['items'] = $richRows;
            return new SaveForLaterListRowsCollection($rowsArray);
        } else {
            return null;
        }
    }
}
