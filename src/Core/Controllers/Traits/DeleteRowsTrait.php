<?php declare(strict_types=1);

namespace FWK\Core\Controllers\Traits;

use SDK\Core\Dtos\Element;
use FWK\Enums\Parameters;
use SDK\Services\Parameters\Groups\Basket\DeleteRowsParametersGroup;

/**
 * This is the delete row trait.
 *
 * @see DeleteRowsTrait::getDeleteRowsResponseData()
 *
 * @package FWK\Core\Controllers\Traits
 */
trait DeleteRowsTrait {

    /**
     * Returns the response data for the delete basket rows actions.
     * 
     * @return Element|NULL
     */
    protected function getDeleteRowsResponseData(array $hashes = []): ?Element {
        if (count($hashes) === 0) {
            $hashes = $this->getRequestParam(Parameters::HASHES, true);
        }
        $deleteRowsParametersGroup = new DeleteRowsParametersGroup();
        $deleteRowsParametersGroup->setHashes($hashes);
        return $this->basketService->deleteRows($deleteRowsParametersGroup);
    }
}
