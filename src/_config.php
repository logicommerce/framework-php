<?php

use FWK\Enums\RouteItems;
use SDK\Core\Resources\Environment;

if (!defined('DEVEL'))
    define('DEVEL', false);

define("FWK_NAMESPACE", 'FWK\\');

$path = Phar::running();
if (strlen($path) === 0)
    $path = FWK_PATH;

define('FWK_LOAD_PATH', $path);

// Version a cargar de JS de Core || Con valor SITE se va a buscar estos JS a la carpeta Core de assets de tienda
if (!defined('FWK_JS_VERSION'))
    define("FWK_JS_VERSION", '');

if (!defined('DEFAULT_CACHE_CONTROL_MAX_AGE'))
    define("DEFAULT_CACHE_CONTROL_MAX_AGE", '3600');

if (!defined('DEFAULT_CACHE_TTL'))
    define("DEFAULT_CACHE_TTL", '3600');

if (!defined('API_TIMEOUT'))
    define("API_TIMEOUT", 10);

define("REQUEST_ID", microtime(true) . '_' . bin2hex(random_bytes(8)));

if (!defined('REQUIRED_FIELD_HTML_FLAG'))
    define('REQUIRED_FIELD_HTML_FLAG', '<span class="required">*</span>');

// Show API requests errors, if you set this variable to TRUE, the first Api request that fail will generate a error exception
if (!defined('THROW_CONNECTION_ERRORS'))
    define('THROW_CONNECTION_ERRORS', false);

// This object will be created when de controllerError don't work
if (!defined('ERROR_WITHOUT_CONTROLLER'))
    define("ERROR_WITHOUT_CONTROLLER", 'ErrorWithoutController.php');

if (!defined('INTERNAL_PREFIX'))
    define("INTERNAL_PREFIX", 'lc_ecom_internal');

if (!defined('INTERNAL_THEME'))
    define("INTERNAL_THEME", 'Internal');

if (!defined('INTERNAL_UTIL_VERSION'))
    define("INTERNAL_UTIL_VERSION", 'Util');

if (!defined('INTERNAL_FOLDER'))
    define("INTERNAL_FOLDER", 'Internal');

define("REQUEST_HEADERS", array_change_key_case(getallheaders(), CASE_UPPER));

// Enable timer debug
$constTimerDebug = 'X-TIMER-DEBUG';
define('TIMER_DEBUG', (isset(REQUEST_HEADERS[$constTimerDebug]) && boolval(REQUEST_HEADERS[$constTimerDebug])));

// Enable Error message debug
$constDevelHeader = 'X-DEVEL-HEADER';
$constExceptionHeader = 'LC-DEVEL-KEY';
define('DEVEL_HEADER', (
    (isset(REQUEST_HEADERS[$constDevelHeader])
        && boolval(REQUEST_HEADERS[$constDevelHeader])
    ) || (isset(REQUEST_HEADERS[$constExceptionHeader])
        && (REQUEST_HEADERS[$constExceptionHeader] == Environment::get('APP_KEY'))
    )
));

// Overridable in SITE
if (!defined('URL_ROUTE'))
    define('URL_ROUTE', 'path');
// If you change this value, remeber to change .htaccess rule

// Default application Charset
if (!defined('CHARSET'))
    define('CHARSET', 'UTF-8');

// Default JSON_ENCODE_FILTER
if (!defined('JSON_ENCODE_FILTER'))
    define('JSON_ENCODE_FILTER', JSON_UNESCAPED_UNICODE);

// Images
if (!defined('IMAGE_NOT_FOUND'))
    define('IMAGE_NOT_FOUND', 'notFound.png');
if (!defined('IMAGE_OFFER_SMALL'))
    define('IMAGE_OFFER_SMALL', 'offerSmall.png');
if (!defined('IMAGE_DISCOUNTS_SMALL'))
    define('IMAGE_DISCOUNTS_SMALL', 'discountsSmall.png');
if (!defined('IMAGE_FEATURED_SMALL'))
    define('IMAGE_FEATURED_SMALL', 'featuredSmall.png');
if (!defined('IMAGE_LANGUAGE'))
    define('IMAGE_LANGUAGE', 'languages/flag_{{initials}}.png');
if (!defined('IMAGE_MISSING_PRODUCTS'))
    define('IMAGE_MISSING_PRODUCTS', 'notFoundProduct.png');
if (!defined('IMAGE_MISSING_PRODUCTS_LIST'))
    define('IMAGE_MISSING_PRODUCTS_LIST', 'notFoundProductSmall.png');
if (!defined('IMAGE_MISSING_OPTION_IMAGE'))
    define('IMAGE_MISSING_OPTION_IMAGE', 'notFoundProductOption.png');
if (!defined('IMAGE_MISSING_BASKET_ITEM'))
    define('IMAGE_MISSING_BASKET_ITEM', 'notFoundProductSmall.png');
if (!defined('USER_ACTIONS_FILE_EXTENSION'))
    define('USER_ACTIONS_FILE_EXTENSION', '.png');

// Default values
if (!defined('THEME_DEFAULT_MODE'))
    define('THEME_DEFAULT_MODE', 'bootstrap5');
if (!defined('THEME_DEFAULT_VERSION'))
    define('THEME_DEFAULT_VERSION', 'Desktop');

if (!defined('DEFAULT_ROUTE'))
    define('DEFAULT_ROUTE', [
        RouteItems::STATUS => 200,
        RouteItems::LANGUAGE => 'es',
        RouteItems::CURRENCY => 'EUR',
        RouteItems::THEME => [
            RouteItems::LAYOUT => 'default',
            RouteItems::CONTENT => 'default',
            RouteItems::NAME => 'default',
            RouteItems::CHANNEL => ''
        ]
    ]);

// DATE_TIME_FORMATER
if (!defined('DATE_TIME_FORMATER_DEFAULT_DATE_TYPE'))
    define('DATE_TIME_FORMATER_DEFAULT_DATE_TYPE', \IntlDateFormatter::MEDIUM);
if (!defined('DATE_TIME_FORMATER_DEFAULT_TIME_TYPE'))
    define('DATE_TIME_FORMATER_DEFAULT_TIME_TYPE', \IntlDateFormatter::MEDIUM);
if (!defined('DATE_TIME_FORMATER_DEFAULT_CALENDAR_TYPE'))
    define('DATE_TIME_FORMATER_DEFAULT_CALENDAR_TYPE', \IntlDateFormatter::GREGORIAN);

// Theme path for docuemnt templantes
if (!defined('DOCUEMENT_TEMPLATES_PATH'))
    define("DOCUEMENT_TEMPLATES_PATH", '/documentTemplates/');

// Set assets commerce path
if (!defined('CDN_ASSETS_COMMERCE'))
    define("CDN_ASSETS_COMMERCE", '/assets');

// Set interceptors namespace
define("SDK_INTERCEPTORS_NAMESPACE", 'FWK\\Core\\Interceptors');

// Set attachment max size
if (!defined('ATTACHMENT_MAX_SIZE'))
    define("ATTACHMENT_MAX_SIZE", 20);

// Set max length applied parameter value
if (!defined('MAX_LENGTH_APPLIED_PARAMETER_VALUE'))
    define("MAX_LENGTH_APPLIED_PARAMETER_VALUE", 500);

// Show used language labels if it set DEVEL model
if (!defined('SHOW_LANGUAGE_LABELS'))
    define("SHOW_LANGUAGE_LABELS", false);

// CURRENCY FORMAT
// Set the minimum number of decimals for currency
if (!defined('CURRENCY_DECIMALS_MIN_LENGTH'))
    define('CURRENCY_DECIMALS_MIN_LENGTH', 2);
// Set the maximun number of decimals for currency
if (!defined('CURRENCY_DECIMALS_MAX_LENGTH'))
    define('CURRENCY_DECIMALS_MAX_LENGTH', 2);
