<?php

declare(strict_types=1);

namespace FWK\Core\Controllers\Traits;

use FWK\Core\Resources\Loader;
use SDK\Core\Dtos\Element;
use FWK\Enums\Parameters;
use FWK\Enums\Services;
use SDK\Services\Parameters\Groups\User\AddSaveForLaterListRowParametersGroup;
use SDK\Services\Parameters\Groups\User\AddSaveForLaterListRowsParametersGroup;

/**
 * This is the delete row trait.
 *
 * @see SaveforLaterRowTrait::getSaveforLaterRowResponseData()
 *
 * @package FWK\Core\Controllers\Traits
 */
trait SaveforLaterRowTrait {

    /**
     * Returns the response data for the save for later basket row actions.
     * 
     * @return Element|NULL
     */
    protected function getSaveforLaterRowResponseData(string $hash = ''): ?Element {
        if (strlen($hash) === 0) {
            $hash = $this->getRequestParam(Parameters::HASH, true);
        }
        $addSaveForLaterListRowsParametersGroup = new AddSaveForLaterListRowsParametersGroup();
        $addSaveForLaterListRowParametersGroup = new AddSaveForLaterListRowParametersGroup();
        $addSaveForLaterListRowParametersGroup->setBasketRowHash($hash);
        $addSaveForLaterListRowsParametersGroup->addItem($addSaveForLaterListRowParametersGroup);
        return Loader::service(Services::USER)->addSaveForLaterListRows($addSaveForLaterListRowsParametersGroup);
    }
}
