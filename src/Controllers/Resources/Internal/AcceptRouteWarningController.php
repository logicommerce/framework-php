<?php

namespace FWK\Controllers\Resources\Internal;

use FWK\Core\Controllers\BaseJsonController;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\Resources\Session;
use FWK\Enums\Parameters;
use SDK\Core\Dtos\Element;
use SDK\Core\Resources\BatchRequests;

/**
 * This is the AcceptRouteWarning controller.
 * This class extends BaseJsonController (FWK\Core\Controllers\BaseJsonController), see this class.
 *
 * @see BaseJsonController
 *
 * @package FWK\Controllers\Resources\Internal
 */
class AcceptRouteWarningController extends BaseJsonController {

    /**
     * This method initialize applied parameters, runs previously to run preSendControllerBaseBatchData
     *
     */
    protected function initializeAppliedParameters(): void {
    }

    protected function getFilterParams(): array {
        return FilterInputFactory::getCountryCodeParameter();
    }

    protected function getOriginParams() {
        return FilterInputHandler::PARAMS_FROM_POST;
    }

    /**
     * Persists the navigation country the user has just confirmed by closing
     * the route-warning modal. If the caller posts a `countryCode` parameter
     * (e.g. when the user explicitly picks a country in a form) we use that;
     * otherwise we fall back to the current navigation country from session
     * general settings.
     *
     * @return Element
     */
    protected function getResponseData(): ?Element {
        $session = Session::getInstance();
        $country = $this->getRequestParam(Parameters::COUNTRY_CODE);
        if (empty($country)) {
            $country = $session->getGeneralSettings()->getCountry();
        }
        $session->setForcedCountry(strtoupper((string)$country));
        return new class($session) extends Element {
            private string $forcedCountry = '';

            public function __construct($session) {
                $this->forcedCountry = $session->getForcedCountry();
            }

            public function jsonSerialize(): mixed {
                return ['forcedCountry' => $this->forcedCountry];
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
