<?php

namespace FWK\Controllers\User\Internal;

use FWK\Enums\Services;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\Resources\Loader;
use FWK\Core\FilterInput\FilterInputFactory;
use SDK\Core\Dtos\Element;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Common\Route;
use SDK\Services\Parameters\Groups\User\Addresses\BillingAddressParametersGroup;
use SDK\Services\Parameters\Groups\User\Addresses\ShippingAddressParametersGroup;
use FWK\Enums\LanguageLabels;
use FWK\Core\Resources\Utils;
use FWK\Enums\Parameters;
use FWK\Core\Controllers\BaseJsonController;
use FWK\Core\Controllers\SetUserController;
use FWK\Enums\SetUserTypeForms;
use FWK\Services\UserService;
use FWK\Core\Controllers\Traits\CheckCaptcha;
use SDK\Services\Parameters\Groups\User\Addresses\AddressValidateParametersGroup;

/**
 * This is the SetAddressBookController controller class.
 * This class extends FWK\Core\Controllers\BaseJsonController, see this class.
 *
 * @see BaseJsonController
 * 
 * @uses CheckCaptcha
 *
 * @package FWK\Controllers\User\Internal
 */
class SetAddressBookController extends BaseJsonController {
    use CheckCaptcha;

    protected bool $loggedInRequired = true;

    private ?UserService $userService = null;

    /**
     * Constructor method.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->userService = Loader::service(Services::USER);
        $this->responseMessage = $this->language->getLabelValue(LanguageLabels::SAVED, $this->responseMessage);
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return array_merge(SetUserController::getInputFilterParameters('', true, $this->getSession()->getUser()), FilterInputFactory::getSetAddressBookParameters());
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
        $response = null;
        $data = SetUserController::parseData($this->getRequestParams());

        $this->appliedParameters[Parameters::ID] = $this->getRequestParam(Parameters::ID, false, 0);
        $this->appliedParameters[Parameters::TYPE] = $this->getRequestParam(Parameters::TYPE, true);
        $this->appliedParameters[Parameters::ACTION] = $this->getRequestParam(Parameters::ACTION, false, '');
        if ($this->appliedParameters[Parameters::ACTION] == 'set_default_address') {
            $dataValidator = '';
        } else {
            $this->checkCaptcha();
            $dataValidator = 'get';
            if ($this->appliedParameters[Parameters::TYPE] === SetUserTypeForms::BILLING) {
                if ($this->appliedParameters[Parameters::ID] != 0) {
                    $dataValidator .= 'UpdateUser';
                }
                $dataValidator .= ucwords($this->appliedParameters[Parameters::TYPE]) . 'Address';
                $dataValidator .= ucwords($data[$this->appliedParameters[Parameters::TYPE]][Parameters::USER_TYPE]);
            } else {
                $dataValidator .= ucwords($this->appliedParameters[Parameters::TYPE]) . 'Address';
            }
            $dataValidator = self::getTheme()->getConfiguration()->getDataValidators()->$dataValidator();
        }

        if ($this->appliedParameters[Parameters::TYPE] === SetUserTypeForms::BILLING) {
            $fieldPrefix = 'BILLING_ADDRESS';
            $billingAddressParametersGroup = new BillingAddressParametersGroup();
            $billingAddressValidateParametersGroup = new AddressValidateParametersGroup();
            $this->appliedParameters[SetUserTypeForms::BILLING] = $this->userService->generateParametersGroupFromArray($billingAddressParametersGroup, $data[SetUserTypeForms::BILLING]);
            $this->userService->generateParametersGroupFromArray($billingAddressValidateParametersGroup, $data[SetUserTypeForms::BILLING]);
            $response = $this->userService->addressValidate($billingAddressValidateParametersGroup);
            if ($response->getValid()) {
                if (isset($data[SetUserTypeForms::BILLING]['locationAppliedParameters'])) {
                    $this->appliedParameters[SetUserTypeForms::BILLING][Parameters::LOCATION] = $data[SetUserTypeForms::BILLING]['locationAppliedParameters'];
                }
                if ($this->appliedParameters[Parameters::ACTION] == 'set_default_address') {
                    $billingAddressParametersGroup->setDefaultAddress(true);
                }
                if ($this->appliedParameters[Parameters::ID] == 0) {
                    $response = $this->userService->createBillingAddress($billingAddressParametersGroup, $dataValidator);
                } else {
                    $response = $this->userService->updateBillingAddress($this->appliedParameters[Parameters::ID], $billingAddressParametersGroup, $dataValidator);
                }
            }
        } else {
            $fieldPrefix = 'SHIPPING_ADDRESS';
            $shippingAddressParametersGroup = new ShippingAddressParametersGroup();
            $shippingAddressValidateParametersGroup = new AddressValidateParametersGroup();
            $this->appliedParameters[SetUserTypeForms::SHIPPING] = $this->userService->generateParametersGroupFromArray($shippingAddressParametersGroup, $data[SetUserTypeForms::SHIPPING]);
            $this->userService->generateParametersGroupFromArray($shippingAddressValidateParametersGroup, $data[SetUserTypeForms::SHIPPING]);
            $response = $this->userService->addressValidate($shippingAddressValidateParametersGroup);
            if ($response->getValid()) {
                if (isset($data[SetUserTypeForms::SHIPPING]['locationAppliedParameters'])) {
                    $this->appliedParameters[SetUserTypeForms::SHIPPING][Parameters::LOCATION] = $data[SetUserTypeForms::SHIPPING]['locationAppliedParameters'];
                }
                if ($this->appliedParameters[Parameters::ACTION] == 'set_default_address') {
                    $shippingAddressParametersGroup->setDefaultAddress(true);
                }
                if ($this->appliedParameters[Parameters::ID] == 0) {
                    $response = $this->userService->createShippingAddress($shippingAddressParametersGroup, $dataValidator);
                } else {
                    $response = $this->userService->updateShippingAddress($this->appliedParameters[Parameters::ID], $shippingAddressParametersGroup, $dataValidator);
                }
            }
        }
        $this->responseMessageError = Utils::getErrorLabelValue($response, $fieldPrefix);

        return $response;
    }

    /**
     * This method parses the given Element and returns it.
     * 
     * @param Element $response
     * 
     * @return \SDK\Core\Dtos\Element
     */
    protected function parseResponseData(Element $response) {
        return [
            'data' => $response,
            'id' => $this->appliedParameters[Parameters::ID],
            'type' => $this->appliedParameters[Parameters::TYPE]
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
    protected function setControllerBaseBatchData(BatchRequests $requests): void {
    }
}
