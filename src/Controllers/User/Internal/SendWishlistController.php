<?php

namespace FWK\Controllers\User\Internal;

use FWK\Core\Controllers\BaseJsonController;
use SDK\Core\Dtos\Element;
use SDK\Core\Resources\BatchRequests;
use FWK\Core\Form\FormFactory;
use FWK\Core\FilterInput\FilterInputHandler;
use SDK\Services\Parameters\Groups\User\SendWishlistParametersGroup;
use SDK\Dtos\Common\Route;
use FWK\Core\Resources\Loader;
use FWK\Enums\Services;
use FWK\Enums\LanguageLabels;
use FWK\Services\UserService;

/**
 * This is the SendWishlistController controller class.
 * This class extends FWK\Core\Controllers\BaseJsonController, see this class.
 *
 * @see BaseJsonController
 *
 * @package FWK\Controllers\User\Internal
 */
class SendWishlistController extends BaseJsonController {

    protected bool $loggedInRequired = true;

    private ?UserService $userService = null;

    private ?SendWishlistParametersGroup $sendWishlistParametersGroup = null;

    /**
     * Constructor method.
     * 
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->userService = Loader::service(Services::USER);
        $this->sendWishlistParametersGroup = new SendWishlistParametersGroup();

        $this->responseMessageError = $this->language->getLabelValue(LanguageLabels::SEND_WISHLIST_RESPONSE_KO, $this->responseMessage);
        $this->responseMessage = $this->language->getLabelValue(LanguageLabels::SEND_WISHLIST_RESPONSE_OK, $this->responseMessage);
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
     * This method launches the adequate actions against the SDK (through the FWK services) and returns the response data. 
     *
     * @return Element
     */
    protected function getResponseData(): ?Element {
        $this->userService->generateParametersGroupFromArray($this->sendWishlistParametersGroup, $this->getRequestParams());
        return $this->userService->sendWishlist($this->sendWishlistParametersGroup);
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FormFactory::getSendWishlist()->getInputFilterParameters();
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
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
    }
}
