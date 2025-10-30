<?php declare(strict_types=1);

namespace FWK\Core\Controllers\Traits;

use SDK\Core\Dtos\Element;
use FWK\Core\Resources\Utils;
use FWK\Enums\Parameters;

/**
 * This is the set delivery trait.
 *
 * @see DeleteVoucherTrait::getDeleteVoucherResponseData()
 *
 * @package FWK\Core\Controllers\Traits
 */
trait DeleteVoucherTrait {

    /**
     * Returns the response data for the set deliveries actions.
     * 
     * @return Element|NULL
     */
    protected function getDeleteVoucherResponseData(string $code = ''): ?Element {
        if (strlen($code) === 0) {
            $code = $this->getRequestParam(Parameters::CODE, true);
        }
        $response = $this->basketService->deleteVoucherCode($code);
        $this->responseMessageError = Utils::getErrorLabelValue($response);
        return $response;
    }
}
