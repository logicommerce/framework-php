<?php

namespace FWK\Controllers\User\Internal;

use DateTime;
use DateTimeZone;
use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\Form\FormFactory;
use FWK\Core\Resources\Loader;
use FWK\Enums\Parameters;
use FWK\Enums\Services;
use FWK\Services\UserService;
use SDK\Core\Resources\BatchRequests;
use SDK\Core\Resources\Date;
use SDK\Dtos\Common\Route;
use SDK\Services\Parameters\Groups\User\SalesAgentCustomersParametersGroup;
use SDK\Core\Application;

/**
 * This is the sales agent customers controller.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @see BaseHtmlController
 *
 * @package FWK\Controllers\User
 * @deprecated
 */
class SalesAgentCustomersController extends BaseHtmlController {

    public const SALES_AGENT_CUSTOMERS = "salesAgentCustomers";

    public const SALES_AGENT_CUSTOMERS_FORM = "salesAgentCustomersForm";

    public const SALES_AGENT_CUSTOMERS_FORM_REQUEST = "salesAgentCustomersFromRequest";

    protected string $q = '';

    protected ?DateTime $fromDate;

    protected ?DateTime $toDate;

    protected bool $includeSubordinates;

    protected UserService $userService;

    protected SalesAgentCustomersParametersGroup $salesAgentCustomersParametersGroup;

    protected bool $loggedInRequired = true;

    protected bool $salesAgentRequired = true;

    protected bool $getAllSalesAgentCustomers = true;

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->userService = Loader::service(Services::USER);
        $this->salesAgentCustomersParametersGroup = new SalesAgentCustomersParametersGroup();
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FormFactory::getSalesAgentCustomers()->getInputFilterParameters() +
            FilterInputFactory::getPaginableItemsParameter();
    }

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
     * This method initialize applied parameters, runs previously to run preSendControllerBaseBatchData
     *
     */
    protected function initializeAppliedParameters(): void {

        $this->q = $this->getRequestParam(Parameters::Q, false, '');
        $paramFromDate = $this->getRequestParam(Parameters::FROM_DATE, false, '');
        $paramToDate = $this->getRequestParam(Parameters::TO_DATE, false, '');
        $this->includeSubordinates = $this->getRequestParam(Parameters::INCLUDE_SUBORDINATES, false, false);

        if (strlen($paramFromDate) > 0 && !is_null(Date::create($paramFromDate))) {
            $this->fromDate = Date::create($paramFromDate)->getDateTime();
        } else {
            $this->fromDate = new DateTime('now', new DateTimeZone(Application::getInstance()->getEcommerceSettings()->getGeneralSettings()->getTimeZone()));
            date_sub($this->fromDate, date_interval_create_from_date_string('7 days'));
        }
        $this->fromDate->setTime(00, 00, 00);
        if (strlen($paramToDate) > 0 && !is_null(Date::create($paramToDate))) {
            $this->toDate = Date::create($paramToDate)->getDateTime();
        } else {
            $this->toDate = new DateTime('now', new DateTimeZone(Application::getInstance()->getEcommerceSettings()->getGeneralSettings()->getTimeZone()));
        }
        $this->toDate->setTime(23, 59, 59);

        if (!empty($this->q)) {
            $this->salesAgentCustomersParametersGroup->setQ($this->q);
        }
        $this->salesAgentCustomersParametersGroup->setFromDate($this->fromDate);
        $this->salesAgentCustomersParametersGroup->setToDate($this->toDate);
        $this->salesAgentCustomersParametersGroup->setIncludeSubordinates($this->includeSubordinates);
        /*if (isset($_GET[Parameters::PAGE])) {
            $this->salesAgentCustomersParametersGroup->setPage($_GET['page']);
        }*/

        $defaultParametersValue = self::getTheme()->getConfiguration()->getSalesAgentCustomers()->getRowsList()->getDefaultParametersValues();
        $requestParams = array_merge($defaultParametersValue, $this->getRequestParams());
        $requestParams['q'] = $requestParams['q'] ?? ($this->q !== '' ? $this->q : null);
        $requestParams['includeSubordinates'] = $this->includeSubordinates;
        $requestParams['fromDate'] = $this->fromDate;
        $requestParams['toDate'] = $this->toDate;
        if ($requestParams['q'] == '') {
            $requestParams['q'] = null;
        }
        $this->userService->generateParametersGroupFromArray($this->salesAgentCustomersParametersGroup, $requestParams);
    }

    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
        if (!$this->getAllSalesAgentCustomers) {
            $this->userService->addGetSalesAgentCustomers($requests, self::SALES_AGENT_CUSTOMERS, $this->salesAgentCustomersParametersGroup);
        }
    }

    /**
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     */
    protected function setControllerBaseData(): void {
        $aux = [];
        if ($this->getAllSalesAgentCustomers) {
            $aux[self::SALES_AGENT_CUSTOMERS] = $this->userService->getSalesAgentCustomers($this->salesAgentCustomersParametersGroup);
        } else {
            $aux[self::SALES_AGENT_CUSTOMERS] = $this->getControllerData(self::SALES_AGENT_CUSTOMERS);
        }
        $aux[self::SALES_AGENT_CUSTOMERS_FORM] = FormFactory::getSalesAgentCustomers(
            $this->q,
            $this->fromDate,
            $this->toDate,
            $this->includeSubordinates
        );

        $aux[self::SALES_AGENT_CUSTOMERS_FORM_REQUEST] = [
            'q' => $this->q,
            'includeSubordinates' => $this->includeSubordinates,
            'fromDate' => $this->fromDate->format(Date::DATETIME_FORMAT),
            'toDate' => $this->toDate->format(Date::DATETIME_FORMAT),
        ];
        $this->setDataValue(self::CONTROLLER_ITEM, $aux);
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
