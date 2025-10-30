<?php

namespace FWK\Controllers\Checkout\Internal;

use FWK\Core\Controllers\SetUserController;
use FWK\Core\Controllers\Traits\RecalculateBasketTrait;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\Form\FormFactory;
use FWK\Core\Resources\Loader;
use FWK\Core\Resources\RoutePaths;
use FWK\Core\Resources\Session;
use FWK\Core\Resources\Utils;
use FWK\Enums\Parameters;
use FWK\Enums\Services;
use FWK\Enums\SetUserTypeForms;
use FWK\Services\BasketService;
use SDK\Core\Dtos\Element;
use SDK\Core\Resources\BatchRequests;
use SDK\Core\Services\Parameters\Groups\CustomTagDataParametersGroup;
use SDK\Dtos\Common\Route;
use SDK\Enums\RouteType;
use SDK\Services\Parameters\Groups\Basket\EditBasketCustomTagParametersGroup;
use SDK\Services\Parameters\Groups\Basket\EditBasketCustomTagsParametersGroup;


/**
 * This is the NextStepController controller class.
 * This class extends FWK\Core\Controllers\BaseJsonController, see this class.
 *
 * @see BaseJsonController
 *
 * @package FWK\Controllers\Checkout\Internal
 */
class NextStepController extends SetUserController {
    use RecalculateBasketTrait;

    private ?BasketService $basketService = null;

    private string $redirect = '';

    protected bool $setUser = false;

    protected bool $newUser = false;

    /**
     * Constructor method.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->basketService = Loader::service(Services::BASKET);
    }

    /**
     *
     * @see \FWK\Core\Controllers\SetUserController::getTypeForm()
     */
    protected function getTypeForm(): string {
        return FormFactory::SET_USER_TYPE_ADD_CUSTOMER;
    }

    /**
     *
     * @see \FWK\Core\Controllers\SetUserController::getUrlRedirect()
     */
    protected function getUrlRedirect(): string {
        return '';
    }

    protected function getParsedParamsData(): array {
        if (isset($this->getRequestParams()['userForm']) && !Utils::isExpressCheckout($this->getSession()->getBasket())) {
            $this->setUser = true;
            return self::parseData($this->getRequestParams()['userForm'], Utils::isSimulatedUser($this->getSession()));
        }
        return [];
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return array_merge(
            FilterInputFactory::getCheckoutNextStep(),
            FilterInputFactory::getOSCNextStep(),
            FilterInputFactory::getCustomTagsParameter()
        );
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
        $response = null;

        if ($this->setUser) {
            // get basket
            $basketBeforeLogin = Session::getInstance()->getBasket();

            $setUserResult = parent::getResponseData();

            if (!is_null($setUserResult->getError())) {
                throw new CommerceException($setUserResult->getError()->getCode());
            }

            if (isset($this->appliedParameters[SetUserTypeForms::USER]) && isset($this->appliedParameters[SetUserTypeForms::USER][Parameters::CREATE_ACCOUNT])) {
                $this->newUser = true;
            } else {
                $this->newUser = false;
            }

            // get basket again
            $basketAfterLogin = Session::getInstance()->getBasket();
            if ($basketAfterLogin->getTotals()->getTotal() !== $basketBeforeLogin->getTotals()->getTotal()) {
                $this->redirect = RoutePaths::getPath(RouteType::CHECKOUT);
                return $basketAfterLogin;
            }
        }

        // Reset appliedParameters after setUser 
        $this->appliedParameters = $this->getRequestParams();

        $errorMessage = '';
        $errorResponse = null;

        if (isset($this->appliedParameters[Parameters::UPDATE_BASKET_ROWS])) {
            $this->getRecalculateBasketResponseData($this->getRequestParam(Parameters::UPDATE_BASKET_ROWS));
            if (!is_null($this->editedRowsErrorResponse?->getError())) {
                $errorMessage .= $this->editedRowsErrorMessage;
                $errorResponse = $this->editedRowsErrorResponse;
            }
        }

        if (isset($this->appliedParameters[Parameters::COMMENT])) {
            $response = $this->basketService->comment($this->appliedParameters[Parameters::COMMENT]);
            if (!is_null($response->getError())) {
                $errorMessage .= Utils::getErrorLabelValue($response) . '. ';
                $errorResponse = $response;
            }
        }

        if (isset($this->appliedParameters[Parameters::CUSTOM_TAGS])) {
            $basketCustomTags = Session::getInstance()->getBasket()->getCustomTagValues();
            $newCustomTagValues = [];
            foreach ($basketCustomTags as $customTagsValues) {
                if (array_key_exists($customTagsValues->getCustomTagId(), $this->appliedParameters[Parameters::CUSTOM_TAGS])) {
                    $value = $this->appliedParameters[Parameters::CUSTOM_TAGS][$customTagsValues->getCustomTagId()];
                    $value = is_null($value) ? '' : $value;
                    if ($value != $customTagsValues->getValue()) {
                        $editBasketCustomTagParametersGroup = new EditBasketCustomTagParametersGroup();
                        $ctParam = ['customTagId' => $customTagsValues->getCustomTagId()];
                        $objValue = json_decode($value);
                        if (is_object($objValue) && property_exists($objValue, 'extension') && property_exists($objValue, 'fileName') && property_exists($objValue, 'value')) {
                            $customTagData = new CustomTagDataParametersGroup();
                            $customTagData->setExtension($objValue->extension);
                            $customTagData->setFileName($objValue->fileName);
                            $customTagData->setValue($objValue->value);
                            $ctParam['data'] = $customTagData;
                            $editBasketCustomTagParametersGroup->setCustomTagId($customTagsValues->getCustomTagId());
                            $editBasketCustomTagParametersGroup->setData($customTagData);
                            $this->appliedParameters[self::CUSTOM_TAGS][] = [
                                'customTagId' => $customTagsValues->getCustomTagId(),
                                'data' => $customTagData->toArray()
                            ];
                        } else {
                            $ctParam['value'] = $value;
                            $this->appliedParameters[self::CUSTOM_TAGS][] = $this->userService->generateParametersGroupFromArray($editBasketCustomTagParametersGroup, $ctParam);
                        }
                        $newCustomTagValues[] = $editBasketCustomTagParametersGroup;
                    }
                }
            }

            if (!empty($newCustomTagValues)) {
                $editBasketCustomTagsParametersGroup = new EditBasketCustomTagsParametersGroup();
                $editBasketCustomTagsParametersGroup->setCustomTags($newCustomTagValues);
                $response = $this->basketService->setCustomTags($editBasketCustomTagsParametersGroup);
                if (!is_null($response->getError())) {
                    $errorMessage .= Utils::getErrorLabelValue($response) . '. ';
                    $errorResponse = $response;
                }
            }
        }

        if (!is_null($errorResponse)) {
            $this->responseMessageError = Utils::getErrorLabelValue($response);
            return $errorResponse;
        } else {
            if (isset($this->appliedParameters[Parameters::ACTION]) && RouteType::isValid($this->appliedParameters[Parameters::ACTION])) {
                $action = $this->appliedParameters[Parameters::ACTION];
                $this->redirect = RoutePaths::getPath($action);
            }
            return Session::getInstance()->getBasket();
        }
    }

    /**
     * This method parses the given Element and returns it.
     * 
     * @param Element $response
     * 
     * @return \SDK\Core\Dtos\Element
     */
    protected function parseResponseData(Element $basket) {
        return [
            'redirect' => $this->redirect
        ];
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
