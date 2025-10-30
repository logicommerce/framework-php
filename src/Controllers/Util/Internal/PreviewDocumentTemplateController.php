<?php

namespace FWK\Controllers\Util\Internal;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\Exceptions\CommerceException;
use SDK\Core\Resources\BatchRequests;
use FWK\Twig\TwigDocumentsLoader;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\Resources\Loader;
use FWK\Core\Resources\Router;
use FWK\Core\Theme\Theme;
use FWK\Enums\Services;
use FWK\Enums\Parameters;
use SDK\Core\Enums\CustomTagControlType;
use SDK\Core\Resources\Environment;
use SDK\Dtos\Common\Route;
use SDK\Dtos\Documents\Rich\RichRMA;
use SDK\Dtos\Documents\Rich\RichReturn;
use SDK\Dtos\Documents\Rich\RichCreditNote;
use SDK\Dtos\Documents\Rich\RichDeliveryNote;
use SDK\Dtos\Documents\Rich\RichInvoice;
use SDK\Dtos\Documents\Rich\RichOrder;
use SDK\Enums\BackorderMode;
use SDK\Enums\BasketRowType;
use SDK\Enums\DeliveryType;
use SDK\Enums\DocumentCurrencyMode;
use SDK\Enums\DocumentShipmentStatusType;
use SDK\Enums\DocumentType;
use SDK\Enums\Gender;
use SDK\Enums\OptionType;
use SDK\Enums\PaymentType;
use SDK\Enums\PrevisionType;
use SDK\Enums\RewardPointsRuleType;
use SDK\Enums\RewardPointsRuleValueMode;
use SDK\Enums\ShippingCalculation;
use SDK\Enums\UserType;

/**
 * This is the PreviewDocumentTemplateController class.
 * The purpose of this class is to check the basic operation of the controller mechanism.<br>
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @see PreviewDocumentTemplateController::TEMPLATE_CORRECTIVE_INVOICE
 * @see PreviewDocumentTemplateController::TEMPLATE_DELIVERY_NOTE
 * @see PreviewDocumentTemplateController::TEMPLATE_INVOICE
 * @see PreviewDocumentTemplateController::TEMPLATE_ORDER
 * @see PreviewDocumentTemplateController::TEMPLATE_RETURN
 * @see PreviewDocumentTemplateController::TEMPLATE_RMA
 *
 * @see BaseHtmlController
 *
 * @package FWK\Controllers\Util\Internal
 */
class PreviewDocumentTemplateController extends BaseHtmlController {

    public const TEMPLATE_CORRECTIVE_INVOICE = 'correctiveInvoice';

    public const TEMPLATE_DELIVERY_NOTE = 'deliveryNote';

    public const TEMPLATE_INVOICE = 'invoice';

    public const TEMPLATE_ORDER = 'order';

    public const TEMPLATE_RETURN = 'return';

    public const TEMPLATE_RMA = 'rma';

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        if (!Environment::get('DEVEL')) {
            (new Router())->notFound();
            exit;
        } else {
            parent::__construct($route);
        }
    }

    protected function alterTheme(): void {
        $theme = Theme::getInstance();
        $theme->setName(INTERNAL_THEME);
        $theme->setVersion('');
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FilterInputFactory::getPreviewDocumentParameters();
    }

    /**
     * This private method sends the batch requests of the controller to the SDK to obtain the data and returns the batch result
     *
     * @return array with the result of the batch request
     */
    protected final function setControllerBaseBatchData(BatchRequests $requests): void {
    }

    /**
     * This method is the one in charge of defining all the data batch requests that
     * are needed for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     * @return void
     */
    protected function setBatchData(BatchRequests $requests): void {
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
     *
     * @see \FWK\Core\Controllers\Controller::render()
     */
    protected function render(String $content = null, String $layout = null, String $format = 'html'): string {
        $content = $this->getRequestParam(Parameters::TEMPLATE, true);
        $document = null;
        $orderService = Loader::service(Services::ORDER);
        if (!is_null($this->getRequestParam(Parameters::ID))) {
            switch ($content) {
                case self::TEMPLATE_CORRECTIVE_INVOICE:
                    $document = $orderService->getRichCorrectiveInvoice($this->getRequestParam(Parameters::ID));
                    break;
                case self::TEMPLATE_DELIVERY_NOTE:
                    $document = $orderService->getRichDeliveryNote($this->getRequestParam(Parameters::ID));
                    break;
                case self::TEMPLATE_INVOICE:
                    $document = $orderService->getRichInvoice($this->getRequestParam(Parameters::ID));
                    break;
                case self::TEMPLATE_ORDER:
                    $document = $orderService->getRichOrder($this->getRequestParam(Parameters::ID));
                    break;
                case self::TEMPLATE_RETURN:
                    $document = $orderService->getRichReturn($this->getRequestParam(Parameters::ID));
                    break;
                case self::TEMPLATE_RMA:
                    $document = $orderService->getRichRMA($this->getRequestParam(Parameters::ID));
                    break;
            }
            if ($document->getError() !== null) {
                throw new CommerceException(self::class . '. ' . $document->getError()->getMessage() . '. ' . $document->getError()->getMessage());
            }
        } else {
            switch ($content) {
                case self::TEMPLATE_RMA:
                    $document = new RichRMA($this::getMockedData());
                    break;
                case self::TEMPLATE_RETURN:
                    $document = new RichReturn($this::getMockedData());
                    break;
                case self::TEMPLATE_CORRECTIVE_INVOICE:
                    $document = new RichCreditNote($this::getMockedData());
                    break;
                case self::TEMPLATE_DELIVERY_NOTE:
                    $document = new RichDeliveryNote($this::getMockedData());
                    break;
                case self::TEMPLATE_INVOICE:
                    $document = new RichInvoice($this::getMockedData());
                    break;
                case self::TEMPLATE_ORDER:
                    $document = new RichOrder($this::getMockedData());
                    break;
            }
        }
        $this->twig = new TwigDocumentsLoader();
        $this->twig->load(['document' => $document, 'sales' => $document]);
        return $this->twig->render($content . '.twig');
    }

    protected static function getMockedData(): array {
        return [
            'getpId' => 'getpId_' . rand(1, 999),
            'documentNumber' => 'documentNumber_' . rand(1, 999),
            'date' => self::getRichDateTime(),
            'deliveryDate' => self::getRichDateTime(),
            'paid' => rand(1, 100) > 50,
            'paymentDate' => self::getRichDateTime(),
            'comment' => 'comment_' . rand(1, 999),
            'reverseChargeVat' => rand(1, 100) > 50,
            'status' => 'status_' . rand(1, 999),
            'substatus' => 'substatus_' . rand(1, 999),
            'reverse' => rand(1, 100) > 50,
            'languageCode' => 'languageCode_' . rand(1, 999),
            'customTags' => [
                self::getRichDocumentCustomTag(),
                self::getRichDocumentCustomTag(),
            ],
            'items' => [
                self::getRichDocumentItem(),
                self::getRichDocumentItem(),
            ],
            'delivery' => self::getRichDocumentDelivery(),
            'additionalInformation' => [
                self::getRichDocumentAdditionalInformation(),
                self::getRichDocumentAdditionalInformation(),
            ],
            'currencies' => [
                self::getRichDocumentCurrency(),
                self::getRichDocumentCurrency(),
            ],
            'headquarter' => self::getRichDocumentHeadquarter(),
            'information' => self::getRichDocumentInformation(),
            'paymentSystem' => self::getRichDocumentPaymentSystem(),
            'taxes' => [
                self::getRichDocumentTax(),
                self::getRichDocumentTax(),
            ],
            'totals' => self::getRichDocumentTotal(),
            'user' => self::getRichDocumentUser(),
            'vouchers' => [
                self::getRichDocumentVoucher(),
                self::getRichDocumentVoucher(),
            ],
            'discounts' => [
                self::getRichDocumentDiscount(),
                self::getRichDocumentDiscount(),
            ],
            'additionalItems' => [
                self::getRichDocumentAdditionalItem(),
                self::getRichDocumentAdditionalItem(),
            ],
            'documentParents' => [
                self::getDocumentParents(),
                self::getDocumentParents(),
            ],
            'rewardPoints' => [
                self::getRewardPoints(),
                self::getRewardPoints(),
            ]
        ];
    }

    private static function getRichDateTime() {
        return [
            'date' => 'date_' . rand(1, 999),
            'time' => 'time_' . rand(1, 999),
        ];
    }

    private static function getRichDocumentCustomTag() {
        return [
            'name' => 'name_' . rand(1, 999),
            'value' => 'value_' . rand(1, 999),
            'position' => rand(1, 999),
            'controlType' => CustomTagControlType::SHORT_TEXT
        ];
    }

    private static function getRichDocumentDiscount() {
        return [
            'name' => 'name_' . rand(1, 999),
            'description' => 'description_' . rand(1, 999),
            'value' => 'value_' . rand(1, 999),
            'valueWithTaxes' => 'valueWithTaxes_' . rand(1, 999),
        ];
    }

    private static function getRichDocumentElementTax() {
        return [
            'base' => 'base_' . rand(1, 999),
            'taxValue' => 'taxValue_' . rand(1, 999),
            'applyTax' => rand(1, 100) > 50,
            'applyRE' => rand(1, 100) > 50,
            'tax' => self::getDocumentItemTax(),
        ];
    }

    private static function getDocumentItemTax() {
        return [
            'taxId' => rand(1, 999),
            'definitionName' => 'definitionName_' . rand(1, 999),
            'taxRate' => rand(1, 999),
            'reRate' => rand(1, 999),
            'priority' => rand(1, 999),
        ];
    }

    private static function getRichDocumentItem() {
        return [
            'hash' => 'hash_' . rand(1, 999),
            'name' => 'name_' . rand(1, 999),
            'link' => 'link_' . rand(1, 999),
            'quantity' => rand(1, 999),
            'prices' => self::getRichDocumentItemPrices(),
            'weight' => rand(1, 999),
            'discounts' => [
                self::getRichDocumentDiscount(),
                self::getRichDocumentDiscount(),
            ],
            'taxes' => [
                self::getRichDocumentElementTax(),
                self::getRichDocumentElementTax(),
            ],
            'options' => [
                self::getRichDocumentItemOption(),
                self::getRichDocumentItemOption(),
            ],
            'stocks' => [
                self::getRichDocumentItemStock(),
                self::getRichDocumentItemStock(),
            ],
            'customTags' => [
                self::getRichDocumentCustomTag(),
                self::getRichDocumentCustomTag(),
            ],
            'linkedParentId' => rand(1, 999),
            'image' => 'image_' . rand(1, 999),
            'stockManagement' => rand(1, 100) > 50,
            'reverseChargeVat' => rand(1, 100) > 50,
            'codes' => self::getProductCodes(),
            'noReturn' => rand(1, 100) > 50,
            'backOrder' => rand(1, 100) > 50 ? BackorderMode::WITH_PREVISION : BackorderMode::WITHOUT_PREVISION,
            'onRequest' => rand(1, 100) > 50,
            'onRequestDays' => rand(1, 100),
            'type' => rand(1, 100) > 50 ? BasketRowType::PRODUCT : BasketRowType::GIFT,
            'reserve' => rand(1, 100) > 50,
            'itemId' => rand(1, 100),
            'rmaReason' => [
                'description' => 'description_' . rand(1, 999),
                'comment' => 'comment_' . rand(1, 999),
                'rmaReasonPId' => 'rmaReasonPId_' . rand(1, 999)
            ]
        ];
    }

    private static function getRichDocumentItemPrices() {
        return [
            'productPrice' => 'productPrice_' . rand(1, 999),
            'productPriceWithTaxes' => 'productPriceWithTaxes_' . rand(1, 999),
            'optionsPrice' => 'optionsPrice_' . rand(1, 999),
            'optionsPriceWithTaxes' => 'optionsPriceWithTaxes_' . rand(1, 999),
            'previousPrice' => 'previousPrice_' . rand(1, 999),
            'previousPriceWithTaxes' => 'previousPriceWithTaxes_' . rand(1, 999),
            'price' => 'price_' . rand(1, 999),
            'priceWithTaxes' => 'priceWithTaxes_' . rand(1, 999),
            'totalTaxesValue' => 'totalTaxesValue_' . rand(1, 999),
            'totalDiscountsValue' => 'totalDiscountsValue_' . rand(1, 999),
            'total' => 'total_' . rand(1, 999),
            'totalWithDiscounts' => 'totalWithDiscounts_' . rand(1, 999),
            'totalWithDiscountsWithTaxes' => 'totalWithDiscountsWithTaxes_' . rand(1, 999),
        ];
    }

    private static function getRichDocumentItemOption() {
        return [
            'values' => [
                self::getRichDocumentItemOptionValue(),
                self::getRichDocumentItemOptionValue(),
            ],
            'sku' => 'sku_' . rand(1, 999),
            'name' => 'name_' . rand(1, 999),
            'prompt' => 'prompt_' . rand(1, 999),
            'value' => 'value_' . rand(1, 999),
            'price' => 'price_' . rand(1, 999),
            'priceWithTaxes' => 'priceWithTaxes_' . rand(1, 999),
            'weight' => rand(1, 999),
            'uniquePrice' => rand(1, 100) > 50,
            'valueType' => rand(1, 100) > 50 ? OptionType::SHORT_TEXT : OptionType::LONG_TEXT,
            'previousPrice' => 'previousPrice_' . rand(1, 999),
            'previousPriceWithTaxes' => 'previousPriceWithTaxes_' . rand(1, 999),
            'optionPId' => 'optionPId_' . rand(1, 999),
            'combinable' => rand(1, 100) > 50,
        ];
    }

    private static function getRichDocumentItemStock() {
        return [
            'warehouseName' => 'warehouseName_' . rand(1, 999),
            'warehouseGroupName' => 'warehouseGroupName_' . rand(1, 999),
            'quantity' => rand(1, 999),
            'incomingDate' => 'incomingDate_' . rand(1, 999),
            'offsetDays' => rand(1, 999),
            'priority' => rand(1, 999),
            'hash' => 'hash_' . rand(1, 999),
            'previsionType' => rand(1, 100) > 50 ? PrevisionType::AVAILABLE : PrevisionType::RESERVE,
        ];
    }

    private static function getProductCodes() {
        return [
            'sku' => 'sku_' . rand(1, 100),
            'jan' => 'jan_' . rand(1, 100),
            'isbn' => 'isbn_' . rand(1, 100),
            'ean' => 'ean_' . rand(1, 100),
            'upc' => 'upc_' . rand(1, 100),
            'manufacturerSku' => 'manufacturerSku_' . rand(1, 100),
        ];
    }

    private static function getRichDocumentItemOptionValue() {
        return [
            'weight' => rand(1, 999),
            'price' => 'price_' . rand(1, 999),
            'priceWithTaxes' => 'priceWithTaxes_' . rand(1, 999),
            'previousPrice' => 'previousPrice_' . rand(1, 999),
            'previousPriceWithTaxes' => 'previousPriceWithTaxes_' . rand(1, 999),
            'value' => 'value_' . rand(1, 999),
            'optionValuePId' => 'optionValuePId_' . rand(1, 999),
            'noReturn' => rand(1, 100) > 50,
        ];
    }

    private static function getRichDocumentDelivery() {
        return [
            'type' => DeliveryType::SHIPPING,
            'shipments' => [
                self::getRichDocumentShipment(),
                self::getRichDocumentShipment(),
            ]
        ];
    }

    private static function getRichDocumentShipment() {
        return [
            'getpId' => 'getpId_' . rand(1, 999),
            'status' => rand(1, 100) > 50 ? DocumentShipmentStatusType::PENDING : DocumentShipmentStatusType::PROCESSING,
            'substatus' => 'substatus_' . rand(1, 999),
            'originWarehouseGroupName' => 'originWarehouseGroupName_' . rand(1, 999),
            'physicalLocationName' => 'physicalLocationName_' . rand(1, 999),
            'incomingDate' => 'incomingDate_' . rand(1, 999),
            'items' => [
                self::getRichDocumentShipmentItem(),
                self::getRichDocumentShipmentItem(),
            ],
            'shipping' => self::getRichDocumentShipping(),
            'trackingNumber' => 'trackingNumber_' . rand(1, 999),
            'trackingUrl' => 'trackingUrl_' . rand(1, 999)
        ];
    }

    private static function getRichDocumentDeliveryPhysicalLocation() {
        return [
            'physicalLocationPId' => 'physicalLocationPId' . rand(1, 999),
            'name' => 'name_' . rand(1, 999),
            'address' => 'address_' . rand(1, 999),
            'city' => 'city_' . rand(1, 999),
            'state' => 'state_' . rand(1, 999),
            'postalCode' => 'postalCode_' . rand(1, 999),
            'location' => self::getRichLocation(),
        ];
    }

    private static function getRichDocumentShipmentItem() {
        return [
            'quantity' => rand(1, 999),
            'hash' => 'hash_' . rand(1, 999),
        ];
    }

    private static function getRichDocumentShipping() {
        return [
            'name' => 'name_' . rand(1, 999),
            'price' => 'price_' . rand(1, 999),
            'priceWithTaxes' => 'priceWithTaxes_' . rand(1, 999),
            'priceWithDiscounts' => 'priceWithDiscounts_' . rand(1, 999),
            'priceWithDiscountsWithTaxes' => 'priceWithDiscountsWithTaxes_' . rand(1, 999),
            'shippingTypeName' => 'shippingTypeName_' . rand(1, 999),
            'shippingSectionId' =>  rand(1, 999),
            'shippingCalculation' => rand(1, 100) > 50 ? ShippingCalculation::BY_UNITS : ShippingCalculation::BY_WEIGHT,
            'shipperId' => 'shipperId_' . rand(1, 999),
            'shippingTypePId' => 'shippingTypePId_' . rand(1, 999),
            'taxes' => [
                self::getRichDocumentElementTax(),
                self::getRichDocumentElementTax(),
            ],
            'discounts' => [
                self::getRichDocumentDiscount(),
                self::getRichDocumentDiscount(),
            ],
        ];
    }

    private static function getRichLocation() {
        return [
            'coordinate' => self::getCoordinate()
        ];
    }

    private static function getCoordinate() {
        return [
            'latitude' => rand(1, 999),
            'longitude' => rand(1, 999),
        ];
    }

    private static function getRichDocumentAdditionalInformation() {
        return [
            'id' => rand(1, 999),
            'name' => 'name_' . rand(1, 999),
            'value' => 'value_' . rand(1, 999),
        ];
    }

    private static function getRichDocumentCurrency() {
        return [
            'mode' => rand(1, 100) > 50 ? DocumentCurrencyMode::INVOICING : DocumentCurrencyMode::PURCHASE,
            'name' => 'name_' . rand(1, 999),
            'code' => 'code_' . rand(1, 999),
            'codeNumber' => 'codeNumber_' . rand(1, 999),
            'symbol' => 'symbol_' . rand(1, 999),
            'usdValue' => rand(1, 999),
        ];
    }

    private static function getRichDocumentHeadquarter() {
        return [
            'name' => 'name_' . rand(1, 999),
            'address' => 'address_' . rand(1, 999),
            'city' => 'city_' . rand(1, 999),
            'state' => 'state_' . rand(1, 999),
            'vat' => 'vat_' . rand(1, 999),
            'postalCode' => 'postalCode_' . rand(1, 999),
            'phone' => 'phone_' . rand(1, 999),
            'email' => 'email_' . rand(1, 999),
            'locationId' => rand(1, 999),
            'countryCode' => 'countryCode_' . rand(1, 999),
            'countryName' => 'countryName_' . rand(1, 999),
            'logo' => 'logo_' . rand(1, 999),
            'timeZone' => 'timeZone_' . rand(1, 999),
        ];
    }

    private static function getRichDocumentInformation() {
        return [
            'channelName' => 'channelName_' . rand(1, 999),
            'transactionId' => 'transactionId_' . rand(1, 999),
            'authNumber' => 'authNumber_' . rand(1, 999),
            'marketplaceId' => rand(1, 999),
            'headquarterName' => 'headquarterName_' . rand(1, 999),
        ];
    }

    private static function getRichDocumentPaymentSystem() {
        return [
            'taxes' => [
                self::getRichDocumentElementTax(),
                self::getRichDocumentElementTax(),
            ],
            'name' => 'name_' . rand(1, 999),
            'message' => 'message_' . rand(1, 999),
            'increaseType' => 'increaseType_' . rand(1, 999),
            'increaseValue' => rand(1, 999),
            'price' => 'price_' . rand(1, 999),
            'priceWithTaxes' => 'priceWithTaxes_' . rand(1, 999),
            'increaseMin' => rand(1, 999),
            'paymentType' => rand(1, 100) > 50 ? PaymentType::CASH_ON_DELIVERY : PaymentType::WIDGET,
        ];
    }

    private static function getRichDocumentTax() {
        return [
            'name' => 'name_' . rand(1, 999),
            'modality' => 'modality_' . rand(1, 999),
            'taxRate' => rand(1, 999),
            'reRate' => rand(1, 999),
            'base' => 'base_' . rand(1, 999),
            'baseWithDiscounts' => 'baseWithDiscounts_' . rand(1, 999),
            'taxPrice' => 'taxPrice_' . rand(1, 999),
            'rePrice' => 'rePrice_' . rand(1, 999),
            'totalPrice' => 'totalPrice_' . rand(1, 999),
            'discount' => 'discount_' . rand(1, 999),
        ];
    }

    private static function getRichDocumentTotal() {
        return [
            'totalRows' => 'totalRows_' . rand(1, 999),
            'totalRowsWithTaxes' => 'totalRowsWithTaxes_' . rand(1, 999),
            'totalShippingsWithDiscounts' => 'totalShippingsWithDiscounts_' . rand(1, 999),
            'totalShippingsWithDiscountsWithTaxes' => 'totalShippingsWithDiscountsWithTaxes_' . rand(1, 999),
            'totalPaymentSystem' => 'totalPaymentSystem_' . rand(1, 999),
            'totalPaymentSystemWithTaxes' => 'totalPaymentSystemWithTaxes_' . rand(1, 999),
            'total' => 'total_' . rand(1, 999),
            'totalWithDiscounts' => 'totalWithDiscounts_' . rand(1, 999),
            'totalTaxesValue' => 'totalTaxesValue_' . rand(1, 999),
            'totalWithDiscountsWithTaxes' => 'totalWithDiscountsWithTaxes_' . rand(1, 999),
            'totalRowsDiscountsValue' => 'totalRowsDiscountsValue_' . rand(1, 999),
            'totalBasketDiscountsValue' => 'totalBasketDiscountsValue_' . rand(1, 999),
            'totalShippingDiscountsValue' => 'totalShippingDiscountsValue_' . rand(1, 999),
            'totalVouchers' => 'totalVouchers_' . rand(1, 999),
        ];
    }

    private static function getRichDocumentUser() {
        return [
            'email' => 'email_' . rand(1, 999),
            'lastUsed' => self::getRichDateTime(),
            'gender' => rand(1, 100) > 50 ? Gender::MALE : Gender::UNDEFINED,
            'billingAddress' => self::getRichDocumentUserBillingAddress(),
            'shippingAddress' => self::getRichDocumentUserShippingAddress(),
            'customTags' => [
                self::getRichDocumentCustomTag(),
                self::getRichDocumentCustomTag(),
            ],
            'additionalInfo' => self::getRichDocumentUserAdditional(),
        ];
    }

    private static function getRichDocumentUserBillingAddress() {
        return [
            'alias' => 'alias_' . rand(1, 999),
            'firstName' => 'firstName_' . rand(1, 999),
            'lastName' => 'lastName_' . rand(1, 999),
            'company' => 'company_' . rand(1, 999),
            'address' => 'address_' . rand(1, 999),
            'addressAdditionalInformation' => 'addressAdditionalInformation_' . rand(1, 999),
            'number' => 'number_' . rand(1, 999),
            'city' => 'city_' . rand(1, 999),
            'state' => 'state_' . rand(1, 999),
            'postalCode' => 'postalCode_' . rand(1, 999),
            'vat' => 'vat_' . rand(1, 999),
            'nif' => 'nif_' . rand(1, 999),
            'location' => self::getRichLocation(),
            'phone' => 'phone_' . rand(1, 999),
            'mobile' => 'mobile_' . rand(1, 999),
            'fax' => 'fax_' . rand(1, 999),
            'tax' => 'tax_' . rand(1, 100) > 50,
            're' => 're_' . rand(1, 100) > 50,
            'userType' => rand(1, 100) > 50 ? UserType::PARTICULAR : UserType::BUSINESS,
            'reverseChargeVat' => rand(1, 100) > 50,
            'country' => 'country_' . rand(1, 999),
        ];
    }

    private static function getRichDocumentUserShippingAddress() {
        return [
            'alias' => 'alias_' . rand(1, 999),
            'firstName' => 'firstName_' . rand(1, 999),
            'lastName' => 'lastName_' . rand(1, 999),
            'company' => 'company_' . rand(1, 999),
            'address' => 'address_' . rand(1, 999),
            'addressAdditionalInformation' => 'addressAdditionalInformation_' . rand(1, 999),
            'number' => 'number_' . rand(1, 999),
            'city' => 'city_' . rand(1, 999),
            'state' => 'state_' . rand(1, 999),
            'postalCode' => 'postalCode_' . rand(1, 999),
            'vat' => 'vat_' . rand(1, 999),
            'nif' => 'nif_' . rand(1, 999),
            'location' => self::getRichLocation(),
            'phone' => 'phone_' . rand(1, 999),
            'mobile' => 'mobile_' . rand(1, 999),
            'fax' => 'fax_' . rand(1, 999),
            'tax' => 'tax_' . rand(1, 100) > 50,
            're' => 're_' . rand(1, 100) > 50,
            'reverseChargeVat' => rand(1, 100) > 50,
            'country' => 'country_' . rand(1, 999),
        ];
    }

    private static function getRichDocumentUserAdditional() {
        return [
            'salesAgent' => rand(1, 100) > 50,
            'salesAgentName' => 'salesAgentName_' . rand(1, 999),
            'blogger' => rand(1, 100) > 50,
            'bloggerName' => 'bloggerName_' . rand(1, 999),
        ];
    }

    private static function getRichDocumentAdditionalItem() {
        return [
            'name' => 'name_' . rand(1, 999),
            'type' => 'type_' . rand(1, 999),
            'amount' => 'amount_' . rand(1, 999),
            'taxes' => [
                self::getRichDocumentElementTax(),
                self::getRichDocumentElementTax(),
            ],
        ];
    }

    private static function getRichDocumentVoucher() {
        return [
            'availableBalance' => 'availableBalance_' . rand(1, 999),
            'code' => 'code_' . rand(1, 999),
        ];
    }

    private static function getDocumentParents() {
        return [
            'documentNumber' => 'documentNumber_' . rand(1, 999),
            'date' => self::getRichDateTime(),
            'documentType' => rand(1, 100) > 50 ? DocumentType::ORDER : DocumentType::INVOICE,
        ];
    }

    private static function getRewardPoints() {
        return [
            'id' => rand(1, 999),
            'expirationType' => rand(1, 100) > 50 ? RewardPointsRuleType::BY_AMOUNT : RewardPointsRuleType::BY_UNITS,
            'expirationDate' => (new \DateTime())->format(\DateTime::ATOM),
            'expirationDays' => rand(1, 999),
            'earned' => [
                'rules' => [
                    [
                        'id' => rand(1, 999),
                        'language' => [
                            'name' => 'name_' . rand(1, 999),
                            'description' => 'description_' . rand(1, 999)
                        ],
                        'from' => 'from_' . rand(1, 999),
                        'to' => 'to_' . rand(1, 999),
                        'value' => rand(1, 999),
                        'type' => rand(1, 100) > 50 ? RewardPointsRuleType::BY_AMOUNT : RewardPointsRuleType::BY_UNITS,
                        'valueMode' => rand(1, 100) > 50 ? RewardPointsRuleValueMode::EACH : RewardPointsRuleValueMode::FROM,
                        'earned' => rand(1, 999)

                    ]
                ]
            ],
            'redeemed' => [
                'toRedeem' => rand(1, 999),
                'rules' => [
                    [
                        'id' => rand(1, 999),
                        'language' => [
                            'name' => 'name_' . rand(1, 999),
                            'description' => 'description_' . rand(1, 999)
                        ],
                        'from' => 'from_' . rand(1, 999),
                        'to' => 'to_' . rand(1, 999),
                        'value' => rand(1, 999),
                        'redeemed' => rand(1, 999)
                    ]
                ]
            ],
            'summary' => [
                'totalByUnits' => rand(1, 999),
                'totalByAmount' => rand(1, 999),
                'totalRedeemed' => rand(1, 999),
                'totalEarned' => rand(1, 999)
            ],
            'pId' => 'pId_' . rand(1, 999)
        ];
    }
}
