<?php

namespace FWK\Controllers\Checkout\Internal;

use SDK\Core\Dtos\Element;
use SDK\Core\Dtos\Error;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Basket\Basket;
use SDK\Dtos\Common\Route;
use SDK\Enums\DeliveryType;
use FWK\Core\Controllers\SetUserController;
use FWK\Core\Controllers\Traits\AddVoucherTrait;
use FWK\Core\Controllers\Traits\DeleteRowsTrait;
use FWK\Core\Controllers\Traits\DeleteRowTrait;
use FWK\Core\Controllers\Traits\DeleteVoucherTrait;
use FWK\Core\Controllers\Traits\RecalculateBasketTrait;
use FWK\Core\Controllers\Traits\RedeemRewardPointsTrait;
use FWK\Core\Controllers\Traits\SaveforLaterRowTrait;
use FWK\Core\Controllers\Traits\SetDeliveryTrait;
use FWK\Core\Controllers\Traits\SetPaymentSystemTrait;
use FWK\Core\Resources\Loader;
use FWK\Enums\Services;
use FWK\Services\BasketService;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\Form\FormFactory;
use FWK\Core\Resources\RoutePaths;
use FWK\Core\Resources\Session;
use FWK\Core\Resources\Utils;
use FWK\Enums\Parameters;
use FWK\Enums\RouteType;
use FWK\Enums\SetUserTypeForms;
use SDK\Core\Dtos\IncidenceSaveForLaterListRowsCollection;

/**
 * This is the OSC internal recalculate controller.
 * This class extends BaseJsonController, see this class.
 *
 * @see BaseJsonController
 * 
 * @uses SetPaymentSystemTrait
 * @uses SetDeliveryTrait
 * @uses DeleteRowTrait
 * @uses AddVoucherTrait
 * @uses DeleteVoucherTrait
 * @uses RecalculateBasketTrait
 *
 * @package FWK\Controllers\Checkout\Internal
 */
class OscRecalculateController extends SetUserController {
    use SetPaymentSystemTrait, SetDeliveryTrait, DeleteRowTrait, DeleteRowsTrait, AddVoucherTrait, DeleteVoucherTrait, SaveforLaterRowTrait, RecalculateBasketTrait, RedeemRewardPointsTrait;

    private const USER_FORM = 'userForm';

    private ?BasketService $basketService = null;

    private array $data = [];

    private ?array $quantities = null;

    private ?Error $voucherError = null;

    private ?IncidenceSaveForLaterListRowsCollection $incidenceSaveForLaterListRows = null;

    private string $rewardPointsError = '';

    private string $orgCheckoutPath = '';

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->orgCheckoutPath = RoutePaths::getInstance()->getPath(RouteType::CHECKOUT);
        $this->basketService = Loader::service(Services::BASKET);
        $this->data = json_decode($this->getRequestParam(Parameters::DATA), true);
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FilterInputFactory::getDataParameters();
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

    /**
     * This method initialize applied parameters, runs previously to run preSendControllerBaseBatchData
     *
     */
    protected function initializeAppliedParameters(): void {
        if (isset($this->data[self::USER_FORM]) && count($this->data[self::USER_FORM]) !== 0) {
            parent::initializeAppliedParameters();
        }
    }

    /**
     * This method launches the adequate actions against the SDK (through the FWK services) and returns the response data. 
     *
     * @return Element
     */
    protected function getResponseData(): ?Element {
        $basket = Session::getInstance()->getBasket();
        if (isset($this->data[self::USER_FORM]) && count($this->data[self::USER_FORM]) !== 0) {
            if (
                isset($this->appliedParameters[SetUserTypeForms::USER]) &&
                array_key_exists(Parameters::EMAIL, $this->appliedParameters[SetUserTypeForms::USER])
            ) {
                $email = $this->appliedParameters[SetUserTypeForms::USER][Parameters::EMAIL];
                if (!parent::userExists($email) || strlen($email) === 0) {
                    parent::getResponseData();
                }
            } else {
                parent::getResponseData();
            }
        }
        $this->addVoucher();
        $this->deleteVoucher();
        $this->editQuantities();
        $this->deleteRow();
        $this->saveForLaterRow();
        $this->editBasketComment($basket);
        $this->setPaymentSystem();
        $this->setDelivery();
        $this->applyRewardPoints();
        return $basket;
    }

    private function checkStoreURL(string $oldStoreURL): void {
        $this->data['storeURL'] = [];
        $this->data['storeURL']['oldStoreURL'] = $oldStoreURL;
        $this->data['storeURL']['newStoreURL'] = Session::getInstance()->getGeneralSettings()->getStoreURL();
        $this->data['storeURL']['checkout'] = RoutePaths::getInstance()->getPath(RouteType::CHECKOUT);

        if ($oldStoreURL != Session::getInstance()->getGeneralSettings()->getStoreURL()) {
            $this->data['storeURL']['change'] = true;
            $this->data['storeURL']['path'] = Session::getInstance()->getGeneralSettings()->getStoreURL();
        }
    }

    private function applyRewardPoints(): void {
        if (isset($this->data['rewardPoints']) && $this->data['rewardPoints']['id'] > 0) {
            $result = $this->getRedeemRewardPointsResponseData(intval($this->data['rewardPoints']['id']), intval($this->data['rewardPoints']['value']));
            if (!empty($result->getError())) {
                $this->rewardPointsError =  Utils::getErrorLabelValue($result);
            }
        }
    }

    private function addVoucher(): void {
        $code = $this->getVoucherCode(FilterInputFactory::ADD);
        if (strlen($code) !== 0) {
            $result = $this->getAddVoucherResponseData($code);
            if (!empty($result->getError())) {
                $this->voucherError = $result->getError();
            }
        }
    }

    private function deleteVoucher(): void {
        $code = $this->getVoucherCode(FilterInputFactory::DELETE);
        if (strlen($code) !== 0) {
            $result = $this->getDeleteVoucherResponseData($code);
            if (!empty($result->getError())) {
                $this->voucherError = $result->getError();
            }
        }
    }

    private function editQuantities(): void {
        $quantities = $this->getQuantities();
        if (!empty($quantities)) {
            $this->getRecalculateBasketResponseData($quantities);
        }
    }

    private function deleteRow(): void {
        $hash = $this->getDeletedHash();
        if (!empty($hash)) {
            $this->getDeleteRowResponseData($hash);
        } else {
            $hashes = $this->getDeletedHashes();
            if (!empty($hashes)) {
                $this->getDeleteRowsResponseData($hashes);
            }
        }
    }

    private function saveForLaterRow(): void {
        $hash = $this->getSaveForLaterHash();
        if (strlen($hash) !== 0) {
            $result = $this->getSaveforLaterRowResponseData($hash);
            if (!empty($result->getError()) || !empty($result->getIncidences())) {
                $this->incidenceSaveForLaterListRows = $result;
            }
        }
    }

    private function editBasketComment(Basket $basket): void {
        $comment = $this->getComment();
        if ($comment !== $basket->getComment()) {
            $this->basketService->comment($comment);
        }
    }

    private function setPaymentSystem(): void {
        $paymentSystem = $this->getPaymentSystem();
        if (!empty($paymentSystem)) {
            $this->getPaymentSystemResponseData($paymentSystem);
        }
    }

    private function setDelivery(): void {
        $delivery = $this->getDelivery();
        if (!empty($delivery)) {
            if ($delivery[Parameters::TYPE] === DeliveryType::SHIPPING) {
                $getDelivery = $this->getDeliveryResponseData($delivery[Parameters::TYPE], $delivery[Parameters::DELIVERY_HASH], $delivery[Parameters::SHIPMENTS]);
            } elseif ($delivery[Parameters::TYPE] === DeliveryType::PICKING) {
                $getDelivery = $this->getDeliveryResponseData($delivery[Parameters::TYPE], $delivery[Parameters::DELIVERY_HASH], [], $delivery[Parameters::PROVIDER_PICKUP_POINT_HASH]);
            }
            $this->updatePickingCountry($getDelivery);
        }
    }

    private function getVoucherCode(string $mode): string {
        $code = '';
        if (isset($this->data['voucher'])) {
            if ($mode == FilterInputFactory::ADD && $this->data['voucher']['mode'] == FilterInputFactory::ADD) {
                $code = $this->data['voucher']['code'];
            } elseif ($mode == FilterInputFactory::DELETE && $this->data['voucher']['mode'] == FilterInputFactory::DELETE) {
                $code = $this->data['voucher']['code'];
            }
        }
        return $code;
    }

    private function getQuantities(): array {
        if (is_null($this->quantities)) {
            $this->quantities = [];
            $basketRows = [];
            foreach ($this->getSession()->getBasket()->getItems() as $row) {
                $basketRows[$row->getHash()] = $row->getQuantity();
            }
            foreach ($this->getSession()->getBasketGridProducts() as $gridRow) {
                foreach ($gridRow->getCombinations() as $gridRowCombinationId => $gridRowCombination) {
                    $basketRows['Grid' . $gridRowCombinationId] = $gridRowCombination->quantity;
                }
            }

            foreach ($this->data as $k => $v) {
                if (isset($v[Parameters::TYPE]) && isset($v[Parameters::QUANTITY]) && $basketRows[$k] != $v[Parameters::QUANTITY]) {
                    $this->quantities[$k] = $v;
                }
            }
        }
        return $this->quantities;
    }

    private function getDeletedHash(): string {
        return $this->data['deleteRow'] ?? '';
    }

    private function getDeletedHashes(): array {
        return $this->data['deleteRows'] ?? [];
    }

    private function getSaveForLaterHash(): string {
        return $this->data['saveForLater'] ?? '';
    }

    private function getComment(): string {
        return $this->data[Parameters::COMMENT] ?? '';
    }

    private function getPaymentSystem(): array {
        $paymentSystem = $this->data['paymentSystem'] ?? [];
        if (is_array($paymentSystem)) {
            return $paymentSystem;
        } else {
            return [];
        }
    }

    private function getDelivery(): array {
        return $this->data['shippingType'] ?? [];
    }

    protected function getParsedParamsData(): array {
        // remove password to allow the form to fill in pieces into the session
        unset($this->data[self::USER_FORM]['createAccount']);
        return self::parseData($this->data[self::USER_FORM], Utils::isSimulatedUser($this->getSession()));
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
        return RoutePaths::getPath(RouteType::CHECKOUT);
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
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
    }

    /**
     * This method parses the given Element and returns it.
     * 
     * @param Element $response
     * 
     * @return \SDK\Core\Dtos\Element
     */
    protected function parseResponseData(Element $basket) {
        $responseData = [
            'basket' => [
                'modified' => false,
                'items' => []
            ],
            'delete' => [
                'deleted' => false,
                'hash' => '',
                'quantity' => 0
            ],
            'saveForLater' => [
                'saved' => false,
                'hash' => '',
                'quantity' => 0,
                'incidences' => null,
            ],
            'comment' => [
                'commented' => false,
                'comment' => ''
            ],
            'paymentSystem' => [
                'changed' => false,
                'id' => 0
            ],
            'delivery' => [
                'changed' => false,
                'deliveryHash' => '',
                'shippings' => [],
            ],
            'voucher' => [
                'added' => false,
                'deleted' => false,
                'code' => ''
            ],
            'rewardPoints' => [
                'redeemed' => false,
                'id' => 0,
                'value' => 0,
            ],
            'user' => [
                'modified' => false
            ],
            'totalItems' => $basket->getTotals()->getTotalQuantity(),
            'folcsVersion' => Utils::getFolcsVersion(false),
        ];
        $quantities = $this->getQuantities();
        if (!empty($quantities)) {
            $responseData['basket']['modified'] = true;
            foreach ($quantities as $hash => $basketRow) {
                $responseData['basket']['items'][] = [
                    'quantity' => $basketRow['quantity'],
                    'hash' => $hash
                ];
            }
            $responseData['totalItems'] = array_reduce($responseData['basket']['items'], fn($acc, $item) => $acc + $item['quantity'], 0);
        }
        $hash = $this->getDeletedHash();
        if (strlen($hash) !== 0) {
            $deletedQuantity = $basket->getItem($hash)->getQuantity();
            $responseData['delete'] = [
                'deleted' => true,
                'hash' => $hash,
                'quantity' => $deletedQuantity
            ];
            $responseData['totalItems'] -= $deletedQuantity;
        }
        $hash = $this->getSaveForLaterHash();
        if (strlen($hash) !== 0) {
            if (!is_null($this->incidenceSaveForLaterListRows)) {
                $responseData['saveForLater'] = [
                    'incidences' => $this->incidenceSaveForLaterListRows,
                    'error' => $this->incidenceSaveForLaterListRows->getError(),
                ];
            } else {
                $savedQuantity = $basket->getItem($hash)->getQuantity();
                $responseData['saveForLater'] = [
                    'saved' => true,
                    'hash' => $hash,
                    'quantity' => $savedQuantity
                ];
                $responseData['totalItems'] -= $savedQuantity;
            }
        }
        $comment = $this->getComment();
        if ($comment !== $basket->getComment()) {
            $responseData['comment'] = [
                'commented' => true,
                'comment' => $comment
            ];
        }
        $paymentSystem = $this->getPaymentSystem();
        if (!empty($paymentSystem)) {
            $responseData['paymentSystem'] = [
                'changed' => true,
                'id' => $paymentSystem['id']
            ];
        }
        $delivery = $this->getDelivery();
        if (!empty($delivery)) {
            $responseData['delivery'] = array_merge($delivery, ['changed' => true]);
        }
        $code = $this->getVoucherCode(FilterInputFactory::ADD);
        if (strlen($code) !== 0) {
            $responseData['voucher'] = [
                'added' => true,
                'code' => $code
            ] + $responseData['voucher'];
        }
        $code = $this->getVoucherCode(FilterInputFactory::DELETE);
        if (strlen($code) !== 0) {
            $responseData['voucher'] = [
                'deleted' => true,
                'code' => $code
            ] + $responseData['voucher'];
        }
        if (isset($this->data['rewardPoints'])) {
            $responseData['rewardPoints'] = [
                'redeemed' => true,
                'id' => $this->data['rewardPoints']['id'],
                'value' => $this->data['rewardPoints']['value'],
                'error' => $this->rewardPointsError
            ];
        }
        if (!is_null($this->voucherError)) {
            $responseData['voucher']['error'] = $this->voucherError->getCode();
        }
        if (isset($this->data[self::USER_FORM]) && count($this->data[self::USER_FORM]) !== 0) {
            $responseData['user']['modified'] = true;
            if (isset($this->appliedParameters[SetUserTypeForms::USER]) && array_key_exists(Parameters::EMAIL, $this->appliedParameters[SetUserTypeForms::USER])) {
                $responseData['user']['userExists'] = parent::userExists($this->appliedParameters[SetUserTypeForms::USER][Parameters::EMAIL]);
            }
        }
        if (isset($this->data[self::USER_FORM]) && count($this->data[self::USER_FORM]) !== 0) {
            $validatedAddress = parent::validateUserAddress();
            $responseData['user']['validAddress']['isValid'] = $validatedAddress->isValid();
            $responseData['user']['validAddress']['messages'] = $validatedAddress->getMessages();
        }
        $responseData['checkoutPath'] = [
            'original' => $this->orgCheckoutPath,
            'final' => RoutePaths::getInstance()->getPath(RouteType::CHECKOUT)
        ];
        return $responseData;
    }
}
