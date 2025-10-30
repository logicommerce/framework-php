<?php

namespace FWK\Controllers\Account;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\Form\FormFactory;
use FWK\Core\Resources\Loader;
use FWK\Core\Theme\Dtos\ItemList;
use FWK\Enums\Parameters;
use FWK\Enums\Services;
use FWK\Services\AccountService;
use SDK\Core\Resources\BatchRequests;
use SDK\Core\Resources\Date;
use SDK\Dtos\Common\Route;
use SDK\Enums\AccountKey;
use SDK\Services\Parameters\Groups\Account\AccountOrderParametersGroup;

/**
 * This is the account orders controller.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @controllerData: self::CONTROLLER_ITEM => \SDK\Core\Dtos\ElementCollection of \SDK\Dtos\Account\AccountOrder
 * 
 * @twigContent: \themes\{{themeName}}\{{version}}\Content\Account\Orders\default.html.twig
 * 
 * @RouteType: \SDK\Enums\RouteType::ACCOUNT_ORDERS
 *
 * @package FWK\Controllers\Account
 */
class OrdersController extends BaseHtmlController {

    public const ORDERS_FORM = "ordersForm";

    public const ORDERS_FILTER = 'ordersFilter';

    public const ORDER_LIST_DATA = 'orderListData';

    public const ACCOUNT = 'account';

    protected array $ordersFilter = [];

    protected array $additionalRequestParameters = [];

    private ?AccountService $accountService = null;

    protected ?AccountOrderParametersGroup $accountOrderparametersGroup = null;

    protected ?ItemList $orderListConfiguration = null;

    protected string $id_used = AccountKey::USED;

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
        return FormFactory::getAccountOrders()->getInputFilterParameters() +
            FilterInputFactory::getPaginableItemsParameter() +
            FilterInputFactory::getSortableItemsParameters('OrderSort');
    }


    /**
     * Constructor method.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->id_used = empty($route->getId()) ?  AccountKey::USED : $route->getId();
        $this->accountService = Loader::service(Services::ACCOUNT);
        $this->accountOrderparametersGroup = new AccountOrderParametersGroup();
        $this->orderListConfiguration = self::getTheme()->getConfiguration()->getOrderList()->getRowsList();
    }

    /**
     * This method initialize applied parameters, runs previously to run preSendControllerBaseBatchData
     *
     */
    protected function initializeAppliedParameters(): void {
        $requestParams = array_merge(
            $this->orderListConfiguration->getDefaultParametersValues(),
            $this->getRequestParams(),
            $this->additionalRequestParameters,
            $this->orderListConfiguration->getRequestParameters()
        );

        if (isset($requestParams[Parameters::ADDED_FROM]) && !empty($requestParams[Parameters::ADDED_FROM])) {
            $requestParams[Parameters::ADDED_FROM] = Date::create($requestParams[Parameters::ADDED_FROM])->getDateTime()->setTime(00, 00, 00);
        }

        if (isset($requestParams[Parameters::ADDED_TO]) && !empty($requestParams[Parameters::ADDED_TO])) {
            $requestParams[Parameters::ADDED_TO] = Date::create($requestParams[Parameters::ADDED_TO])->getDateTime()->setTime(23, 59, 59);
        }

        $this->ordersFilter = $this->accountService->generateParametersGroupFromArray($this->accountOrderparametersGroup, $requestParams);
    }

    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
        $ordersForm = FormFactory::getAccountOrders($this->accountOrderparametersGroup->toArray());

        $this->accountService->addGetOrders($requests, self::CONTROLLER_ITEM, $this->id_used, $this->accountOrderparametersGroup);
        $this->accountService->addGetAccounts($requests, self::ACCOUNT, $this->id_used);
        $this->setDataValue(self::ORDERS_FILTER, $this->ordersFilter);
        $this->setDataValue(self::ORDERS_FORM, $ordersForm);
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
        $this->setDataValue(self::ORDER_LIST_DATA, $this->orderListConfiguration);
    }
}
