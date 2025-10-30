<?php declare(strict_types=1);

namespace FWK\Core\Controllers\Traits;

use SDK\Core\Dtos\Element;
use FWK\Enums\Parameters;

/**
 * This is the delete row trait.
 *
 * @see DeleteRowTrait::getDeleteRowResponseData()
 *
 * @package FWK\Core\Controllers\Traits
 */
trait DeleteRowTrait {

    /**
     * Returns the response data for the delete basket row actions.
     * 
     * @return Element|NULL
     */
    protected function getDeleteRowResponseData(string $hash = ''): ?Element {
        if (strlen($hash) === 0) {
            $hash = $this->getRequestParam(Parameters::HASH, true);
        }
        return $this->basketService->deleteRow($hash);
    }
}
