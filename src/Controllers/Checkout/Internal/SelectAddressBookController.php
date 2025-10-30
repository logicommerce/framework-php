<?php

namespace FWK\Controllers\Checkout\Internal;

use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\FilterInput\FilterInputFactory;
use SDK\Core\Dtos\Element;
use SDK\Core\Resources\BatchRequests;
use FWK\Enums\Parameters;
use FWK\Core\Controllers\BaseJsonController;
use FWK\Core\Resources\Loader;
use FWK\Core\Resources\Utils;
use FWK\Enums\Services;
use SDK\Dtos\Common\Route;
use FWK\Enums\LanguageLabels;
use FWK\Enums\SetUserTypeForms;
use FWK\Services\BasketService;
use SDK\Application;
use SDK\Core\Services\Parameters\Factories\UserToAccountFactory;
use SDK\Services\Parameters\Groups\Account\UpdateOmsBasketCustomerParametersGroup;
use SDK\Services\Parameters\Groups\Basket\SetBasketAddressesBookParametersGroup;

/**
 * This is the SelectAddressBookController controller class.
 * This class extends FWK\Core\Controllers\BaseJsonController, see this class.
 *
 * @see BaseJsonController
 *
 * @package FWK\Controllers\User\Internal
 */
class SelectAddressBookController extends BaseJsonController {

    private ?BasketService $basketService = null;

    private $setBasketAddressesBookParametersGroup = null;

    private ?UpdateOmsBasketCustomerParametersGroup $updateOmsBasketCustomerParametersGroup = null;

    private $id = 0;

    private $type = SetUserTypeForms::BILLING;

    /**
     * Constructor method.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->responseMessage = $this->language->getLabelValue(LanguageLabels::SAVED, $this->responseMessage);
        $this->basketService = Loader::service(Services::BASKET);
        $this->setBasketAddressesBookParametersGroup = new SetBasketAddressesBookParametersGroup();
        $this->updateOmsBasketCustomerParametersGroup = new UpdateOmsBasketCustomerParametersGroup();
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FilterInputFactory::getSetAddressBookParameters();
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
        $this->setBasketAddressesBookParametersGroup = new SetBasketAddressesBookParametersGroup();
        $this->id = $this->getRequestParam(Parameters::ID, false, 0);
        $this->type = $this->getRequestParam(Parameters::TYPE, true);
        $this->appliedParameters = [];

        if ($this->type === SetUserTypeForms::BILLING) {
            $this->setBasketAddressesBookParametersGroup->setBillingAddressId($this->id);
            $this->appliedParameters['billingAddressId'] = $this->id;
        } elseif ($this->type === SetUserTypeForms::SHIPPING) {
            if ($this->getRequestParam(Parameters::ACTION, true) == 'check_use_shipping') {
                $this->setBasketAddressesBookParametersGroup->setUseShippingAddress($this->id == 1);
                $this->appliedParameters['useShippingAddress'] = $this->id == 1;
            } else {
                $this->setBasketAddressesBookParametersGroup->setUseShippingAddress(true);
                $this->appliedParameters['useShippingAddress'] = true;
                $this->setBasketAddressesBookParametersGroup->setShippingAddressId($this->id);
                $this->appliedParameters['shippingAddressId'] = $this->id;
            }
        }
        if (!Application::getInstance()->getEcommerceSettings()->getAccountRegisteredUsersSettings()->getCardinalityPlus()) {
            $response = $this->basketService->setAddressesBook($this->setBasketAddressesBookParametersGroup);
        } else {
            $this->updateOmsBasketCustomerParametersGroup = UserToAccountFactory::mapSetAddressesBookToUpdateOmsBasketCustomer($this->setBasketAddressesBookParametersGroup);
            $response = $this->basketService->updateOmsBasketCustomer($this->updateOmsBasketCustomerParametersGroup);
        }

        $this->responseMessageError = Utils::getErrorLabelValue($response);

        if ($this->responseMessageError == '') {
            if ($this->type === SetUserTypeForms::BILLING) {
                $this->id = $response->getBasketUser()->getUser()->getSelectedBillingAddressId();
            } elseif ($this->type === SetUserTypeForms::SHIPPING) {
                $this->id = $response->getBasketUser()->getUser()->getSelectedShippingAddressId();
            }
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
    protected function parseResponseData(Element $response) {
        $data = [
            'data' => $response,
            'id' => $this->id,
            'type' => $this->type
        ];
        return $data;
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
     * This private method sends the batch requests of the controller to the SDK to obtain the data and returns the batch result
     *
     * @return array with the result of the batch request
     */
    protected function setControllerBaseBatchData(BatchRequests $requests): void {
    }
}
