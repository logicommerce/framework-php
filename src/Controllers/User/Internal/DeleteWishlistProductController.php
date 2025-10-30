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
use FWK\Enums\LanguageLabels;
use SDK\Dtos\Common\Route;

/**
 * This is the DeleteWishlistProductController controller class.
 * This class extends FWK\Core\Controllers\BaseJsonController, see this class.
 *
 * @see BaseJsonController
 *
 * @package FWK\Controllers\User\Internal
 */
class DeleteWishlistProductController extends BaseJsonController {

    protected bool $loggedInRequired = true;

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->responseMessage = $this->language->getLabelValue(LanguageLabels::WISHLIST_DELETED, $this->responseMessage);
        $this->responseMessageError = $this->language->getLabelValue(LanguageLabels::WISHLIST_DELETE_ERROR, $this->responseMessageError);
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FormFactory::getDeleteWishlist()->getInputFilterParameters();
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
     *
     * @see \FWK\Controllers\User\Internal\AddWishlistProductController::getResponseData()
     */
    protected function getResponseData(): ?Element {
        $this->appliedParameters = $this->getRequestParams();
        unset($this->appliedParameters[Parameters::PATH]);
        $idList = explode(',', $this->getRequestParam(Parameters::PRODUCT_ID_LIST, true));
        foreach ($idList as $id) {
            Loader::service(Services::USER)->deleteWishlistProduct($id);
        }
        return $this->getSession()->getAggregateData()->getWishlist();
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
