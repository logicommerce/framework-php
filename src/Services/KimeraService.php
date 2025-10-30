<?php

namespace FWK\Services;

use FWK\Core\Resources\Registries\RegistryService;
use SDK\Services\KimeraService as KimeraServiceSDK;
use FWK\Services\Traits\ServiceTrait;
use FWK\ViewHelpers\Util\Macro\Trackers;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Kimera\KimeraDataRequest;

class KimeraService extends KimeraServiceSDK {
    use ServiceTrait;

    private const REGISTRY_KEY = RegistryService::KIMERA_SERVICE;

    private const ADD_FILTER_INTERVAL_PARAMETERS = [];

    private const ADD_FILTER_ID_VALUE_PARAMETERS = [];

    /**
     * This method returns the KimeraDataRequest.
     * 
     * @param string $routeType
     * 
     * @return ElementCollection|NULL
     */
    public function getKimeraData(): ?KimeraDataRequest {
        return $this->getData();
    }

    /**
     * Add the request to get the kimera data.
     *
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *            the name that will identify the request on the batch return.
     *
     * @return void
     */
    public function addGetKimeraData(BatchRequests $batchRequests, string $batchName): void {
        $this->addGetData($batchRequests, $batchName);
    }
}