<?php

namespace FWK\Controllers\Checkout\Internal;

use SDK\Core\Resources\BatchRequests;
use FWK\Core\Controllers\BaseJsonController;
use FWK\Core\Resources\Language;
use FWK\Core\Resources\Loader;
use FWK\Core\Resources\Session;
use FWK\Enums\LanguageLabels;
use FWK\Enums\Services;
use SDK\Core\Dtos\Element;
use SDK\Dtos\Common\Route;
use SDK\Services\BasketService;
/**
 * This is the ExpressCheckoutAccountController class.
 * This class extends BaseJsonController, see this class.
 *
 * @see BaseJsonController
 *
 * @package FWK\Controllers\Checkout\Internal
 */
class ExpressCheckoutAccountController extends BaseJsonController {

    private ?BasketService $basketService = null;

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->basketService = Loader::service(Services::BASKET);
    }

    /**
     * This private method sends the batch requests of the controller to the SDK to obtain the data and returns the batch result
     *
     * @return array with the result of the batch request
     */
    protected function setBatchData(BatchRequests $request): void {
    }

    /**
     * This method runs after the batch requests (defined in the setBatchData methods) are resolved,
     * so here you can work with the response of the batch requests and calculate and set more needed data.
     *
     * @param array $additionalData
     *              Set additiona data to the controller data
     * 
     * @return void
     */
    protected function setData(array $additionalData = []): void {
    }

    /**
     * This method launches the adequate actions against the SDK (through the FWK services) and returns the response data. 
     *
     * @return Element
     */
    protected function getResponseData(): ?Element {
        $validate = $this->basketService->validateExpressCheckout();
        if ($validate->getError() != null && $validate->getError()->getCode() == 'A01000-USER_IS_LOGGED_IN') {
            $warning = [
                'code' => $validate->getError()->getCode(),
                'message' => Language::getInstance()->getLabelValue(LanguageLabels::ERROR_CODE_USER_IS_ALREADY_LOGGED_IN)
            ];
            Session::getInstance()->addWarning($warning);
        }
        return $validate;
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
