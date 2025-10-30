<?php

namespace FWK\Controllers\Resources\Internal;

use FWK\Core\Controllers\BaseJsonController;
use FWK\Core\FilterInput\FilterInput;
use SDK\Core\Resources\BatchRequests;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\Resources\LcFWK;
use SDK\Core\Dtos\Element;

use SDK\Core\Resources\Redis;
use SDK\Enums\RedisKey;

/**
 * This is the Php Commerce Clean Cache controller.
 * This class extends BaseJsonController (FWK\Core\Controllers\BaseJsonController), see this class.
 *
 * @see BaseJsonController
 *
 * @package FWK\Controllers\Resources\Internal
 */
class PhpCommerceCleanCacheController extends BaseJsonController {

    public const PHP_COMMERCE_TOKEN = 'php-commerce-token';

    /**
     * This method returns the origin of the params (see FilterInputHandler::PARAMS_FROM_GET, FilterInputHandler::PARAMS_FROM_QUERY_STRING or FilterInputHandler::PARAMS_FROM_POST,...).
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     *
     * @see FilterInputHandler
     */
    protected function getOriginParams() {
        return FilterInputHandler::PARAMS_FROM_HEADER;
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return [
            self::PHP_COMMERCE_TOKEN => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => true,
                FilterInput::CONFIGURATION_FILTER_KEY_FUNCTION_VALIDATOR => function ($value) {
                    return $value === LcFWK::getPhpCommerceToken();
                }
            ])
        ];
    }

    /**
     * This method initialize applied parameters, runs previously to run preSendControllerBaseBatchData
     *
     */
    protected function initializeAppliedParameters(): void {
    }

    /**
     * This method launches the adequate actions against the SDK (through the FWK services) and returns the response data. 
     *
     * @return Element
     */
    protected function getResponseData(): ?Element {
        // Check valid token
        $this->getRequestParam(self::PHP_COMMERCE_TOKEN, true);

        // Clean cache
        LcFWK::deleteRedisCacheObjecs();

        // No errors, return ok
        return new class() extends Element {
            public function jsonSerialize(): mixed {
                return [
                    'done' => 'ok'
                ];
            }
        };
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
}
