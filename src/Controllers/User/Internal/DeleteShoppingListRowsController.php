<?php

namespace FWK\Controllers\User\Internal;

use FWK\Enums\Services;
use FWK\Core\Resources\Loader;
use SDK\Core\Dtos\Element;
use FWK\Enums\Parameters;
use FWK\Core\Controllers\BaseJsonController;
use FWK\Core\FilterInput\FilterInputHandler;
use SDK\Core\Resources\BatchRequests;
use FWK\Core\Form\FormFactory;
use FWK\Core\Resources\Session;
use FWK\Enums\LanguageLabels;
use SDK\Dtos\Common\Route;
use FWK\Core\Controllers\Traits\CheckCaptcha;
use FWK\Services\UserService;
use SDK\Services\Parameters\Groups\User\DeleteShoppingListRowsParametersGroup;

/**
 * This is the DeleteShoppingListRowsController controller class.
 * This class extends FWK\Core\Controllers\BaseJsonController, see this class.
 *
 * @see BaseJsonController
 * 
 * @uses CheckCaptcha
 *
 * @package FWK\Controllers\User\Internal
 */
class DeleteShoppingListRowsController extends BaseJsonController {
    use CheckCaptcha;

    protected bool $loggedInRequired = true;

    protected ?DeleteShoppingListRowsParametersGroup $deleteShoppingListRowsParametersGroup = null;

    protected ?UserService $userService = null;

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->responseMessage = $this->language->getLabelValue(LanguageLabels::SHOPPING_LIST_ROWS_DELETED, $this->responseMessage);
        $this->responseMessageError = $this->language->getLabelValue(LanguageLabels::SHOPPING_LIST_ROWS_DELETE_ERROR, $this->responseMessageError);
        $this->deleteShoppingListRowsParametersGroup = new DeleteShoppingListRowsParametersGroup();
        $this->userService = Loader::service(Services::USER);
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FormFactory::getDeleteShoppingListRows()->getInputFilterParameters();
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
        return FilterInputHandler::PARAMS_FROM_POST_DATA_OBJECT;
    }


    /**
     * This method initialize applied parameters, runs previously to run preSendControllerBaseBatchData
     *
     */
    protected function initializeAppliedParameters(): void {
        $requestParams = $this->getRequestParams();
        if (isset($requestParams[Parameters::ROW_ID_LIST])) {
            $requestParams[Parameters::ROW_ID_LIST] = !is_null($requestParams[Parameters::ROW_ID_LIST]) ? explode(',', $requestParams[Parameters::ROW_ID_LIST]) : [];
        }
        if (isset($requestParams[Parameters::PRODUCT_ID_LIST])) {
            $requestParams[Parameters::PRODUCT_ID_LIST] = !is_null($requestParams[Parameters::PRODUCT_ID_LIST]) ? explode(',', $requestParams[Parameters::PRODUCT_ID_LIST]) : [];
        }
        if (isset($requestParams[Parameters::BUNDLE_ID_LIST])) {
            $requestParams[Parameters::BUNDLE_ID_LIST] = !is_null($requestParams[Parameters::BUNDLE_ID_LIST]) ? explode(',', $requestParams[Parameters::BUNDLE_ID_LIST]) : [];
        }
        $this->appliedParameters = $this->userService->generateParametersGroupFromArray($this->deleteShoppingListRowsParametersGroup, $requestParams);
    }

    /**
     *
     * @see \FWK\Controllers\User\Internal\AddShoppingListRowController::getResponseData()
     */
    protected function getResponseData(): ?Element {
        $this->checkCaptcha();
        $response = $this->userService->deleteShoppingListRows($this->getRequestParam(Parameters::SHOPPING_LIST_ID, false, $this->getSession()->getShoppingList()->getDefaultOneId()), $this->deleteShoppingListRowsParametersGroup);
        Session::getInstance()->setAggregateDataFromApiRequest();
        return $response;
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
