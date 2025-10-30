<?php

namespace FWK\Services\Dtos;

use SDK\Core\Dtos\BundleGroupingCollection;
use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Dtos\Catalog\BundleDefinition;

/**
 * This is the BundleDefinitionWithGroupings class.
 *
 * @see BundleDefinitionWithGroupings::getGroupings()
 *
 * @see BundleDefinitionWithGroupings
 *
 * @package FWK\Services
 */
class BundleDefinitionWithGroupings extends BundleDefinition {
    use ElementTrait;

    private ?BundleGroupingCollection $groupings = null;

    private function setGroupings(?BundleGroupingCollection $groupings)
    {
        $this->groupings = $groupings;
    }

    public function getGroupings(): ?BundleGroupingCollection
    {
        return $this->groupings;
    }

}
