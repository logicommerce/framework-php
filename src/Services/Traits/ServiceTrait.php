<?php

namespace FWK\Services\Traits;

use FWK\Core\Resources\Registries\RegistryService;
use SDK\Core\Services\Service;
use SDK\Core\Services\Parameters\Groups\ParametersGroup;
use FWK\Core\FilterInput\FilterInput;
use FWK\Core\Resources\Session;
use SDK\Core\Dtos\ElementCollection;
use SDK\Core\Resources\BatchRequests;
use SDK\Core\Services\BatchService;

/**
 * This is the service trait.
 * This trait has been created to share common methods between FWK services.
 *
 * @see ServiceTrait::getInstance()
 * @see ServiceTrait::generateParametersGroupFromArray()
 * @see ServiceTrait::validateParameterGroup()
 *
 * @package FWK\Services\Traits
 */
trait ServiceTrait {

    /**
     * This method returns the service instance.
     * 
     * @internal
     * Its like a singleton pattern, the service instance returned is the one registered on the RegistryService with self::REGISTRY_KEY key. 
     * If the instance does not exist then this method creates and sets it in the RegistryService.
     * 
     * @return Service
     */
    public static function getInstance(): Service {
        if (!RegistryService::exist(self::REGISTRY_KEY)) {
            RegistryService::set(self::REGISTRY_KEY, new static());
        }

        return RegistryService::get(self::REGISTRY_KEY);
    }

    /**
     * This method sets a parametersGroup object with the filters indicated in params (array key=>value). 
     * 
     * @param ParametersGroup $parametersGroup.
     *            Parameters group object to be set with the given params. This variable is passed by reference.
     * @param array $params.
     *            Filters to set to the parametersGroup object. 
     *            It is composed by an array of key=>value. $this. Each key is a filter excep the keys included in FilterParams::ARRAY_PARAMS, these variables are an array of filtrable values.
     *            
     * @return array with the applied filters
     */
    public function generateParametersGroupFromArray(ParametersGroup &$parametersGroup, array $params): array {
        $appliedFilter = [];
        foreach ($params as $paramKey => $paramValue) {
            $setMethodName = 'set' . ucfirst($paramKey);
            if (method_exists($parametersGroup, $setMethodName) && $paramValue !== null) {
                $param = (new \ReflectionMethod($parametersGroup, $setMethodName))->getParameters()[0];
                $auxParamValue = $paramValue;
                if (is_string($auxParamValue)) {
                    $auxParamValue = $parametersGroup->getFormattedDataOutputWithStripTags($paramKey) ? strip_tags($auxParamValue) : $auxParamValue;
                }
                if (preg_match('/^.*List/', $paramKey) && is_Array($paramValue) && $param->getType()->getName() === 'string') {
                    $auxParamValue = implode(',', $paramValue);
                }
                $parametersGroup->$setMethodName($auxParamValue);
                $appliedFilter[$paramKey] = $auxParamValue;
            } else {
                $setMethodName = 'add' . ucfirst($paramKey);
                if (method_exists($parametersGroup, $setMethodName)) {
                    foreach ($paramValue as $identityKey => $addValues) {
                        foreach ($addValues as $addValue) {
                            if ($addValue !== null) {
                                if (is_string($addValue)) {
                                    $addValue = $parametersGroup->getFormattedDataOutputWithStripTags($identityKey) ? strip_tags($addValue) : $addValue;
                                }
                                if (in_array($paramKey, self::ADD_FILTER_ID_VALUE_PARAMETERS)) {
                                    if (in_array($paramKey, self::ADD_FILTER_INTERVAL_PARAMETERS)) {
                                        $range = explode(FilterInput::REGEX_VALIDATE_RANGE_SEPARATOR, $addValue);
                                        if (count($range) === 2) {
                                            if ($range[0] <= $range[1]) {
                                                $parametersGroup->$setMethodName($identityKey, intval($range[0]), intval($range[1]));
                                            } else {
                                                $parametersGroup->$setMethodName($identityKey, intval($range[1]), intval($range[0]));
                                            }
                                            $appliedFilter[$paramKey][$identityKey][] = $range;
                                        }
                                    } else {
                                        $parametersGroup->$setMethodName($identityKey, $addValue);
                                        $appliedFilter[$paramKey][$identityKey][] = $addValue;
                                    }
                                } elseif (in_array($paramKey, self::ADD_FILTER_INTERVAL_PARAMETERS)) {
                                    $range = explode(FilterInput::REGEX_VALIDATE_RANGE_SEPARATOR, $addValue);
                                    if (count($range) === 2) {
                                        if ($range[0] <= $range[1]) {
                                            $parametersGroup->$setMethodName($identityKey, intval($range[0]), intval($range[1]));
                                        } else {
                                            $parametersGroup->$setMethodName($identityKey, intval($range[1]), intval($range[0]));
                                        }
                                        $appliedFilter[$paramKey][$identityKey][] = $addValue;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $appliedFilter;
    }

    /**
     * This method validates the given parametersGroup. Returns true if valid, false otherwise. 
     * 
     * @param ParametersGroup $parametersGroup
     * 
     * @return bool
     */
    public function validateParameterGroup(ParametersGroup $parametersGroup): bool {
        try {
            $parametersGroup->toArray();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * This method returns and ElementCollection from the given data and class
     * 
     * @param array $data
     * @param string $class
     * 
     * @return NULL|ElementCollection
     */
    public function buildElementCollection(array $data, string $class): ?ElementCollection {
        return $this->getResponse($data, $class);
    }

    /**
     * Returns all available items filtered with the given parameters
     *
     * @param string $elementClass
     * @param string $resource
     * @param ParametersGroup $params
     *            object with the needed filters to send to the API resource
     *
     * @return ElementCollection|NULL
     */
    protected function getAllElementCollectionItems(string $elementClass, string $resource, ParametersGroup $params, int $id = 0): ?ElementCollection {
        $params->setPerPage(100);
        $getFunction = "get" . $resource;
        $addGetfunction = "addGet" . $resource;
        if ($id > 0) {
            $baseElementCollection = $this->$getFunction($id, $params);
        } else {
            $baseElementCollection = $this->$getFunction($params);
        }
        if (is_null($baseElementCollection->getError()) && $baseElementCollection->getPagination()->getTotalPages() > 1) {
            $batchRequest = new BatchRequests();
            for ($i = 2; $i <= $baseElementCollection->getPagination()->getTotalPages(); $i++) {
                $params->setPage($i);
                if ($id > 0) {
                    $this->$addGetfunction($batchRequest, $i, $id, $params);
                } else {
                    $this->$addGetfunction($batchRequest, $i, $params);
                }
            }
            $allElementsArray = $baseElementCollection->toArray();
            $batchResult = BatchService::getInstance()->send($batchRequest);
            $items = $allElementsArray['items'];
            for ($i = 2; $i <= $baseElementCollection->getPagination()->getTotalPages(); $i++) {
                $items = [...$items, ...$batchResult[$i]->toArray()['items']];
            }
            $allElementsArray['pagination']['perPage'] = count($items);
            $allElementsArray['pagination']['totalItems'] = count($items);
            $allElementsArray['pagination']['totalPages'] = 1;
            $allElementsArray['items'] = $items;
            return $this->buildElementCollection($allElementsArray, $elementClass);
        } else {
            return $baseElementCollection;
        }
    }

    protected function getNavigationHash(): ?string {
        return Session::getInstance()->getNavigationHash();
    }
}
