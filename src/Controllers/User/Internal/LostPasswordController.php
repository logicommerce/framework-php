<?php

namespace FWK\Controllers\User\Internal;

use FWK\Enums\Services;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\Resources\Loader;
use SDK\Dtos\Common\Route;
use FWK\Core\Form\FormFactory;
use SDK\Core\Dtos\Element;
use SDK\Core\Resources\BatchRequests;
use FWK\Core\Controllers\BaseJsonController;
use FWK\Services\UserService;
use FWK\Enums\LanguageLabels;
use FWK\Enums\Parameters;
use SDK\Core\Exceptions\InvalidParameterException;
use FWK\Core\Resources\Utils;
use FWK\Core\Controllers\Traits\CheckCaptcha;

/**
 * This is the LostPasswordController controller class.
 * This class extends FWK\Core\Controllers\BaseJsonController, see this class.
 *
 * @see BaseJsonController
 * 
 * @uses CheckCaptcha
 *
 * @package FWK\Controllers\User\Internal
 */
class LostPasswordController extends BaseJsonController {
    use CheckCaptcha;

    private ?UserService $userService = null;

    private string $redirect = '';

    /**
     * Constructor method.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->userService = Loader::service(Services::USER);
        $this->responseMessage = $this->language->getLabelValue(LanguageLabels::CHANGE_PASSWORD_SEND, $this->responseMessage);
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FormFactory::getLostPassword()->getInputFilterParameters();
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
        try {
            $response = $this->userService->recoverPassword($this->getRequestParam(Parameters::USERNAME, true));
            $this->responseMessageError = Utils::getErrorLabelValue($response);
        } catch (InvalidParameterException $e) {
            $this->responseMessageError = $this->language->getInstance()->getLabelValue(LanguageLabels::ERROR_LOST_PASSWORD_EMPTY);
        }

        return $response;
    }

    /**
     * This method parses the given Element and returns it.
     * 
     * @param Element $response
     * 
     * @return \SDK\Core\Dtos\Element
     */
    protected function parseResponseData(Element $user) {
        // Not found the reason for the redirect parameter in js
        return [
            Parameters::REDIRECT => $this->redirect
        ];
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
