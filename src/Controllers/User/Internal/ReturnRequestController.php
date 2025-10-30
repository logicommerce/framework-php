<?php

namespace FWK\Controllers\User\Internal;

use FWK\Enums\Services;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\Resources\Loader;
use SDK\Dtos\Common\Route;
use SDK\Core\Dtos\Element;
use SDK\Core\Resources\BatchRequests;
use FWK\Core\Controllers\BaseJsonController;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\Form\FormFactory;
use FWK\Enums\LanguageLabels;
use FWK\Enums\Parameters;
use FWK\Services\OrderService;
use SDK\Dtos\Catalog\PhysicalLocation;
use SDK\Dtos\Documents\Rows\TransactionDocumentRowProduct;
use SDK\Enums\DeliveryType;
use SDK\Services\Parameters\Groups\Document\CreateReturnRequestDeliveryParametersGroup;
use SDK\Services\Parameters\Groups\Document\CreateReturnRequestItemParametersGroup;
use SDK\Services\Parameters\Groups\Document\CreateReturnRequestParametersGroup;
use FWK\Core\Controllers\Traits\CheckCaptcha;
use SDK\Dtos\Documents\Transactions\Returns\RMAReason;
use SDK\Services\Parameters\Groups\Document\CreateReturnRequestRmaReasonParametersGroup;

/**
 * This is the ReturnRequestController controller class.
 * This class extends FWK\Core\Controllers\BaseJsonController, see this class.
 *
 * @see BaseJsonController
 * 
 * @uses CheckCaptcha
 *
 * @package FWK\Controllers\User\Internal
 */
class ReturnRequestController extends BaseJsonController {
    use CheckCaptcha;

    protected bool $loggedInRequired = true;

    protected ?OrderService $orderService = null;

    /**
     * Constructor method.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        $this->orderService = Loader::service(Services::ORDER);
        parent::__construct($route);
        $this->responseMessage = $this->language->getLabelValue(LanguageLabels::RETURN_REQUEST_OK, $this->responseMessage);
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        $data = FilterInputHandler::getFilterFilterInputs(FilterInputHandler::PARAMS_FROM_POST, FilterInputFactory::getDataParameters());
        $formData = json_decode($data[Parameters::DATA], true)['formData'];
        return FormFactory::getReturnRequest(
            0,
            $this->orderService->buildElementCollection($formData[FormFactory::RETURN_PRODUCTS], TransactionDocumentRowProduct::class),
            $this->orderService->buildElementCollection($formData[FormFactory::RETURN_POINTS], PhysicalLocation::class),
            $this->orderService->buildElementCollection($formData[FormFactory::RMA_REASONS], RMAReason::class)
        )->getInputFilterParameters();
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
        $items = [];
        $createReturnRequestParametersGroup = new CreateReturnRequestParametersGroup();
        foreach ($this->getRequestParams() as $parameter => $value) {
            if ($parameter === Parameters::RETURN_DELIVERY) {
                $createReturnRequestDeliveryParametersGroup = new CreateReturnRequestDeliveryParametersGroup();
                if ($value == 0) {
                    $createReturnRequestDeliveryParametersGroup->setType(DeliveryType::SHIPPING);
                } else {
                    $createReturnRequestDeliveryParametersGroup->setType(DeliveryType::PICKING);
                }
                $createReturnRequestDeliveryParametersGroup->setItemId($value);
                $createReturnRequestParametersGroup->setDelivery($createReturnRequestDeliveryParametersGroup);
            } elseif ($parameter === Parameters::RETURN_COMMENT) {
                $createReturnRequestParametersGroup->setComment($value);
            } else {
                $auxParam = explode("_", $parameter);
                if ($auxParam[0] === Parameters::RETURN_QUANTITY) {
                    $items[$auxParam[1]][Parameters::RETURN_QUANTITY] = $value;
                }
                if ($auxParam[0] === Parameters::RMA_REASON_ID) {
                    $items[$auxParam[1]][Parameters::RMA_REASON_ID] = $value;
                }
                if ($auxParam[0] === Parameters::RMA_REASON_COMMENT) {
                    $items[$auxParam[1]][Parameters::RMA_REASON_COMMENT] = $value;
                }
            }
        }

        foreach ($items as $hash => $item) {
            $createReturnRequestItemParametersGroup = new CreateReturnRequestItemParametersGroup();
            $createReturnRequestItemParametersGroup->setHash($hash);
            $createReturnRequestItemParametersGroup->setQuantity($item[Parameters::RETURN_QUANTITY]);
            if (isset($item[Parameters::RMA_REASON_ID])) {
                $createReturnRequestRmaReasonParametersGroup = new CreateReturnRequestRmaReasonParametersGroup();
                $createReturnRequestRmaReasonParametersGroup->setId($item[Parameters::RMA_REASON_ID]);
                if (isset($item[Parameters::RMA_REASON_COMMENT])) {
                    $createReturnRequestRmaReasonParametersGroup->setComment($item[Parameters::RMA_REASON_COMMENT]);
                }
                $createReturnRequestItemParametersGroup->setRmaReason($createReturnRequestRmaReasonParametersGroup);
            }
            $createReturnRequestParametersGroup->addItem($createReturnRequestItemParametersGroup);
        }
        return $this->orderService->createReturnRequest($this->getRequestParam(Parameters::ID, true), $createReturnRequestParametersGroup);
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
            'id' => $this->getRequestParam(Parameters::ID, true)
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
