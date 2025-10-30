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
use FWK\Services\PluginService;
use SDK\Dtos\Common\Route;
use FWK\Core\Controllers\Traits\CheckCaptcha;

/**
 * This is the DeletePaymentCardController controller class.
 * This class extends FWK\Core\Controllers\BaseJsonController, see this class.
 *
 * @see BaseJsonController
 * 
 * @uses CheckCaptcha
 *
 * @package FWK\Controllers\User\Internal
 */
class DeletePaymentCardController extends BaseJsonController {
    use CheckCaptcha;

    protected bool $loggedInRequired = true;

    private ?PluginService $pluginService = null;

    /**
     * Constructor method.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->pluginService = Loader::service(Services::PLUGIN);
        $this->responseMessage = $this->language->getLabelValue(LanguageLabels::CARD_DELETED, $this->responseMessage);
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FormFactory::getDeletePaymentCard()->getInputFilterParameters();
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
        $this->checkCaptcha();
        $id = $this->getRequestParam(Parameters::ID, true);
        $token = $this->getRequestParam(Parameters::TOKEN, true);
        $this->appliedParameters[Parameters::ID] = $id;
        $this->appliedParameters[Parameters::TOKEN] = $token;
        $result = $this->pluginService->deleteUserPluginPaymentTokens($id, $token);
        return $result;
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
