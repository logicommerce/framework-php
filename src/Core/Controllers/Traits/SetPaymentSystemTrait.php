<?php

declare(strict_types=1);

namespace FWK\Core\Controllers\Traits;

use SDK\Core\Dtos\Element;
use SDK\Core\Dtos\Factories\PaymentSystemAdditionalDataFactory;
use SDK\Services\Parameters\Groups\Basket\EditPaymentSystemParametersGroup;
use FWK\Enums\Parameters;

/**
 * This is the set payment system trait.
 *
 * @see SetPaymentSystemTrait::getPaymentSystemResponseData()
 *
 * @package FWK\Core\Controllers\Traits
 */
trait SetPaymentSystemTrait {

    /**
     * Returns the response data for the set payment systems actions.
     * 
     * @return Element|NULL
     */
    protected function getPaymentSystemResponseData(array $payment = []): ?Element {
        if (count($payment) === 0) {
            $payment = $this->getRequestParams();
        }

        if (isset($payment[Parameters::ADDITIONAL_DATA])) {
            $payment[Parameters::ADDITIONAL_DATA] = PaymentSystemAdditionalDataFactory::getAdditionaData(json_decode($payment[Parameters::ADDITIONAL_DATA], true));
            if (!is_null($payment[Parameters::ADDITIONAL_DATA])) {
                $payment[Parameters::ADDITIONAL_DATA] = $payment[Parameters::ADDITIONAL_DATA]->toArray();
            } else {
                unset($payment[Parameters::ADDITIONAL_DATA]);
            }
        }
        $editPaymentParametersGroup = new EditPaymentSystemParametersGroup();
        $this->appliedParameters['paymentSystem'] = $this->basketService->generateParametersGroupFromArray($editPaymentParametersGroup, $payment);

        return $this->basketService->setPaymentSystem($editPaymentParametersGroup);
    }
}
