<?php

namespace FWK\Enums;

use SDK\Core\Enums\Enum;

/**
 * This is the Parameters enumeration class.
 * This class declares enumerations about request parameters.
 * <br> This class extends SDK\Core\Enums\Enum, see this class.
 *
 * @abstract
 *
 * @see Parameters::ACCOUNT
 * @see Parameters::ACCOUNT_ALIAS
 * @see Parameters::ACCOUNT_ID
 * @see Parameters::ACCOUNT_TYPE
 * @see Parameters::ACTION
 * @see Parameters::ADDITIONAL_DATA
 * @see Parameters::ADDRESS
 * @see Parameters::ADDRESS_ADDITIONAL_INFORMATION
 * @see Parameters::ADDED_FROM
 * @see Parameters::ADDED_TO
 * @see Parameters::ADDRESS_ID
 * @see Parameters::AGREEMENT
 * @see Parameters::ALIAS
 * @see Parameters::ALL_ITEMS
 * @see Parameters::AREA_ID
 * @see Parameters::AREA_ID_LIST
 * @see Parameters::AREA_PID
 * @see Parameters::AREA_POSITION
 * @see Parameters::ATTACHMENT
 * @see Parameters::ATTACHMENTS
 * @see Parameters::AUTH_NUMBER
 * @see Parameters::BASKET_CUSTOMTAG
 * @see Parameters::BASKET_TOKEN
 * @see Parameters::BILLING_ADDRESS
 * @see Parameters::BILLING_ADDRESS_ID
 * @see Parameters::BIRTHDAY
 * @see Parameters::BLOG_CATEGORY_ID
 * @see Parameters::BLOGGER_ID
 * @see Parameters::BODY
 * @see Parameters::BRAND_ID
 * @see Parameters::BRAND_ROLE
 * @see Parameters::BRANDS_ID
 * @see Parameters::BRANDS_LIST
 * @see Parameters::BUNDLE_ID_LIST
 * @see Parameters::BUNDLE_OPTIONS
 * @see Parameters::CAPTCHA_TOKEN
 * @see Parameters::CATEGORIES_PID
 * @see Parameters::CATEGORY_ID
 * @see Parameters::CATEGORY_ID_LIST
 * @see Parameters::CATEGORY_PID
 * @see Parameters::CATEGORY_PRODUCTS
 * @see Parameters::CATEGORY_ROLE
 * @see Parameters::CITY
 * @see Parameters::CODE
 * @see Parameters::COMBINATION_ID
 * @see Parameters::COMMENT
 * @see Parameters::COMMERCES
 * @see Parameters::COMPANY
 * @see Parameters::CONDITIONS_TO_BE_MET
 * @see Parameters::COUNTRY
 * @see Parameters::COUNTRY_CODE
 * @see Parameters::CREATE_ACCOUNT
 * @see Parameters::CUSTOM_TAGS
 * @see Parameters::CUSTOMER_ID
 * @see Parameters::CUSTOMTAGS_SEARCH_LIST
 * @see Parameters::CUSTOMTAGS_SEARCH_TYPE
 * @see Parameters::DATA
 * @see Parameters::DATE
 * @see Parameters::DEFAULT_ADDRESS
 * @see Parameters::DEFAULT_ONE
 * @see Parameters::DEFAULT_OPTION_PRICE_SORT
 * @see Parameters::DELIVERY_DATE
 * @see Parameters::DELIVERY_HASH
 * @see Parameters::DELIVERY_HOUR
 * @see Parameters::DELIVERY_POINT
 * @see Parameters::DESCRIPTION
 * @see Parameters::DISCARD_CONDITIONED_BY
 * @see Parameters::DISCOUNT_SELECTABLE_GIFT_ID
 * @see Parameters::DOCUMENT_ID
 * @see Parameters::DOCUMENT_TYPE
 * @see Parameters::EMAIL
 * @see Parameters::EXPIRES_AT_EXTEND_MINUTES
 * @see Parameters::EXPIRES_AT_EXTEND_MINUTES_UPON_USER_REQUEST
 * @see Parameters::EXTENSION
 * @see Parameters::END_DATE
 * @see Parameters::EVENT
 * @see Parameters::FAPIAO_ACTIVED
 * @see Parameters::FAX
 * @see Parameters::FIELD_NAME
 * @see Parameters::FILE_NAME
 * @see Parameters::FILTER
 * @see Parameters::FILTER_BY_OWN_AREA
 * @see Parameters::FILTER_BY_POINT_AREA 
 * @see Parameters::FILTER_CUSTOMTAG
 * @see Parameters::FILTER_CUSTOMTAG_RANGE
 * @see Parameters::FILTER_CUSTOMTAG_GROUP
 * @see Parameters::FILTER_CUSTOMTAG_INTERVAL
 * @see Parameters::FILTER_INDEXABLE
 * @see Parameters::FILTER_OPTION
 * @see Parameters::FIRST_NAME
 * @see Parameters::FROM_AMOUNT
 * @see Parameters::FROM_DATE
 * @see Parameters::FROM_EXPIRATION_DATE
 * @see Parameters::FROM_NUMBER
 * @see Parameters::FROM_PAYMENT_DATE
 * @see Parameters::FROM_PRICE
 * @see Parameters::FROM_PROCESSING_DATE
 * @see Parameters::GENDER
 * @see Parameters::GODFATHER_CODE
 * @see Parameters::GROUP_PID
 * @see Parameters::HASH
 * @see Parameters::H
 * @see Parameters::HASHES
 * @see Parameters::HEADQUARTERS
 * @see Parameters::HOST
 * @see Parameters::ID
 * @see Parameters::ID_LIST
 * @see Parameters::IMAGE
 * @see Parameters::IMPORTANCE
 * @see Parameters::INCLUDE_OPTIONS
 * @see Parameters::INCLUDE_PENDING
 * @see Parameters::INCLUDE_SUBCATEGORIES
 * @see Parameters::INCLUDE_SUBCOMPANY_STRUCTURE
 * @see Parameters::INCLUDE_SUBORDINATES
 * @see Parameters::INVOICE_NAME
 * @see Parameters::INVOICE_TYPE
 * @see Parameters::IP_STRICT
 * @see Parameters::ITEM_ID
 * @see Parameters::ITEMS
 * @see Parameters::JOB
 * @see Parameters::KEEP_PURCHASED_ITEMS
 * @see Parameters::LANGUAGE
 * @see Parameters::LANGUAGE_CODE
 * @see Parameters::LANGUAGE_ID
 * @see Parameters::LANGUAGES
 * @see Parameters::LAST_NAME
 * @see Parameters::LATITUDE
 * @see Parameters::LEVELS
 * @see Parameters::LIMIT
 * @see Parameters::LIMITED
 * @see Parameters::LOCATION
 * @see Parameters::LOCATION_ID
 * @see Parameters::LOCATION_LIST
 * @see Parameters::LONGITUDE
 * @see Parameters::MASTER
 * @see Parameters::MAX_PRICE
 * @see Parameters::MIN_PRICE
 * @see Parameters::MOBILE
 * @see Parameters::MODE
 * @see Parameters::MODULE
 * @see Parameters::MOTIVE_ID
 * @see Parameters::NAME
 * @see Parameters::NEW_PASSWORD
 * @see Parameters::NEW_PASSWORD_RETYPE
 * @see Parameters::NICK
 * @see Parameters::NIF
 * @see Parameters::NUMBER
 * @see Parameters::OFFSET
 * @see Parameters::ONLY_ACTIVE
 * @see Parameters::ONLY_FEATURED
 * @see Parameters::ONLY_IN_STOCK
 * @see Parameters::ONLY_CREATED_BY_ME
 * @see Parameters::ONLY_OFFERS
 * @see Parameters::ONLY_PENDING_PROCESSING
 * @see Parameters::OPTION_ID
 * @see Parameters::OPTIONS
 * @see Parameters::ORDER_ID 
 * @see Parameters::OSC 
 * @see Parameters::P_ID
 * @see Parameters::PAGE
 * @see Parameters::PAGE_TYPE
 * @see Parameters::PAGE_VIEW
 * @see Parameters::PAGES_GROUP_ID
 * @see Parameters::PAGES_GROUP_LIST
 * @see Parameters::PAGES_GROUP_PID
 * @see Parameters::PAGES_TO_SHOW
 * @see Parameters::PAID
 * @see Parameters::PARENT_ID
 * @see Parameters::PARENT_ID_LIST
 * @see Parameters::PARENT_PID
 * @see Parameters::PASSWORD
 * @see Parameters::PASSWORD_RETYPE
 * @see Parameters::PATH
 * @see Parameters::PAYMENT_SYSTEMS
 * @see Parameters::PDF
 * @see Parameters::PER_PAGE
 * @see Parameters::PHONE
 * @see Parameters::PICKUP_POINT_PROVIDER_ID
 * @see Parameters::PLUGIN
 * @see Parameters::PLUGIN_ACCOUNT_ID
 * @see Parameters::PLUGIN_MODULE
 * @see Parameters::PORTAL_ID
 * @see Parameters::POSITION
 * @see Parameters::POSITION_LIST
 * @see Parameters::POSTAL_CODE
 * @see Parameters::PRICE
 * @see Parameters::PRICE_RANGE
 * @see Parameters::PRIORITY
 * @see Parameters::PRODUCT_ID
 * @see Parameters::PRODUCT_ID_LIST
 * @see Parameters::PRODUCT_OPTIONS 
 * @see Parameters::PRODUCTS
 * @see Parameters::PRODUCTS_NUMBER
 * @see Parameters::PROPERTY
 * @see Parameters::PROTOCOL
 * @see Parameters::PROVIDER_PICKUP_POINT_HASH
 * @see Parameters::Q
 * @see Parameters::Q_DEEP
 * @see Parameters::Q_TYPE
 * @see Parameters::QUANTITY
 * @see Parameters::RADIUS
 * @see Parameters::RANDOM_ITEMS
 * @see Parameters::RATING
 * @see Parameters::RE
 * @see Parameters::REDIRECT
 * @see Parameters::REFERENCE
 * @see Parameters::REGISTERED_USER
 * @see Parameters::REGISTERED_USER_ID
 * @see Parameters::RETURN_POINT
 * @see Parameters::RETURN_CHECK
 * @see Parameters::RETURN_COMMENT
 * @see Parameters::RETURN_DELIVERY
 * @see Parameters::RETURN_POINT
 * @see Parameters::RETURN_QUANTITY
 * @see Parameters::REWARD_POINTS_ID
 * @see Parameters::RMA_REASON_COMMENT
 * @see Parameters::RMA_REASON_ID
 * @see Parameters::ROLE_ID
 * @see Parameters::ROW_ID_LIST
 * @see Parameters::ROUTE_TYPE
 * @see Parameters::SCROLL_CONTAINER
 * @see Parameters::SECTION_ID
 * @see Parameters::SERVICE
 * @see Parameters::SETUP
 * @see Parameters::SHIPMENT_HASH
 * @see Parameters::SHIPPING_ADDRESS
 * @see Parameters::SHIPPING_ADDRESS_ID
 * @see Parameters::SHIPPING_HASH
 * @see Parameters::SHOPPING_LIST_ID
 * @see Parameters::SHOPPING_LIST_ROW_REFERENCE
 * @see Parameters::SHOW_ALL_LANGUAGES
 * @see Parameters::SHOW_FILTERS
 * @see Parameters::SORT
 * @see Parameters::SORT_BY_ID_LIST
 * @see Parameters::SORT_DIRECTION
 * @see Parameters::START_DATE
 * @see Parameters::STATE
 * @see Parameters::STATUS
 * @see Parameters::STATUS_LIST
 * @see Parameters::STATUS_ID_LIST
 * @see Parameters::STATUSES
 * @see Parameters::STOCK
 * @see Parameters::SUBJECT
 * @see Parameters::SUBSCRIBED
 * @see Parameters::SUBSCRIPTION_TYPE
 * @see Parameters::SUBSTATUSES
 * @see Parameters::TAG_ID
 * @see Parameters::TEMPLATE
 * @see Parameters::THIRD_PARTY_SORT
 * @see Parameters::TO
 * @see Parameters::TO_AMOUNT
 * @see Parameters::TO_DATE
 * @see Parameters::TO_EMAIL
 * @see Parameters::TO_EXPIRATION_DATE
 * @see Parameters::TO_NAME
 * @see Parameters::TO_NUMBER
 * @see Parameters::TO_PAYMENT_DATE
 * @see Parameters::TO_PRICE
 * @see Parameters::TO_PROCESSING_DATE
 * @see Parameters::TOKEN 
 * @see Parameters::TRANSACTION_ID
 * @see Parameters::TAX
 * @see Parameters::TAX_INCLUDED
 * @see Parameters::TYPE
 * @see Parameters::U_ID
 * @see Parameters::UNIQUE_ID
 * @see Parameters::UPDATE_BASKET_ROWS
 * @see Parameters::USE_SHIPPING_ADDRESS
 * @see Parameters::USER_FORM
 * @see Parameters::USER_ID
 * @see Parameters::USER_TYPE
 * @see Parameters::USERNAME
 * @see Parameters::VALIDATOR
 * @see Parameters::VALUE
 * @see Parameters::VALUES
 * @see Parameters::VAT
 * @see Parameters::VISIBLE_ON_MAP
 * @see Parameters::VOTE
 *
 * @see Enum
 *
 * @package FWK\Enums
 */
abstract class Parameters extends Enum {

    public const ACCOUNT = 'account';

    public const ACCOUNT_ALIAS = 'accountAlias';

    public const ACCOUNT_ID = 'accountId';

    public const ACCOUNT_TYPE = 'accountType';

    public const ACCOUNTS = 'accounts';

    public const ACTION = 'action';

    public const ADDITIONAL_DATA = 'additionalData';

    public const ADDRESS = 'address';

    public const ADDRESS_ADDITIONAL_INFORMATION = 'addressAdditionalInformation';

    public const ADDRESS_ID = 'addressId';

    public const ADDED_FROM = 'addedFrom';

    public const ADDED_TO = 'addedTo';

    public const AGREEMENT = 'agreement';

    public const ALIAS = 'alias';

    public const ALL_ITEMS = 'allItems';

    public const AREA_ID = 'areaId';

    public const AREA_ID_LIST = 'areaIdList';

    public const AREA_PID = 'areaPid';

    public const AREA_POSITION = 'areaPosition';

    public const ATTACHMENT = 'attachment';

    public const ATTACHMENTS = 'attachments';

    public const AUTH_NUMBER = 'authNumber';

    public const BASKET_CUSTOMTAG = 'basketCustomTag';

    public const BASKET_TOKEN = 'basketToken';

    public const ROLE_PERMISSIONS = 'permissions';

    public const TARGET_DEFAULT = 'targetDefault';

    public const ALLOW_DIRECT_ORDER_CREATION = 'ordersCreateWithoutApproval';

    public const ALLOW_DIRECT_ORDER_APPROVAL_THIS_ACCOUNT = 'ordersApprovalDecisionThisAccount';

    public const ALLOW_DIRECT_ORDER_APPROVAL_SUB_ACCOUNTS = 'ordersApprovalDecisionSubAccounts';

    public const BUNDLE_ID_LIST = 'bundleIdList';

    public const BUNDLE_OPTIONS = 'bundleOptions';

    public const BILLING_ADDRESS = 'billingAddress';

    public const BILLING_ADDRESS_ID = 'billingAddressId';

    public const BIRTHDAY = 'birthday';

    public const BLOGGER_ID = 'bloggerId';

    public const BODY = 'body';

    public const BRAND_ID = 'brandId';

    public const BRAND_ROLE = 'brandRole';

    public const BRANDS_ID = 'brandsId';

    public const BRANDS_LIST = 'brandsList';

    public const BUYER_TOKEN = 'buyerToken';

    public const CAPTCHA_TOKEN = 'captchaToken';

    public const CATEGORIES_PID = 'categoriesPId';

    public const CATEGORY_ID = 'categoryId';

    public const CATEGORY_ID_LIST = 'categoryIdList';

    public const CATEGORY_PID = 'categoryPId';

    public const CATEGORY_PRODUCTS = 'categoryProducts';

    public const CATEGORY_ROLE = 'categoryRole';

    public const CITY = 'city';

    public const CODE = "code";

    public const COMBINATION_ID = 'combinationId';

    public const COMMENT = 'comment';

    public const COMMERCES = 'commerces';

    public const COMPANY = 'company';

    public const CONDITIONS_TO_BE_MET = 'conditionsToBeMet';

    public const COUNTRY = 'country';

    public const COUNTRY_CODE = 'countryCode';

    public const CREATE_ACCOUNT = 'createAccount';

    public const CUSTOM_TAGS = 'customTags';

    public const CUSTOMER_ID = 'customerId';

    public const CUSTOMTAGS_SEARCH_LIST = 'customTagsSearchList';

    public const CUSTOMTAGS_SEARCH_TYPE = 'customTagsSearchType';

    public const REGISTERED_USER_SEARCH_TYPE = 'registeredUserSearchType';

    public const DATA = 'data';

    public const DATE = 'date';

    public const DECISION = 'decision';

    public const DEFAULT_ADDRESS = 'defaultAddress';

    public const DEFAULT_ONE = 'defaultOne';

    public const DEFAULT_OPTION_PRICE_SORT = 'defaultOptionsPriceSort';

    public const DELIVERY_DATE = 'deliveryDate';

    public const DELIVERY_HASH = 'deliveryHash';

    public const DELIVERY_HOUR = 'deliveryHour';

    public const DELIVERY_POINT = 'deliveryPoint';

    public const DESCRIPTION = 'description';

    public const DISCOUNT_ID = 'discountId';

    public const DISCOUNT_SELECTABLE_GIFT_ID = 'discountSelectableGiftId';

    public const DISCARD_CONDITIONED_BY = 'discardConditionedBy';

    public const DOCUMENT_ID = 'documentId';

    public const DOCUMENT_TYPE = 'documentType';

    public const EMAIL = 'email';

    public const END_DATE = 'endDate';

    public const EVENT = 'event';

    public const EXPIRES_AT_EXTEND_MINUTES = 'expiresAtExtendMinutes';

    public const EXPIRES_AT_EXTEND_MINUTES_UPON_USER_REQUEST = 'expiresAtExtendMinutesUponUserRequest';

    public const EXTENSION = 'extension';

    public const FAPIAO_ACTIVED = 'fapiaoActived';

    public const FAX = 'fax';

    public const FIELD_NAME = 'fieldName';

    public const FIELDS = 'fields';

    public const FILE_NAME = 'fileName';

    public const FILTER = 'filter';

    public const FILTER_BY_OWN_AREA = 'filterByOwnArea';

    public const FILTER_BY_POINT_AREA = 'filterByPointArea';

    public const FILTER_CUSTOMTAG = 'filterCustomTag';

    public const FILTER_CUSTOMTAG_RANGE = 'filterCustomTagRange';

    public const FILTER_CUSTOMTAG_GROUP = 'filterCustomTagGroup';

    public const FILTER_CUSTOMTAG_INTERVAL = 'filterCustomTagInterval';

    public const FILTER_INDEXABLE = 'filterIndexable';

    public const FILTER_OPTION = 'filterOption';

    public const FIRST_NAME = 'firstName';

    public const FROM_AMOUNT = 'fromAmount';

    public const FROM_DATE = 'fromDate';

    public const FROM_EXPIRATION_DATE = 'fromExpirationDate';

    public const FROM_NUMBER = 'fromNumber';

    public const FROM_PAYMENT_DATE = 'fromPaymentDate';

    public const FROM_PRICE = 'fromPrice';

    public const FROM_PROCESSING_DATE = 'fromProcessingDate';

    public const FORM_SHOPPING_LIST_ROW_ID = 'fromShoppingListRow';

    public const GENDER = 'gender';

    public const GROUP_PID = 'groupPId';

    public const GODFATHER_CODE = 'godfatherCode';

    public const H = 'h';

    public const HASH = 'hash';

    public const HASHES = 'hashes';

    public const HEADQUARTERS = 'headquarters';

    public const HOST = 'host';

    public const ID = 'id';

    public const ID_LIST = 'idList';

    public const ID_USED = 'idUsed';

    public const DATE_ADDED = 'dateAdded';

    public const LAST_USED = 'lastUsed';

    public const REGISTERED_USER_EMAIL = 'registeredUserEmail';

    public const REGISTERED_USER_P_ID = 'registeredUserPId';

    public const IMAGE = 'image';

    public const IMAGE2 = 'image2';

    public const IMPORTANCE = 'importance';

    public const INCLUDE_PENDING = 'includePending';

    public const INCLUDE_SUBCATEGORIES = 'includeSubcategories';

    public const INCLUDE_SUBCOMPANY_STRUCTURE = 'includeSubCompanyStructure';

    public const INCLUDE_SUBORDINATES = 'includeSubordinates';

    public const INCLUDE_OPTIONS = 'includeOptions';

    public const INVOICE_NAME = 'invoiceName';

    public const INVOICE_TYPE = 'invoiceType';

    public const IP_STRICT = 'ipStrict';

    public const ITEM_ID = 'itemId';

    public const ITEMS = 'items';

    public const JOB = 'job';

    public const KEEP_PURCHASED_ITEMS = 'keepPurchasedItems';

    public const LANGUAGE = 'language';

    public const LANGUAGE_CODE = 'languageCode';

    public const LANGUAGE_ID = 'languageId';

    public const LANGUAGES = 'languages';

    public const LAST_NAME = 'lastName';

    public const LATITUDE = 'latitude';

    public const LEVELS = 'levels';

    public const LIMIT = 'limit';

    public const LIMITED = 'limited';

    public const LOCATION = 'location';

    public const LOCATION_ID = 'locationId';

    public const LOCATION_LIST = 'locationList';

    public const LONGITUDE = 'longitude';

    public const MASTER = 'master';

    public const MAX_PRICE = 'maxPrice';

    public const MIN_PRICE = 'minPrice';

    public const MOBILE = 'mobile';

    public const MODE = 'mode';

    public const MODULE = 'module';

    public const MOTIVE_ID = 'motiveId';

    public const NAME = 'name';

    public const TARGET = 'target';

    public const NEW_PASSWORD = 'newPassword';

    public const NEW_PASSWORD_RETYPE = 'newPasswordRetype';

    public const NICK = 'nick';

    public const NIF = 'nif';

    public const NUMBER = 'number';

    public const OFFSET = 'offset';

    public const ONLY_ACTIVE = 'onlyActive';

    public const ONLY_FEATURED = 'onlyFeatured';

    public const ONLY_IN_STOCK = 'onlyInStock';

    public const ONLY_CREATED_BY_ME = 'onlyCreatedByMe';

    public const ONLY_OFFERS = 'onlyOffers';

    public const ONLY_PENDING_PROCESSING = 'onlyPendingProcessing';

    public const OPTION_ID = 'optionId';

    public const OPTION_PRICE_MODE = 'optionsPriceMode';

    public const OPTIONS = 'options';

    public const ORDER_ID = 'orderId';

    public const OSC = 'osc';

    public const P_ID = 'pId';

    public const PAGE = 'page';

    public const PAGE_TYPE = 'pageType';

    public const PAGE_VIEW = 'pageView';

    public const PAGES_GROUP_ID = 'pagesGroupId';

    public const PAGES_GROUP_LIST = 'pagesGroupIdList';

    public const PAGES_GROUP_PID = 'pagesGroupPId';

    public const PAGES_TO_SHOW = 'pagesToShow';

    public const PAID = 'paid';

    public const PARENT_ID = 'parentId';

    public const PARENT_ID_LIST = 'parentIdList';

    public const PARENT_PID = 'parentPId';

    public const PASSWORD = 'password';

    public const PASSWORD_RETYPE = 'passwordRetype';

    public const PATH = 'path';

    public const PAYMENT_SYSTEMS = 'paymentSystems';

    public const PDF = 'pdf';

    public const PER_PAGE = 'perPage';

    public const PHONE = 'phone';

    public const PICKUP_POINT_PROVIDER_ID = 'pickupPointProviderId';

    public const PLUGIN = 'plugin';

    public const PLUGIN_ACCOUNT_ID = 'pluginAccountId';

    public const PLUGIN_MODULE = 'pluginModule';

    public const PORTAL_ID = 'portalId';

    public const POSITION = 'position';

    public const POSITION_LIST = 'positionList';

    public const POSTAL_CODE = 'postalCode';

    public const PRICE = 'price';

    public const PRICE_RANGE = 'priceRange';

    public const PRIORITY = 'priority';

    public const PRODUCT_ID = 'productId';

    public const PRODUCT_TYPE = 'productType';

    public const RECIPIENTS = 'recipients';

    public const PRODUCT_OPTIONS = 'productOptions';

    public const PRODUCTS = 'products';

    public const PRODUCT_ID_LIST = 'productIdList';

    public const PRODUCTS_NUMBER = 'productsNumber';

    public const PROPERTY = 'property';

    public const PROTOCOL = 'protocol';

    public const PROVIDER_PICKUP_POINT_HASH = 'providerPickupPointHash';

    public const Q = 'q';

    public const Q_DEEP = 'qDeep';

    public const Q_TYPE = 'qType';

    public const QUANTITY = 'quantity';

    public const RADIUS = 'radius';

    public const RANDOM_ITEMS = 'randomItems';

    public const RATING = 'rating';

    public const REDIRECT = 'redirect';

    public const RE = 're';

    public const REFERENCE = 'reference';

    public const REGISTERED_USER = 'registeredUser';

    public const REGISTERED_USER_ID = 'registeredUserId';

    public const RETURN_CHECK = 'returnCheck';

    public const RETURN_COMMENT = 'returnComment';

    public const RETURN_DELIVERY = 'returnDelivery';

    public const RETURN_POINT = 'returnPoint';

    public const RETURN_QUANTITY = 'returnQuantity';

    public const REWARD_POINTS_ID = 'rewardPointsId';

    public const RMA_REASON_COMMENT = 'rmaReasonComment';

    public const RMA_REASON_ID = 'rmaReasonId';

    public const ROLE_ID = 'roleId';

    public const ROUTE_TYPE = 'routeType';

    public const ROW_ID_LIST = 'rowIdList';

    public const SCROLL_CONTAINER = 'scrollContainer';

    public const SECTION_ID = 'sectionId';

    public const PARENT_HASH = 'parentHash';

    public const SERVICE = 'service';

    public const SETUP = 'setup';

    public const SHIPMENTS = 'shipments';

    public const SHIPMENT_HASH = 'shipmentHash';

    public const SHIPPING_ADDRESS = 'shippingAddress';

    public const SHIPPING_ADDRESS_ID = 'shippingAddressId';

    public const SHIPPING_HASH = 'shippingHash';

    public const SHOPPING_LIST_ID = 'shoppingListId';

    public const SHOPPING_LIST_ROW_REFERENCE = 'shoppingListRowReference';

    public const SHOW_ALL_LANGUAGES = 'showAllLanguages';

    public const SHOW_FILTERS = 'showFilters';

    public const SORT = 'sort';

    public const SORT_BY_ID_LIST = 'sortByIdList';

    public const SORT_DIRECTION = 'sortDirection';

    public const START_DATE = 'startDate';

    public const STATE = 'state';

    public const STATUS = 'status';

    public const STATUS_LIST = 'statusList';

    public const STATUSES = 'statuses';

    public const STATUS_ID_LIST = 'statusIdList';

    public const STOCK = 'stock';

    public const SUBJECT = 'subject';

    public const SUBSCRIBED = 'subscribed';

    public const SUBSCRIPTION_TYPE = 'subscriptionType';

    public const SUBSTATUSES = 'substatuses';

    public const TAG_ID = 'tagId';

    public const TEMPLATE = 'template';

    public const THIRD_PARTY_SORT = 'thirdPartySort';

    public const TO = 'to';

    public const TO_AMOUNT = 'toAmount';

    public const TO_DATE = 'toDate';

    public const TO_EMAIL = 'toEmail';

    public const TO_EXPIRATION_DATE = 'toExpirationDate';

    public const TO_NAME = 'toName';

    public const TO_NUMBER = 'toNumber';

    public const TO_PAYMENT_DATE = 'toPaymentDate';

    public const TO_PRICE = 'toPrice';

    public const TO_PROCESSING_DATE = 'toProcessingDate';

    public const TOKEN = 'token';

    public const TRANSACTION_ID = 'transactionId';

    public const TAX = 'tax';

    public const TAX_INCLUDED = 'taxIncluded';

    public const TYPE = 'type';

    public const U_ID = 'uId';

    public const UNIQUE_ID = 'uniqueId';

    public const UPDATE_BASKET_ROWS = 'updateBasketRows';

    public const USE_SHIPPING_ADDRESS = 'useShippingAddress';

    public const USER_FORM = 'userForm';

    public const USER_ID = 'userId';

    public const USER_TYPE = 'userType';

    public const USERNAME = 'username';

    public const VALIDATOR = 'validator';

    public const VALUE = 'value';

    public const VALUES = 'values';

    public const VAT = 'vat';

    public const VISIBLE_ON_MAP = 'visibleOnMap';

    public const VOTE = 'vote';
}
