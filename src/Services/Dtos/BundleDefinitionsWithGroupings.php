<?php

namespace FWK\Services\Dtos;

use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Resources\Loader;
use FWK\Enums\Services;
use SDK\Core\Dtos\BundleGroupingCollection;
use SDK\Core\Dtos\Element;
use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Core\Resources\BatchRequests;
use SDK\Core\Services\BatchService;

/**
 * This is the BundleDefinitionsWithGroupings class.
 *
 * @see BundleDefinitionsWithGroupings::getItems()
 *
 * @see Element
 *
 * @package FWK\Services
 */
class BundleDefinitionsWithGroupings extends Element {
    use ElementTrait;

    private array $items = [];

    public function __construct(int $productId, array $BundleDefinitions) {
        if (!empty($BundleDefinitions)) {
            $groupings = [];
            $batchBundleDefinitionsGroupings = new BatchRequests();
            foreach ($BundleDefinitions as $key => $bundleDefinitionItem) {
                Loader::service(Services::PRODUCT)->addGetBundleDefinitionsGroupings($batchBundleDefinitionsGroupings, strval($bundleDefinitionItem->getId()), $productId, $bundleDefinitionItem->getId());
            }
            $groupings = BatchService::getInstance()->send($batchBundleDefinitionsGroupings);
            foreach ($BundleDefinitions as $key => $bundleDefinitionItem) {
                if (isset($groupings[$bundleDefinitionItem->getId()])) {
                    $grouping = $groupings[$bundleDefinitionItem->getId()];
                    if ($grouping instanceof BundleGroupingCollection) {
                        $bundleDefinitionItemArray = $bundleDefinitionItem->toArray();
                        $bundleDefinitionItemArray['groupings'] =  $grouping;
                        $this->items[] = new BundleDefinitionWithGroupings($bundleDefinitionItemArray);
                    } else {
                        $message = '';
                        if (!is_null($grouping->getError())) {
                            $message = "Response of bundle definition grouping:" . $bundleDefinitionItem->getId() . ", status:" . $grouping->getError()->getStatus() . " ,code:" . $grouping->getError()->getCode() . " ,message:" . $grouping->getError()->getMessage();
                        } else {
                            $message = "Response of bundle definition grouping:" . $bundleDefinitionItem->getId() . " ,must be an instance of BundleGroupingCollection";
                        }
                        throw new CommerceException($message, CommerceException::CONTROLLER_UNDEFINED_CRITICAL_DATA);
                    }
                }
            }
        }
    }

    public function getItems(): array {
        return $this->items;
    }
}
