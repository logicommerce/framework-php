<?php

namespace FWK\Core\Theme;

use SDK\Core\Resources\BatchRequests;
use FWK\Enums\RouteType;
use FWK\Enums\TcDataItems;

/**
 * This is the TcInterface.
 * This interface defines the methods to be defined by those
 * 'theme configuration classes' that implement this interface.
 *
 * @see TcInterface::routeTypeBatchRequestsFilter()
 * @see TcInterface::addBatchRequests()
 * @see TcInterface::getCalculatedData()
 * @see TcInterface::getConfigurationData()
 * @see TcInterface::runForbiddenResponse()
 *
 * @package FWK\Core\Theme
 */
interface TcInterface {

    public const FORBIDDEN_RESPONSE_ACTIONS = [
        RouteType::USER_ADDRESS_BOOK_ADD => [
            TcDataItems::FORBIDDEN_ROUTE_TYPE => RouteType::USER,
            TcDataItems::FORBIDDEN_STATUS => 301
        ],
        RouteType::USER_ADDRESS_BOOK_EDIT => [
            TcDataItems::FORBIDDEN_ROUTE_TYPE => RouteType::USER,
            TcDataItems::FORBIDDEN_STATUS => 301
        ],
        RouteType::CHANGE_PASSWORD_ANONYMOUS => [
            TcDataItems::FORBIDDEN_ROUTE_TYPE => RouteType::USER,
            TcDataItems::FORBIDDEN_STATUS => 301
        ],
        RouteType::USER_ADDRESS_BOOK => [
            TcDataItems::FORBIDDEN_ROUTE_TYPE => RouteType::USER,
            TcDataItems::FORBIDDEN_STATUS => 301
        ],
        RouteType::USER_CHANGE_PASSWORD => [
            TcDataItems::FORBIDDEN_ROUTE_TYPE => RouteType::USER,
            TcDataItems::FORBIDDEN_STATUS => 301
        ],
        RouteType::USER_COMPLETE_ACCOUNT => [
            TcDataItems::FORBIDDEN_ROUTE_TYPE => RouteType::USER,
            TcDataItems::FORBIDDEN_STATUS => 301
        ],
        RouteType::USER_DELETE_ACCOUNT => [
            TcDataItems::FORBIDDEN_ROUTE_TYPE => RouteType::USER,
            TcDataItems::FORBIDDEN_STATUS => 301
        ],
        RouteType::USER_VOUCHER_CODES => [
            TcDataItems::FORBIDDEN_ROUTE_TYPE => RouteType::USER,
            TcDataItems::FORBIDDEN_STATUS => 301
        ],
        RouteType::USER_ORDER => [
            TcDataItems::FORBIDDEN_ROUTE_TYPE => RouteType::USER,
            TcDataItems::FORBIDDEN_STATUS => 301
        ],
        RouteType::USER_ORDERS => [
            TcDataItems::FORBIDDEN_ROUTE_TYPE => RouteType::USER,
            TcDataItems::FORBIDDEN_STATUS => 301
        ],
        RouteType::USER_PAYMENT_CARDS => [
            TcDataItems::FORBIDDEN_ROUTE_TYPE => RouteType::USER,
            TcDataItems::FORBIDDEN_STATUS => 301
        ],
        RouteType::USER_REWARD_POINTS => [
            TcDataItems::FORBIDDEN_ROUTE_TYPE => RouteType::USER,
            TcDataItems::FORBIDDEN_STATUS => 301
        ],
        RouteType::USER_RMAS => [
            TcDataItems::FORBIDDEN_ROUTE_TYPE => RouteType::USER,
            TcDataItems::FORBIDDEN_STATUS => 301
        ],
        RouteType::USER_SALES_AGENT => [
            TcDataItems::FORBIDDEN_ROUTE_TYPE => RouteType::USER,
            TcDataItems::FORBIDDEN_STATUS => 301
        ],
        RouteType::USER_RECOMMENDED_BASKETS => [
            TcDataItems::FORBIDDEN_ROUTE_TYPE => RouteType::USER,
            TcDataItems::FORBIDDEN_STATUS => 301
        ],
        RouteType::USER_SPONSORSHIP => [
            TcDataItems::FORBIDDEN_ROUTE_TYPE => RouteType::USER,
            TcDataItems::FORBIDDEN_STATUS => 301
        ],
        RouteType::USER_STOCK_ALERTS => [
            TcDataItems::FORBIDDEN_ROUTE_TYPE => RouteType::USER,
            TcDataItems::FORBIDDEN_STATUS => 301
        ],
        RouteType::USER_SUBSCRIPTIONS => [
            TcDataItems::FORBIDDEN_ROUTE_TYPE => RouteType::USER,
            TcDataItems::FORBIDDEN_STATUS => 301
        ],
        RouteType::USER_USER_WELCOME => [
            TcDataItems::FORBIDDEN_ROUTE_TYPE => RouteType::USER,
            TcDataItems::FORBIDDEN_STATUS => 301
        ],
        RouteType::USER_WISHLIST => [
            TcDataItems::FORBIDDEN_ROUTE_TYPE => RouteType::USER,
            TcDataItems::FORBIDDEN_STATUS => 301
        ]
    ];

    /**
     * This method returns true if the 'theme configuration' allows to run/add
     * batch requests for the given 'route type', it returns false otherwise.
     *
     * @param string $routeType
     *
     * @return bool
     */
    public function routeTypeBatchRequestsFilter(string $routeType): bool;

    /**
     * This method adds the given BatchRequest for the 'theme configuration'.
     *
     * @param BatchRequests $requests
     */
    public function addBatchRequests(BatchRequests $requests, string $routeType);

    /**
     * This method runs after send the 'theme configuration' batchRequest
     * and here you can work with the response of the batch request
     * or/and calculate some values that will be usable in all pages.
     *
     * @param array $batchResult
     *
     * @return array
     */
    public function getCalculatedData(array $batchResult): array;

    /**
     * This method returns the 'theme configuration' data.
     *
     * @return array
     */
    public function getConfigurationData(): array;

    /**
     * This method runs the Response::redirect() method with the corresponding parameters
     * based on the corresponding definition for the given routeType (that is defined in
     * the self::FORBIDDEN_RESPONSE_ACTIONS constant).
     *
     * @param string $routeType
     *
     * @see self::FORBIDDEN_RESPONSE_ACTIONS
     */
    public function runForbiddenResponse(string $routeType): void;
}
