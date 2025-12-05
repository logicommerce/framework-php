<?php

namespace FWK\Controllers\Account;

use DateTime;
use DateTimeZone;
use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\Form\FormFactory;
use FWK\Core\Resources\Loader;
use FWK\Enums\Parameters;
use FWK\Enums\Services;
use SDK\Core\Application;
use SDK\Core\Resources\BatchRequests;
use SDK\Core\Resources\Date;
use SDK\Dtos\Common\Route;
use SDK\Services\Parameters\Groups\User\SalesAgentSalesParametersGroup;

/**
 * This is the registered user sales agent sales controller.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @see BaseHtmlController
 *
 * @package FWK\Controllers\Account
 */
class RegisteredUserSalesAgentSalesController extends BaseHtmlController {

    public const SALES_AGENT_SALES = "salesAgentSales";

    public const SALES_AGENT_SALES_FORM = "salesAgentSalesForm";

    public const SALES_AGENT_SALES_FORM_REQUEST = "salesAgentSalesFormRequest";

    protected ?DateTime $fromDate;

    protected ?DateTime $toDate;

    protected ?SalesAgentSalesParametersGroup $salesAgentSalesParametersGroup = null;

    protected bool $loggedInRequired = true;

    protected bool $salesAgentRequired = true;


    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->salesAgentSalesParametersGroup = new SalesAgentSalesParametersGroup();
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FormFactory::getAccountSalesAgentSales()->getInputFilterParameters();
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
        return FilterInputHandler::PARAMS_FROM_POST;
    }

    /**
     * This method initialize applied parameters, runs previously to run preSendControllerBaseBatchData
     *
     */
    protected function initializeAppliedParameters(): void {
        $paramFromDate = $this->getRequestParam(Parameters::FROM_DATE, false, '');
        $paramToDate = $this->getRequestParam(Parameters::TO_DATE, false, '');

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

        $this->salesAgentSalesParametersGroup->setFromDate($this->fromDate);
        $this->salesAgentSalesParametersGroup->setToDate($this->toDate);
    }


    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
        Loader::service(Services::ACCOUNT)->addGetSalesAgentSales($requests, self::SALES_AGENT_SALES, $this->salesAgentSalesParametersGroup);
    }

    /**
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     */
    protected function setControllerBaseData(): void {
        $items = [];
        $items[self::SALES_AGENT_SALES] = $this->getControllerData(self::SALES_AGENT_SALES);
        $items[self::SALES_AGENT_SALES_FORM] = FormFactory::getAccountSalesAgentSales(
            $this->fromDate,
            $this->toDate,
        );
        $this->setDataValue(self::CONTROLLER_ITEM, $items);
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
