<?php

namespace FWK\Controllers\Checkout;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\Exceptions\CommerceException;
use FWK\Enums\Services;
use FWK\Core\Resources\Loader;
use FWK\Services\OrderService;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\PaymentValidationResponse;
use SDK\Enums\PaymentValidateStatus;
use SDK\Enums\PaymentValidateType;

/**
 * This is the checkout async order controller.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @see BaseHtmlController
 *
 * @package FWK\Controllers\Checkout
 */
class AsyncOrderController extends BaseHtmlController {

    public const POST_PARAMETERS = 'postParameters';
    public const GET_PARAMETERS = 'getParameters';

    /**
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     */
    final protected function setControllerBaseData(): void {
        $this->setDataValue(self::POST_PARAMETERS, $_POST);
        $get = $_GET;
        unset($get[URL_ROUTE]);
        $this->setDataValue(self::GET_PARAMETERS, $get);
        $this->setDataValue(self::CONTROLLER_ITEM, $this->getValidatePaymentResponse());
    }

    private function getValidatePaymentResponse(): ?PaymentValidationResponse {
        /** @var OrderService $orderService */
        $orderService = Loader::service(Services::ORDER);
        $paymentValidationResponse = $orderService->validatePayment();
        $validatePaymentStatus = $paymentValidationResponse->getStatus();
        $validatePaymentType = $paymentValidationResponse->getType();
        if ($validatePaymentType == PaymentValidateType::WEBHOOK_MESSAGE) {
            header('Content-Type: application/json');
        }
        if ($validatePaymentStatus == PaymentValidateStatus::ACCEPTED) {
            $count = 0;
            do {
                sleep(1);
                $paymentValidationResponse = $orderService->validatePayment();
                $validatePaymentStatus = $paymentValidationResponse->getStatus();
                $count++;
                if ($count >= 10) {
                    throw new CommerceException('Too many validate payment sended, try ' . $count . ' times', CommerceException::ASYNC_ORDER_VALIDATE_PAYMENT);
                }
            } while ($validatePaymentStatus == PaymentValidateStatus::ACCEPTED);
        }
        return $paymentValidationResponse;
    }

    /**
     * This method is the one in charge of defining all the data batch requests that
     * are needed for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     * @return void
     */
    protected function setBatchData(BatchRequests $request): void {
    }

    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
    }

    /**
     * This method runs after the batch requests (defined in the setBatchData methods) are resolved,
     * so here you can work with the response of the batch requests and calculate and set more needed data.
     *
     * @param array $additionalData
     *              Set additiona data to the controller data
     * 
     * @return void
     */
    protected function setData(array $additionalData = []): void {
    }
}
