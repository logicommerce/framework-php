<?php

namespace FWK\Core\FilterInput;

use FWK\Core\Controllers\BaseJsonController;
use FWK\Core\Resources\Utils;
use FWK\Enums\Parameters;

/**
 * This is the FilterInputHandler class.
 * The purpose of this class is to filter (validate and sanitize) the given values according to the given filter configuration.
 * <br>Use examples: It is commonly used to filter the parameters of a request,...
 *
 * @see FilterInputHandler::getAvailableDinamicParam()
 * @see FilterInputHandler::getFilterFilterInputs()
 *
 * @package FWK\Core\FilterInput
 *         
 * @link https://www.php.net/manual/es/filter.filters.sanitize.php
 */
class FilterInputHandler {

    public const PARAMS_FROM_GET = 1;

    public const PARAMS_FROM_QUERY_STRING = 2;

    public const PARAMS_FROM_POST = 3;

    public const PARAMS_FROM_POST_DATA_OBJECT = 4;

    public const PARAMS_FROM_HEADER = 5;

    public const DATE_PARAMS = [
        Parameters::FROM_DATE,
        Parameters::TO_DATE
    ];

    public const ARRAY_PARAMS = [
        Parameters::FILTER_CUSTOMTAG,
        Parameters::FILTER_OPTION,
        Parameters::FILTER_CUSTOMTAG_INTERVAL,
        Parameters::VALUES,
        Parameters::OPTIONS,
        Parameters::BRANDS_LIST,
        Parameters::CATEGORY_ID_LIST,
        Parameters::HASHES
    ];

    public const DYNAMIC_NAME_PARAMS = [
        Parameters::CUSTOM_TAGS,
        Parameters::FILTER_CUSTOMTAG,
        Parameters::FILTER_CUSTOMTAG_RANGE,
        Parameters::FILTER_CUSTOMTAG_INTERVAL,
        Parameters::FILTER_OPTION,
        Parameters::OPTION_ID,
        Parameters::RETURN_QUANTITY,
        Parameters::RETURN_CHECK
    ];

    public const DYNAMIC_NAME_PARAM_DELIMITER = '_';

    private static function getArrayParams($orginQueryParams): array {
        $queryParams = [];
        switch ($orginQueryParams) {
            case self::PARAMS_FROM_GET:
                foreach ($_GET as $param => $value) {
                    $queryParams[$param][] = $value;
                }
                break;
            case self::PARAMS_FROM_QUERY_STRING:
                $queryParams = Utils::getQueryStringParameters();
                break;
            case self::PARAMS_FROM_POST:
                foreach ($_POST as $param => $value) {
                    $queryParams[$param][] = $value;
                }
                break;
            case self::PARAMS_FROM_POST_DATA_OBJECT:
                if (isset($_POST[BaseJsonController::DATA]) && !is_null($_POST[BaseJsonController::DATA])) {
                    $postData = json_decode($_POST[BaseJsonController::DATA], true);
                    if (is_array($postData)) {
                        foreach (json_decode($_POST[BaseJsonController::DATA], true) as $param => $value) {
                            if (in_array($param, self::ARRAY_PARAMS)) {
                                $queryParams[$param] = $value;
                            } else {
                                $queryParams[$param][] = $value;
                            }
                        }
                    }
                }
                break;
            case self::PARAMS_FROM_HEADER:
                foreach (getallheaders() as $param => $value) {
                    $queryParams[strtolower($param)][] = $value;
                }
                break;
            default:
                $queryParams = $orginQueryParams;
                break;
        }
        return $queryParams;
    }

    /**
     * This method returns null if the given param is not a dynamic param (see filterInputHandler::DYNAMIC_NAME_PARAMS),
     * otherwise returns a two positions array where position 0 contains the name of the dynamic param and the position 1 contains its value.
     *
     * @param string $param
     *
     * @return array|NULL
     */
    public static function getAvailableDinamicParam(string $param): ?array {
        $defaultRP = new FilterInput([
            FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false
        ]);
        $auxParam = explode(self::DYNAMIC_NAME_PARAM_DELIMITER, $param, 2);
        if (count($auxParam) > 1 && in_array($auxParam[0], self::DYNAMIC_NAME_PARAMS) && $defaultRP->getFilterValue($auxParam[1]) !== null) {
            return $auxParam;
        }
        return null;
    }

    /**
     * This method gets the params of the specified origin and applies to them the specified filters defined in the given $availableParams.
     * It returns the filtered params with its values.
     *
     * @param mixed $orginQueryParams
     *            Could be string as FilterParams::PARAMS_FROM_GET, FilterParams::PARAMS_FROM_QUERY_STRING or FilterParams::PARAMS_FROM_POST, or an array with the varaibles and values.
     * @param array $availableParams
     *            each node must be the param name, and the filter to apply to the param.
     *            
     * @return array
     */
    public static function getFilterFilterInputs($orginQueryParams, $availableParams): array {
        $queryParams = self::getArrayParams($orginQueryParams);
        $filterRequestaParams = [];
        foreach ($queryParams as $param => $values) {
            $identityKey = '';

            // Chech if the parameter is an available param
            if (isset($availableParams[$param]) && !in_array($param, self::DYNAMIC_NAME_PARAMS)) {
                $auxAvailableParam = $availableParams[$param];
            } else {
                $dynamicParam = self::getAvailableDinamicParam($param);
                if ($dynamicParam !== null && isset($availableParams[$dynamicParam[0]])) {
                    $auxAvailableParam = $availableParams[$dynamicParam[0]];
                    $param = $dynamicParam[0];
                    $identityKey = $dynamicParam[1];
                } else {
                    continue;
                }
            }
            if (!$auxAvailableParam instanceof FilterInput) {
                continue;
            }

            // Chech if the value is an available value
            if (is_array($values)) {
                foreach ($values as $value) {
                    if (strlen($identityKey)) {
                        if (in_array($param, self::ARRAY_PARAMS)) {
                            $filterRequestaParams[$param][$identityKey][] = $auxAvailableParam->getFilterValue($value);
                        } else {
                            $filterRequestaParams[$param][$identityKey] = $auxAvailableParam->getFilterValue($value);
                        }
                    } else {
                        if (in_array($param, self::ARRAY_PARAMS)) {
                            $filterRequestaParams[$param][] = $auxAvailableParam->getFilterValue($value);
                        } else {
                            $filterRequestaParams[$param] = $auxAvailableParam->getFilterValue($value);
                        }
                    }
                }
            } else {
                $filterRequestaParams[$param] = $auxAvailableParam->getFilterValue($values);
            }
        }
        return $filterRequestaParams;
    }
}
