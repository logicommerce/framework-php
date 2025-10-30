<?php

namespace FWK\Core\Exceptions;

use SDK\Core\Exceptions\SdkException;

/**
 * This is the CommerceException class.
 * This class extends SdkException (SDK\Core\Exceptions\SdkException), see this class.
 * <br>Here we define all the commerce exception codes.
 *
 * @see SdkException
 *
 * @package FWK\Core\Exceptions
 */
class CommerceException extends SdkException {

    public const ASYNC_ORDER_VALIDATE_PAYMENT = 'F030-A101';

    public const BASE_JSON_CONTROLLER_SET_DATA_ERROR = 'F030-B200';

    public const CACHE_CONTROL_UNDEFINED_ROUTE_TYPE = 'C130-B200';

    public const CONFIRM_ORDER_VALIDATE_PAYMENT = 'F030-C200';

    public const CONTROLLER_CATPCHA_ERROR = 'F030-C100';

    public const CONTROLLER_LOGIN_ERROR = 'F030-C101';

    public const CONTROLLER_LOGIN_REQUIRED = 'F030-C102';

    public const CONTROLLER_LOGOUT_ERROR = 'F030-C103';

    public const CONTROLLER_PARAMETER_REQUIRED = 'F030-C104';

    public const CONTROLLER_REDIRECT_ERROR = 'F030-C105';

    public const CONTROLLER_REDIRECT_NOT_FOUND = 'F030-C106';

    public const CONTROLLER_SET_DATA_VALUE_KEY_ALREADY_DEFINED = 'F030-C107';

    public const CONTROLLER_SET_DATA_VALUE_KEY_RESERVED = 'F030-C108';

    public const CONTROLLER_UNDEFINED_CRITICAL_DATA = 'F030-C109';

    public const CONTROLLER_UNDEFINED_REQUIRED_PARAMETER = 'F030-C110';

    public const DATE_TIME_FORMATTER_INIT_REQUIRED = 'F030-D000';

    public const FILL_FROM_PARENT_TRAIT_REQUIRED_ELEMENT_TRAIT = 'F030-F200';

    public const FILTER_INPUT_UNDEFINED_STRING_FORMAT = 'F030-F100';

    public const FORM_FACTORY_DUPLICATED_USER_FIELD = 'F030-F001';

    public const FORM_FACTORY_UNDEFINED_BLOG_SUBSCRIBE_TYPE = 'F030-F003';

    public const FORM_FACTORY_UNDEFINED_USER_FIELD = 'F030-F000';

    public const FORM_FACTORY_UNDEFINED_USER_KEY_CRITERIA = 'F030-F002';

    public const LOADER_CONTROLLER_NOT_FOUND = 'F030-L001';

    public const LOADER_ENUM_INTERNAL_NOT_FOUND = 'F030-L004';

    public const LOADER_INVALID_CLASS_NAME = 'F030-L005';

    public const LOADER_SERVICE_NOT_FOUND = 'F030-L000';

    public const LOADER_TWIG_EXTENSIONS_NOT_FOUND = 'F030-L006';

    public const LOADER_TWIG_FUNCTIONS_NOT_FOUND = 'F030-L003';

    public const LOADER_VIEW_HELPER_NOT_FOUND = 'F030-L002';

    public const ROUTE_PATHS_UNDEFINED_ROUTE_TYPE = 'F030-R200';

    public const SESSION_INFO_RESET = 'F030-S300';

    public const SESSION_KEY_ALREADY_EXISTS = 'F030-S200';

    public const SET_RELATED_ITEMS_TRAIT_INVALID_CLASS = 'F030-S100';

    public const THEME_CONFIGURATION_UNDEFINED = 'F030-T100';

    public const TWIG_LOADER_UNDEFINED_ESCAPING_STRATEGY = 'F030-T200';

    public const UTILS_MISSING_PDF_SIGNATURE = 'F030-U002';

    public const UTILS_NO_COUNTRIES_IN_RESPONSE = 'F030-U003';

    public const UTILS_UNDEFINED_ERROR_CODE_LABEL = 'F030-U002';

    public const UTILS_UNDEFINED_VALIDATE_TYPE = 'F030-U001';

    public const UTILS_VALIDATE_TYPE_ERROR = 'F030-U000';

    public const VIEW_HELPER_ARGUMENT_REQUIRED = 'F030-V001';

    public const VIEW_HELPER_INVALID_ARGUMENT = 'F030-V002';

    public const VIEW_HELPER_UNDEFINED_ARGUMENT = 'F030-V000';
}
