<?php

namespace FWK\Controllers\Checkout;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\Resources\Loader;
use FWK\Enums\Services;
use FWK\Services\OrderService;
use SDK\Core\Enums\MethodType;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\PayResponse;
use SDK\Enums\PaymentType;
use SDK\Dtos\Documents\Transactions\Purchases\Order;
use SDK\Dtos\Common\Route;
use FWK\Core\Resources\Response;
use FWK\Core\Resources\RoutePaths;
use FWK\Core\Resources\Session;
use FWK\Core\Resources\Utils;
use FWK\Core\Theme\Dtos\CommerceLockedStock;
use FWK\Enums\Parameters;
use FWK\Enums\RouteType;
use SDK\Enums\AddressType;
use FWK\Services\BasketService;

/**
 * This is the checkout end order controller.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @see BaseHtmlController
 *
 * @package FWK\Controllers\Checkout
 */
class EndOrderController extends BaseHtmlController {

    public const PLUGIN_VALIDATION = 'pluginValidation';

    private ?OrderService $orderService = null;

    private ?PayResponse $payResponse = null;

    private ?BasketService $basketService = null;

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);

        $this->basketService = Loader::service(Services::BASKET);

        $lockedStock = self::getTheme()->getConfiguration()->getCommerce()->getLockedStock();
        $this->basketService->extendLockedStockTimer(CommerceLockedStock::EXTEND_BY_PAYMENT_GATEWAY_VISITED, $lockedStock);

        $user = Session::getInstance()->getBasket()->getBasketUser()->getUser();
        if (!$this->getTheme()->getConfiguration()->getCommerce()->isAllowDifferentCountriesOnBillingAndShippingAddress() && $user->getUseShippingAddress()) {
            $billingAddress = $user->getAddress($user->getSelectedBillingAddressId(), AddressType::BILLING);
            $billingCountryCode = $billingAddress->getLocation()->getGeographicalZone()->getCountryCode();
            $shippingAddress = $user->getAddress($user->getSelectedShippingAddressId(), AddressType::SHIPPING);
            $shippingCountryCode = $shippingAddress->getLocation()->getGeographicalZone()->getCountryCode();
            if ($billingCountryCode != $shippingCountryCode) {
                $redirectParams = '?' . Parameters::CODE . '=ALLOW_DIFFERENT_COUNTRIES_ON_BILLING_AND_SHIPPING_ADDRESS_COUNTRY_ERROR';
                Response::redirect(RoutePaths::getPath(RouteType::CHECKOUT_DENIED_ORDER) . $redirectParams);
            }
        }

        $this->orderService = Loader::service(Services::ORDER);
        $order = $this->orderService->createOrder();
        $fail = true;
        $payResponse = null;
        if (!is_null($order)) {
            $orderId = $order->getId();
            if ($orderId !== 0) {
                $payResponse = $this->orderService->redirectToPayment($orderId);
                if (!is_null($payResponse) && is_null($payResponse->getError())) {
                    $payResponseValidatorClass = 'Plugins\\' . Utils::getCamelFromSnake($payResponse->getPluginModule(), '.') . '\\Core\\Resources\\PayResponseValidator';
                    if (Utils::isExpressCheckout($this->getSession()->getBasket()) && class_exists($payResponseValidatorClass)) {
                        $payResponse = (new ($payResponseValidatorClass))->verifyPayResponse($orderId, $payResponse, self::PLUGIN_VALIDATION);
                    } elseif ($this->isDirectPayment($payResponse)) {
                        $payResponse = $this->getNoPayResponse($orderId, $payResponse);
                    }
                    if (is_null($payResponse->getError())) {
                        $this->payResponse = $payResponse;
                        $fail = false; // success
                    }
                }
            }
        }
        if ($fail) {
            $this->onFailureAction($order, $payResponse);
            Session::getInstance()->setBasket($this->basketService->getBasket());
        }
    }

    /**
     * This method sets the redirect page in error's case
     *
     * @param Order $order
     * @param null|PayResponse $order
     */
    protected function onFailureAction(Order $order, ?PayResponse $payResponse): void {
        if (!is_null($order->getError()) || (!is_null($payResponse) && !is_null($payResponse->getError()))) {
            $error = !is_null($order->getError()) ? $order->getError() : $payResponse->getError();
            $redirectParams = '?' . Parameters::CODE . '=' . urlencode($error->getCode());
            if ($error->getCode() == 'A01000-INVALID_REQUEST_BODY') {
                $fieldsArray = [];
                foreach ($error->getFields() as $field) {
                    array_push($fieldsArray, ['name' => $field->getName(), 'type' => $field->getType(), 'additionalInformation' => $field->getAdditionalInformation()]);
                }
                $redirectParams .=  '&' . Parameters::FIELDS . '=' . urlencode(json_encode($fieldsArray));
            }
            Response::redirect(RoutePaths::getPath(RouteType::CHECKOUT_DENIED_ORDER) . $redirectParams);
        }
    }

    /**
     * This method returns if type of pay response is no pay or offline
     *
     * @param PayResponse $payResponse
     * @return bool
     */
    private function isDirectPayment(PayResponse $payResponse): bool {
        return ($payResponse->getType() === PaymentType::NO_PAY || $payResponse->getType() === PaymentType::OFFLINE);
    }

    /**
     * This method returns default endorder form data
     *
     * @param int $orderId
     * @return array
     */
    private function getNoPayResponse(int $orderId, PayResponse $payResponse): PayResponse {
        $token = isset($payResponse->getData()[Parameters::TOKEN]) ? $payResponse->getData()[Parameters::TOKEN] : '';
        return
            new PayResponse(
                array_replace_recursive(
                    $payResponse->toArray(),
                    [
                        'type' => $payResponse->getType(),
                        'pluginId' => $payResponse->getPluginId(),
                        'pluginModule' => $payResponse->getPluginModule(),
                        'data' => [
                            'url' => RoutePaths::getPath(RouteType::CHECKOUT_CONFIRM_ORDER),
                            'method' => MethodType::POST,
                            'params' => [
                                'orderId' => $orderId,
                                'transactionId' => $payResponse->getTransactionId(),
                                'token' => $token,
                            ]
                        ]
                    ]
                )
            );
    }

    /**
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     *
     */
    protected function setControllerBaseData(): void {
        $this->setDataValue(self::CONTROLLER_ITEM, $this->payResponse);
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
