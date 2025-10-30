<?php

namespace FWK\Controllers\User;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\Resources\Loader;
use FWK\Enums\Services;
use FWK\Services\AccountService;
use FWK\Services\UserService;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Common\Route;
use SDK\Enums\AccountKey;
use SDK\Services\Parameters\Groups\Account\AccountOrderParametersGroup;
use SDK\Services\Parameters\Groups\User\OrderParametersGroup;

/**
 * This is the user orders controller.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @controllerData: self::CONTROLLER_ITEM => \SDK\Core\Dtos\ElementCollection of \SDK\Dtos\User\UserOrder
 * 
 * @twigContent: \themes\{{themeName}}\{{version}}\Content\User\Orders\default.html.twig
 * 
 * @RouteType: \SDK\Enums\RouteType::USER_ORDERS
 *
 * @package FWK\Controllers\User
 */
class OrdersController extends BaseHtmlController {

    private ?UserService $userService = null;

    protected ?OrderParametersGroup $orderparametersGroup = null;

    private ?AccountService $accountService = null;

    protected ?AccountOrderParametersGroup $accountOrderparametersGroup = null;

    /**
     * This method returns the origin of the params (see FilterInputHandler::PARAMS_FROM_GET, FilterInputHandler::PARAMS_FROM_QUERY_STRING or FilterInputHandler::PARAMS_FROM_POST,...).
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     *
     * @see FilterInputHandler
     */
    protected function getOriginParams() {
        return FilterInputHandler::PARAMS_FROM_GET;
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FilterInputFactory::getPaginableItemsParameter();
    }



    /**
     * Constructor method.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->userService = Loader::service(Services::USER);
        $this->orderparametersGroup = new OrderParametersGroup();
        $this->accountService = Loader::service(Services::ACCOUNT);
        $this->accountOrderparametersGroup = new AccountOrderParametersGroup();
    }

    /**
     * This method initialize applied parameters, runs previously to run preSendControllerBaseBatchData
     *
     */
    protected function initializeAppliedParameters(): void {
        $defaultParametersValue = self::getTheme()->getConfiguration()->getOrderList()->getRowsList()->getDefaultParametersValues();
        $requestParams = array_merge($defaultParametersValue, $this->getRequestParams());

        $this->accountService->generateParametersGroupFromArray($this->accountOrderparametersGroup, $requestParams);
    }

    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
        $this->accountService->addGetOrders($requests, self::CONTROLLER_ITEM, AccountKey::USED, $this->accountOrderparametersGroup);
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
