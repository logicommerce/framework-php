<?php

namespace FWK\Controllers\Page\Internal;

use FWK\Core\Controllers\BaseJsonController;
use FWK\Enums\LanguageLabels;
use FWK\Core\FilterInput\FilterInputHandler;
use SDK\Dtos\Common\Route;
use FWK\Core\Form\FormFactory;
use SDK\Core\Dtos\Element;
use SDK\Core\Resources\BatchRequests;
use SDK\Services\Parameters\Groups\ContactParametersGroup;
use FWK\Core\Resources\Loader;
use FWK\Enums\Services;
use FWK\Core\Resources\Utils;
use FWK\Services\ContactService;
use FWK\Core\Controllers\Traits\CheckCaptcha;

/**
 * This is the FormContactController class.
 * This class extends BaseJsonController (FWK\Core\Controllers\BaseJsonController), see this class.
 *
 * @see BaseJsonController
 * 
 * @uses CheckCaptcha
 *
 * @package FWK\Controllers\Page\Internal
 */
class SendContactController extends BaseJsonController {
    use CheckCaptcha;

    private ?ContactService $contactService = null;

    private ?ContactParametersGroup $conactParameters = null;

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->contactService = Loader::service(Services::CONTACT);
        $this->conactParameters = new ContactParametersGroup();
        $this->responseMessage = $this->language->getLabelValue(LanguageLabels::CONTACT_RESPONSE_OK);
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FormFactory::getContact()->getInputFilterParameters();
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
     * This method launches the adequate actions against the SDK (through the FWK services) and returns the response data. 
     *
     * @return Element
     */
    protected function getResponseData(): ?Element {
        $this->checkCaptcha();
        $this->appliedParameters += $this->contactService->generateParametersGroupFromArray($this->conactParameters, $this->getRequestParams());
        $response = $this->contactService->send($this->conactParameters, self::getTheme()->getConfiguration()->getDataValidators()->getContact());
        $this->responseMessageError = Utils::getErrorLabelValue($response);
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
