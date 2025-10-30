<?php

namespace FWK\Controllers\User\Internal;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\Form\FormFactory;
use FWK\Enums\Services;
use FWK\Core\Resources\Loader;
use FWK\Core\Resources\Response;
use FWK\Core\Resources\RoutePaths;
use FWK\Enums\Parameters;
use FWK\Enums\RouteType;
use FWK\Services\OrderService;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Common\Route;

/**
 * This is the user delivery note controller.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @see BaseHtmlController
 *
 * @package FWK\Controllers\User
 */
class ReturnController extends BaseHtmlController {

    public const RETURN_PRODUCTS = "returnProducts";

    public const RETURN_POINTS = "returnPoints";

    public const RETURN_REQUEST_FORM = "returnRequestForm";

    public const RMA_REASONS = "rmaReasons";

    private ?OrderService $orderService = null;

    protected bool $getAllReturnPoints = true;

    /**
     * Constructor method.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->orderService = Loader::service(Services::ORDER);
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FilterInputFactory::getIdParameter();
    }

    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
        $this->orderService->addGetReturnProducts($requests, self::RETURN_PRODUCTS, $this->getRequestParam(Parameters::ID, true));
        $this->orderService->addGetRMAReasons($requests, self::RMA_REASONS);
        if (!$this->getAllReturnPoints) {
            $this->orderService->addGetReturnPoints($requests, self::RETURN_POINTS, $this->getRequestParam(Parameters::ID, true));
        }
    }

    /**
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     */
    protected function setControllerBaseData(): void {
        if ($this->getAllReturnPoints) {
            $this->setDataValue(self::RETURN_POINTS, $this->orderService->getAllReturnPoints($this->getRequestParam(Parameters::ID, true)));
        }

        $aux = [];
        $aux[self::RETURN_PRODUCTS] = $this->getControllerData(self::RETURN_PRODUCTS);
        $aux[self::RETURN_POINTS] = $this->getControllerData(self::RETURN_POINTS);

        if (!empty($this->getControllerData(self::RETURN_PRODUCTS)->getItems())) {
            $aux[self::RETURN_REQUEST_FORM] = FormFactory::getReturnRequest(
                $this->getRequestParam(Parameters::ID, true),
                $this->getControllerData(self::RETURN_PRODUCTS),
                $this->getControllerData(self::RETURN_POINTS),
                $this->getControllerData(self::RMA_REASONS)
            );
        } else {
            $aux[self::RETURN_REQUEST_FORM] = null;
        }
        $this->setDataValue(self::CONTROLLER_ITEM, $aux);

        $this->deleteControllerData(self::RETURN_PRODUCTS);
        $this->deleteControllerData(self::RETURN_POINTS);
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
     * This method checks if the data required for the correct run of the controller has a correct value, in opposite, it breaks the execution of the controller
     *
     * @param Object $data
     *            is the data required for the correct run of the controller
     * @param string $onExceptionCode
     *            is the Exception to be thrown if the check is ko.
     *            
     * @return void
     */
    protected function checkCriticalServiceLoaded(?Object $data, string $onExceptionCode = CommerceException::CONTROLLER_UNDEFINED_CRITICAL_DATA): void {
        if (!is_null($data->getError()) && $data->getError()->getStatus() === 403) {
            Response::redirect(RoutePaths::getPath(RouteType::USER));
        } else {
            parent::checkCriticalServiceLoaded($data, $onExceptionCode);
        }
    }
}
