<?php

namespace FWK\Controllers\User\Internal;

use FWK\Core\Controllers\BaseJsonController;
use FWK\Core\Controllers\Traits\CheckCaptcha;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\Form\FormFactory;
use FWK\Core\Resources\Loader;
use SDK\Dtos\Common\Route;
use FWK\Enums\Services;
use SDK\Core\Dtos\Element;
use SDK\Core\Resources\BatchRequests;
use SDK\Services\Parameters\Groups\User\LoginParametersGroup;
use FWK\Enums\Parameters;
use FWK\Services\UserService;
use FWK\Core\Resources\Utils;
use FWK\Enums\LanguageLabels;

/**
 * This is the LoginController controller class.
 * This class extends FWK\Core\Controllers\BaseJsonController, see this class.
 *
 * @see BaseJsonController
 * 
 * @uses CheckCaptcha
 *
 * @package FWK\Controllers\User\Internal
 */
class LoginController extends BaseJsonController {
    use CheckCaptcha;

    protected const USER = 'user';

    protected const WARNING = 'warning';

    protected ?LoginParametersGroup $loginParameters = null;

    protected ?UserService $userService = null;

    protected string $redirect = '';

    protected string $warning = '';

    /**
     * Constructor method.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->userService = Loader::service(Services::USER);
        $this->loginParameters = new LoginParametersGroup();
        $this->appliedParameters = $this->userService->generateParametersGroupFromArray($this->loginParameters, $this->getRequestParams());
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FormFactory::getLogin()->getInputFilterParameters();
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
        $response = $this->userService->login($this->loginParameters);
        if (!is_null($response->getError()) && $response->getError()->getCode() == 'A01000-MULTIPLE_USABLE_ACCOUNTS') {
            $this->responseMessageError = 'A01000-MULTIPLE_USABLE_ACCOUNTS';
        } else {
            $this->responseMessageError = Utils::getErrorLabelValue($response);
            if (!is_null($response->getError())) {
                if ($response->getError()->getCode() == 'A01000-USER_IS_LOGGED_IN') {
                    // Update Session user and basket, to sync API session with FWK session
                    $response = Loader::service(Services::BASKET)->getBasket();
                } else if (
                    $response->getError()->getCode() == 'A01000-USER_REQUIRES_VERIFICATION'
                    || $response->getError()->getCode() == 'A01000-NONE_USABLE_ACCOUNTS_CAUSE_REQUIRED_VERIFICATION'
                ) {
                    $this->warning = $this->responseMessageError .= ', <a class="emailErrorLoginCall" data-lc-username="' . $this->getRequestParam(Parameters::USERNAME) . '" onclick="LC.dataEvents.userVerifyResend(event)">' . $this->language->getLabelValue(LanguageLabels::RESEND_EMAIL) . '</a>';
                    return new class($response->getError()->getCode()) extends Element {
                        private ?string $code = null;
                        public function __construct(?string $code) {
                            $this->code = $code;
                        }
                        public function jsonSerialize(): mixed {
                            return [
                                'code' => $this->code
                            ];
                        }
                    };
                }
            }
        }
        $this->redirect = $this->getRequestParam(Parameters::REDIRECT, false, '');

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
        return [
            Parameters::REDIRECT => $this->redirect,
            self::USER => $this->getSession()->getUser(),
            'warning' => $this->warning
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
