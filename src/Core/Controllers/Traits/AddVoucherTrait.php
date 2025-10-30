<?php declare(strict_types=1);

namespace FWK\Core\Controllers\Traits;

use SDK\Core\Dtos\Element;
use FWK\Core\Resources\Utils;
use FWK\Enums\Parameters;

/**
 * This is the set voucher trait.
 *
 * @see AddVoucherTrait::getAddVoucherResponseData()
 *
 * @package FWK\Core\Controllers\Traits
 */
trait AddVoucherTrait {

    /**
     * Returns the response data for the set vouchers actions.
     * 
     * @return Element|NULL
     */
    protected function getAddVoucherResponseData(string $code = ''): ?Element {
        if (strlen($code) === 0) {
            $code = $this->getRequestParam(Parameters::CODE, true);
        }
        $response = $this->basketService->addVoucherCode($code);
        $this->responseMessageError = Utils::getErrorLabelValue($response);
        return $response;
    }
}
