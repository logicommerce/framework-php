<?php

namespace FWK\Controllers\Checkout;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\Exceptions\CommerceException;
use FWK\Enums\Services;
use FWK\Core\Resources\Loader;
use SDK\Core\Resources\BatchRequests;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Services\OrderService;
use SDK\Dtos\Common\Route;
use FWK\Core\Resources\Session;
use FWK\Core\Resources\Response;
use FWK\Enums\Parameters;
use FWK\Core\Resources\RoutePaths;
use FWK\Dtos\Documents\Document;
use FWK\Enums\RouteType;
use SDK\Enums\PaymentType;
use SDK\Enums\PaymentValidateStatus;
use SDK\Enums\PluginConnectorType;
use FWK\Services\PluginService;
use SDK\Core\Dtos\ElementCollection;
use SDK\Services\Parameters\Groups\PluginConnectorTypeParametersGroup;

/**
 * This is the checkout confirm order controller.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @see BaseHtmlController
 *
 * @package FWK\Controllers\Checkout
 */
class ConfirmOrderController extends BaseHtmlController {

    private ?OrderService $orderService = null;

    private const ORDER = 'order';

    private int $orderId = 0;

    private int $transactionId = 0;

    private string $token = '';

    private string $type = '';

    private ?PluginService $pluginService = null;

    private ?ElementCollection $confirmOrderPlugins = null;

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FilterInputFactory::getConfirmOrderParameters();
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
        return FilterInputHandler::PARAMS_FROM_POST;
    }

    private function init(): void {
        if ($this->getRequestParam(Parameters::ORDER_ID) !== null) {
            $this->orderId = intval($this->getRequestParam(Parameters::ORDER_ID));
        }
        if ($this->getRequestParam(Parameters::TRANSACTION_ID) !== null) {
            $this->transactionId = intval($this->getRequestParam(Parameters::TRANSACTION_ID));
        }
        if ($this->getRequestParam(Parameters::TOKEN) !== null) {
            $this->token = $this->getRequestParam(Parameters::TOKEN);
        }
        if (!strlen($this->token) && $this->orderId !== 0) {
            $order = Session::getInstance()->getOrder($this->orderId);
            if (!is_null($order)) {
                $this->token = $order->getToken();
            }
        }
        if ($this->getRequestParam(Parameters::TYPE) !== null) {
            $this->type = $this->getRequestParam(Parameters::TYPE);
        }
        // ... other confirmOrder form parameters
    }

    /**
     * Constructor method.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $basket = null;
        $this->orderService = Loader::service(Services::ORDER);
        $this->init();

        if (!strlen($this->token)) {
            $validatePaymentStatus = PaymentValidateStatus::KO;
            if ($this->type != PaymentType::NO_PAY) {
                $paymentValidationResponse = $this->orderService->validatePayment();
                $validatePaymentStatus = $paymentValidationResponse->getStatus();
                if ($validatePaymentStatus == PaymentValidateStatus::ACCEPTED) {
                    $count = 0;
                    do {
                        sleep(1);
                        $paymentValidationResponse = $this->orderService->validatePayment();
                        $validatePaymentStatus = $paymentValidationResponse->getStatus();
                        $count++;
                        if ($count >= 10) {
                            throw new CommerceException('Too many validate payment sended, try ' . $count . ' times', CommerceException::CONFIRM_ORDER_VALIDATE_PAYMENT);
                        }
                    } while ($validatePaymentStatus == PaymentValidateStatus::ACCEPTED);
                }
                if (!is_null($paymentValidationResponse->getError())) {
                    throw new CommerceException($paymentValidationResponse->getError()->getMessage(), CommerceException::CONFIRM_ORDER_VALIDATE_PAYMENT, null, $paymentValidationResponse->getError());
                }
            }
            if ($validatePaymentStatus == PaymentValidateStatus::OK || $validatePaymentStatus == PaymentValidateStatus::VALIDATED || $validatePaymentStatus == PaymentValidateStatus::ACCEPTED) {
                Session::getInstance()->addOrder($paymentValidationResponse);
                $this->orderId = $paymentValidationResponse->getId();
                $this->token = $paymentValidationResponse->getToken();
            }
            if ($validatePaymentStatus == PaymentValidateStatus::KO) {
                $basket = Loader::service(Services::BASKET)->getBasket();
                if (!empty($basket->getItems())) {
                    Response::redirect(RoutePaths::getPath(RouteType::CHECKOUT_DENIED_ORDER));
                }
            }
        }
        if (is_null($basket)) {
            $basket = Loader::service(Services::BASKET)->getBasket();
        }
        if ($this->orderId === 0) {
            Response::redirect(RoutePaths::getPath(RouteType::HOME));
        }
    }

    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
        if (strlen($this->token) > 0) {
            $this->orderService->addGetOrder($requests, self::ORDER, $this->orderId, $this->token);
        } else {
            $this->orderService->addGetOrder($requests, self::ORDER, $this->orderId);
        }

        $this->pluginService = Loader::service(Services::PLUGIN);
        $params = new PluginConnectorTypeParametersGroup();
        $params->setType(PluginConnectorType::CONFIRM_ORDER);
        $params->setNavigationHash($this->getSession()->getNavigationHash());
        $this->confirmOrderPlugins = $this->pluginService->getPlugins($params);
        foreach ($this->confirmOrderPlugins as $confirmOrderPlugin) {
            $this->pluginService->addGetPluginProperties($requests, Services::PLUGIN . '_' . PluginConnectorType::CONFIRM_ORDER . '_' . $confirmOrderPlugin->getId(), $confirmOrderPlugin->getId());
        }
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
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     *
     */
    protected function setControllerBaseData(): void {
        $this->setDataValue('token', $this->token);
        $order = $this->getControllerData(self::ORDER);
        $order = Document::fillFromParent($order);
        $this->setDataValue(self::CONTROLLER_ITEM, $order);
        $this->deleteControllerData(self::ORDER);

        $confirmOrderPlugins = [];
        foreach ($this->confirmOrderPlugins as $confirmOrderPlugin) {
            $pluginProperty = $this->getControllerData(Services::PLUGIN . '_' . PluginConnectorType::CONFIRM_ORDER . '_' . $confirmOrderPlugin->getId());
            $confirmOrderPlugins[$pluginProperty->getPluginModule()] = $pluginProperty;
        }
        $this->setDataValue('confirmOrderPlugins', $confirmOrderPlugins);
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
}
