<?php

namespace FWK\Core\FilterInput;

use FWK\Controllers\Util\Internal\PreviewDocumentTemplateController;
use FWK\Enums\Parameters;
use SDK\Enums\UserType;
use FWK\Core\Form\FormFactory;
use FWK\Enums\Services;
use FWK\Enums\SetUserTypeForms;
use SDK\Enums\AddProductsMode;
use SDK\Enums\DeliveryType;
use SDK\Enums\RelatedItemsType;

/**
 * This is the FilterInputFactory class, a factory of FilterInput instances.
 * This class facilitates some predefined filterInput for the parameters of some of the most usual requests.
 *
 * @abstract
 *
 * @see FilterInputFactory::getAddBaseCommentParameters()
 * @see FilterInputFactory::getAddBundleParameters()
 * @see FilterInputFactory::getAddCommentUserParameters()
 * @see FilterInputFactory::getAddLinkedParameters()
 * @see FilterInputFactory::getAddProductOptionParameters()
 * @see FilterInputFactory::getAddProductParameters()
 * @see FilterInputFactory::getBlogRssParameters()
 * @see FilterInputFactory::getCheckoutNextStep()
 * @see FilterInputFactory::getConfirmOrderGetParams()
 * @see FilterInputFactory::getContactUserParameters()
 * @see FilterInputFactory::getCountriesParameters()
 * @see FilterInputFactory::getCountryCodeParameter()
 * @see FilterInputFactory::getCustomerIdParameter()
 * @see FilterInputFactory::getCustomTagsParameter()
 * @see FilterInputFactory::getDataParameters()
 * @see FilterInputFactory::getDeleteRowParameters()
 * @see FilterInputFactory::getDeleteRowsParameters()
 * @see FilterInputFactory::getDiscountsCommonParameters()
 * @see FilterInputFactory::getDiscountsParameters()
 * @see FilterInputFactory::getDiscountsParameters()
 * @see FilterInputFactory::getEditPaymentSystemParameters()
 * @see FilterInputFactory::getEditQuantityParameters()
 * @see FilterInputFactory::getFeedParameters()
 * @see FilterInputFactory::getIdParameter()
 * @see FilterInputFactory::getLanguageCodeParameter
 * @see FilterInputFactory::getLocationListParameters()
 * @see FilterInputFactory::getLocationsLocalitiesParameters()
 * @see FilterInputFactory::getLocationsParameters()
 * @see FilterInputFactory::getLocationsPathParameters()
 * @see FilterInputFactory::getNewsletterParameters()
 * @see FilterInputFactory::getPagesParameters()
 * @see FilterInputFactory::getPathParameter()
 * @see FilterInputFactory::getPdfParameter()
 * @see FilterInputFactory::getProductDiscountsParameters()
 * @see FilterInputFactory::getProductsDiscountsParameters()
 * @see FilterInputFactory::getProductsListParameters()
 * @see FilterInputFactory::getRecommendUserParameters()
 * @see FilterInputFactory::getSelectedOptions()
 * @see FilterInputFactory::getSetAccount()
 * @see FilterInputFactory::getSetAddressBookParameters()
 * @see FilterInputFactory::getSetCurrencyParameters()
 * @see FilterInputFactory::getSetDeliveryParameters()
 * @see FilterInputFactory::getTokenParameter()
 * @see FilterInputFactory::getUniqueIdParameter
 * @see FilterInputFactory::getUrlParameter()
 * @see FilterInputFactory::getVoucherCodeParameters()
 *
 * @package FWK\Core\FilterInput
 */
abstract class FilterInputFactory {

    public const ADD = 'ADD';

    public const DELETE = 'DELETE';

    /**
     * This static method returns the predefined filterInput configuration for a brands parameter.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     * In this case the array only contains a position 'PAGE' with its corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getBrandsParameters(): array {
        return
            self::getPaginableItemsParameter() +
            self::getSortableItemsParameters('BrandSort') +
            self::getIdParameter() +
            self::getPIdParameter() +
            [Parameters::ID_LIST => self::getIdListFilterInput()] +
            [
                Parameters::Q => new FilterInput(),
                Parameters::ONLY_ACTIVE => new FilterInput([
                    FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                    FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_BOOLEAN
                ])
            ];
    }

    /**
     * This static method returns the predefined filterInput configuration for a discounts common parameter.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     * In this case the array only contains a position 'PAGE' with its corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getDiscountsCommonParameters(): array {
        return self::getPaginableItemsParameter() + self::getSortableItemsParameters('DiscountSort') +
            [
                Parameters::CONDITIONS_TO_BE_MET => new FilterInput([
                    FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_TO_UPPER,
                    FilterInput::CONFIGURATION_FILTER_KEY_FUNCTION_VALIDATOR => 'SDK\Enums\DiscountConditionsToBeMet::areValid',
                    FilterInput::CONFIGURATION_FILTER_KEY_FUNCTION_VALIDATOR_EXPLONDE_PARAM_SEPARATOR => ','
                ])
            ] +
            [
                Parameters::DISCARD_CONDITIONED_BY => new FilterInput([
                    FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_TO_UPPER,
                    FilterInput::CONFIGURATION_FILTER_KEY_FUNCTION_VALIDATOR => 'SDK\Enums\DiscountDiscardConditionedBy::areValid',
                    FilterInput::CONFIGURATION_FILTER_KEY_FUNCTION_VALIDATOR_EXPLONDE_PARAM_SEPARATOR => ','
                ])
            ];
    }

    /**
     * This static method returns the predefined filterInput configuration for a discounts parameter.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     * In this case the array only contains a position 'PAGE' with its corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getDiscountsParameters(): array {
        return
            self::getDiscountsCommonParameters() +
            self::getIdParameter() +
            self::getPIdParameter();
    }

    /**
     * This static method returns the predefined filterInput configuration for a product discounts parameter.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     * In this case the array only contains a position 'PAGE' with its corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getProductDiscountsParameters(): array {
        return self::getDiscountsCommonParameters();
    }

    /**
     * This static method returns the predefined filterInput configuration for a product discounts parameter.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     * In this case the array only contains a position 'PAGE' with its corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getProductsDiscountsParameters(): array {
        return
            self::getDiscountsCommonParameters() +
            [
                Parameters::PRODUCT_ID_LIST => new FilterInput([
                    FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                    FilterInput::CONFIGURATION_FILTER_KEY_REGEX_VALIDATE => FilterInput::REGEX_VALIDATE_INT_LIST,
                    FilterInput::CONFIGURATION_FILTER_KEY_FUNCTION_VALIDATOR_EXPLONDE_PARAM_SEPARATOR => ','
                ])
            ];
    }

    /**
     * This static method returns the predefined filterInput Id parameter and filter configuration.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     * In this case the array only contains a position 'ID' with its corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getIdParameter(): array {
        return [
            Parameters::ID => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_INT
            ])
        ];
    }

    public static function getParentIdParameter(): array {
        return [
            Parameters::PARENT_ID => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_INT
            ])
        ];
    }

    /**
     * This static method returns the predefined filterInput Id parameter and filter configuration.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     * In this case the array only contains a position 'ID' with its corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getPIdParameter(): array {
        return [
            Parameters::P_ID => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false
            ])
        ];
    }

    /**
     * This static method returns the predefined filterInput Path parameter and filter configuration.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     * In this case the array only contains a position 'PATH' with its corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getPathParameter(): array {
        return [
            Parameters::PATH => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false
            ])
        ];
    }

    public static function getPasswordParameter(): array {
        return [
            Parameters::PASSWORD => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false
            ])
        ];
    }

    /**
     * This static method returns the predefined filterInput configuration for an Id list parameter.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     * In this case the array only contains a position 'ID' with its corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getIdListFilterInput(): FilterInput {
        return new FilterInput([
            FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
            FilterInput::CONFIGURATION_FILTER_KEY_REGEX_VALIDATE => FilterInput::REGEX_VALIDATE_INT_LIST
        ]);
    }

    /**
     * This static method returns the predefined filterInput configuration for an UniqueId parameter.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     * In this case the array only contains a position 'ID' with its corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getUniqueIdParameter(): array {
        return [
            Parameters::UNIQUE_ID => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false
            ])
        ];
    }

    /**
     * This static method returns the predefined filterInput configuration for a Token parameter.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     * In this case the array only contains a position 'TOKEN' with its corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getTokenParameter(): array {
        return [
            Parameters::TOKEN => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false
            ])
        ];
    }

    /**
     * This static method returns the predefined filterInput configuration for a Path parameter.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     * In this case the array only contains a position 'URL_ROUTE' with its corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getUrlParameter(): array {
        return [
            URL_ROUTE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_TO_LOWER
            ])
        ];
    }

    /**
     * This static method returns the predefined filterInput configuration for a Email parameter.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     * In this case the array only contains a position 'URL_ROUTE' with its corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getEmailParameter(): array {
        return [
            Parameters::EMAIL => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => true,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_EMAIL
            ])
        ];
    }

    /**
     * This static method returns the predefined filterInput configuration for a page parameter.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     * In this case the array only contains a position 'URL_ROUTE' with its corresponding properly initialized filterInput.
     *
     * @return array PaginableItems
     */
    public static function getPaginableItemsParameter(): array {
        return
            [
                Parameters::PAGE => new FilterInput([
                    FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                    FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_INT,
                    FilterInput::CONFIGURATION_FILTER_KEY_FILTER_FLAGS => array(
                        'options' => array(
                            'min_range' => 1
                        )
                    )
                ]),
                Parameters::PER_PAGE => new FilterInput([
                    FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                    FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_INT
                ])
            ];
    }

    /**
     * This static method returns the predefined filterInput configuration for a Page parameter.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     * In this case the array only contains a position 'PAGE' with its corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getPagesParameters(): array {
        return self::getPaginableItemsParameter() +
            self::getSortableItemsParameters('PageSort');
    }


    /**
     * This static method returns the predefined filterInput configuration for a Sortable Items parameter.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     * In this case the array only contains a position 'PAGE' with its corresponding properly initialized filterInput.
     *
     * @param string $sortEnum
     * 
     * @return array
     */
    public static function getSortableItemsParameters(string $sortEnum): array {
        return [
            Parameters::SORT => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_TO_UPPER,
                FilterInput::CONFIGURATION_FILTER_KEY_FUNCTION_VALIDATOR => 'SDK\Enums\\' . $sortEnum . '::areValid',
                FilterInput::CONFIGURATION_FILTER_KEY_FUNCTION_VALIDATOR_EXPLONDE_PARAM_SEPARATOR => ','
            ]),
            Parameters::SORT_BY_ID_LIST => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_REGEX_VALIDATE => FilterInput::REGEX_VALIDATE_INT_LIST,
                FilterInput::CONFIGURATION_FILTER_KEY_FUNCTION_VALIDATOR_EXPLONDE_PARAM_SEPARATOR => ','
            ])
        ];
    }

    public static function getRolesPermissionParameters(): array {
        return [
            Parameters::ROLE_PERMISSIONS => new FilterInput([
                FilterInput::CONFIGURATION_NO_FILTER => true
            ])
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of a productList request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getProductsListParameters(): array {
        return self::getPaginableItemsParameter() + self::getSortableItemsParameters('ProductSort') + [
            Parameters::CATEGORY_ID_LIST => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_REGEX_VALIDATE => FilterInput::REGEX_VALIDATE_INT_LIST
            ]),
            Parameters::INCLUDE_SUBCATEGORIES => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_BOOLEAN
            ]),
            Parameters::BRANDS_LIST => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_REGEX_VALIDATE => FilterInput::REGEX_VALIDATE_INT_LIST
            ]),
            Parameters::ONLY_FEATURED => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_BOOLEAN
            ]),
            Parameters::ONLY_OFFERS => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_BOOLEAN
            ]),
            Parameters::FROM_PRICE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_FLOAT
            ]),
            Parameters::TO_PRICE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_FLOAT
            ]),
            Parameters::PRICE_RANGE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_REGEX_VALIDATE => FilterInput::REGEX_VALIDATE_FLOAT_RANGE
            ]),
            Parameters::MAX_PRICE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_FLOAT
            ]),
            Parameters::MIN_PRICE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_FLOAT
            ]),
            Parameters::Q => new FilterInput(),
            Parameters::Q_TYPE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_TO_UPPER,
                FilterInput::CONFIGURATION_FILTER_KEY_FUNCTION_VALIDATOR => 'SDK\Enums\ProductSearchType::isValid'
            ]),
            Parameters::Q_DEEP => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_TO_UPPER,
                FilterInput::CONFIGURATION_FILTER_KEY_FUNCTION_VALIDATOR => 'SDK\Enums\ProductSearchDeep::isValid'
            ]),
            Parameters::CUSTOMTAGS_SEARCH_LIST => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_REGEX_VALIDATE => FilterInput::REGEX_VALIDATE_INT_LIST
            ]),
            Parameters::CUSTOMTAGS_SEARCH_TYPE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false
            ]),
            Parameters::FILTER_CUSTOMTAG => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false
            ]),
            Parameters::FILTER_CUSTOMTAG_RANGE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_REGEX_VALIDATE => FilterInput::REGEX_VALIDATE_FLOAT_RANGE
            ]),
            Parameters::FILTER_CUSTOMTAG_INTERVAL => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_REGEX_VALIDATE => FilterInput::REGEX_VALIDATE_INTERVAL
            ]),
            Parameters::FILTER_OPTION => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false
            ]),
            Parameters::TEMPLATE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_INT,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_FLAGS => array(
                    'options' => array(
                        'min_range' => 1,
                        'max_range' => 2
                    )
                )
            ]),
            Parameters::ID_LIST => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_REGEX_VALIDATE => FilterInput::REGEX_VALIDATE_INT_LIST,
                FilterInput::CONFIGURATION_FILTER_KEY_FUNCTION_VALIDATOR_EXPLONDE_PARAM_SEPARATOR => ','
            ]),
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of a newsList request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getNewsListParameters(): array {
        return
            self::getPaginableItemsParameter() +
            self::getIdParameter() +
            self::getPIdParameter() +
            self::getSortableItemsParameters('NewsSort') + [
                Parameters::ID_LIST => new FilterInput([
                    FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                    FilterInput::CONFIGURATION_FILTER_KEY_REGEX_VALIDATE => FilterInput::REGEX_VALIDATE_INT_LIST,
                    FilterInput::CONFIGURATION_FILTER_KEY_FUNCTION_VALIDATOR_EXPLONDE_PARAM_SEPARATOR => ','
                ]),
                Parameters::ONLY_ACTIVE => new FilterInput([
                    FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                    FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_BOOLEAN
                ]),
                Parameters::Q => new FilterInput(),
                Parameters::Q_TYPE => new FilterInput([
                    FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                    FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_TO_UPPER,
                    FilterInput::CONFIGURATION_FILTER_KEY_FUNCTION_VALIDATOR => 'SDK\Enums\SearchType::isValid'
                ]),
                Parameters::Q_DEEP => new FilterInput([
                    FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                    FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_TO_UPPER,
                    FilterInput::CONFIGURATION_FILTER_KEY_FUNCTION_VALIDATOR => 'SDK\Enums\SearchDeep::isValid'
                ]),
                Parameters::RANDOM_ITEMS => new FilterInput([
                    FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                    FilterInput::CONFIGURATION_FILTER_KEY_REGEX_VALIDATE => FilterInput::REGEX_VALIDATE_INT_LIST
                ])
            ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of a Feed request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getFeedParameters(): array {
        return self::getIdParameter() + self::getHashParameter() + self::getLanguageCodeParameter();
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of an addProduct request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getAddProductParameters(): array {
        return self::getIdParameter() + [
            Parameters::QUANTITY => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_INT
            ]),
            Parameters::FORM_SHOPPING_LIST_ROW_ID => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_INT
            ]),
            Parameters::MODE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => true,
                FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_TO_UPPER,
                FilterInput::CONFIGURATION_FILTER_KEY_FUNCTION_VALIDATOR => function ($value) {
                    return $value === AddProductsMode::ADD || $value === AddProductsMode::UPDATE;
                }
            ]),
            Parameters::OPTIONS => new FilterInput([
                FilterInput::CONFIGURATION_NO_FILTER => true
            ]),
            Parameters::PLUGIN_ACCOUNT_ID => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_INT
            ]),
            Parameters::COMBINATION_ID => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_INT
            ]),
            Parameters::STOCK => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_INT
            ]),
            Parameters::PRODUCT_TYPE => new FilterInput([]),
            Parameters::RECIPIENTS => new FilterInput([
                FilterInput::CONFIGURATION_NO_FILTER => true
            ]),
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of an getProductCalculateData request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getGetProductCalculateDataParameters(): array {
        return self::getIdParameter() + [
            Parameters::QUANTITY => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_INT
            ]),
            Parameters::OPTIONS => new FilterInput([
                FilterInput::CONFIGURATION_NO_FILTER => true
            ])
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of an
     * addLinked request.
     * Returns an array where the key of each position is the name of the parameter and the value of
     * the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getAddLinkedParameters(): array {
        return self::getAddProductParameters() + [
            Parameters::SECTION_ID => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_INT
            ]),
            Parameters::PARENT_HASH => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false
            ])
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of an addProducts request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getAddProductsParameters(): array {
        return [
            Parameters::MODE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => true,
                FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_TO_UPPER,
                FilterInput::CONFIGURATION_FILTER_KEY_FUNCTION_VALIDATOR => function ($value) {
                    return $value === AddProductsMode::ADD || $value === AddProductsMode::UPDATE;
                }
            ]),
            Parameters::PRODUCTS => new FilterInput([
                FilterInput::CONFIGURATION_NO_FILTER => true
            ]),
            Parameters::ID => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false
            ]),
            Parameters::TYPE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false
            ]),
            Parameters::PLUGIN_ACCOUNT_ID => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_INT
            ])
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of an addBundle request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getAddBundleParameters(): array {
        return self::getIdParameter() + [
            Parameters::QUANTITY => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_INT
            ]),
            Parameters::FORM_SHOPPING_LIST_ROW_ID => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_INT
            ]),
            Parameters::ITEMS => new FilterInput([
                FilterInput::CONFIGURATION_NO_FILTER => true
            ]),
            Parameters::PLUGIN_ACCOUNT_ID => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_INT
            ])
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of an AddGift request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getAddGiftParameters(): array {
        return [
            Parameters::PRODUCT_ID => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_INT
            ]),
            Parameters::OPTIONS => new FilterInput([
                FilterInput::CONFIGURATION_NO_FILTER => true
            ]),
            Parameters::DISCOUNT_SELECTABLE_GIFT_ID => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_INT
            ])
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of an getBundleCalculateDataParameters request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getGetBundleCalculateDataParameters(): array {
        return self::getAddBundleParameters();
    }


    /**
     * This static method returns the predefined filterInput configurations for the parameters of an addProductOption request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getAddProductOptionParameters(): array {
        return self::getIdParameter() + [
            Parameters::VALUES => new FilterInput([
                FilterInput::CONFIGURATION_NO_FILTER => true
            ])
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of an addProductOptionValue request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getAddProductOptionValueParameters(): array {
        return [
            Parameters::VALUE => new FilterInput([
                FilterInput::CONFIGURATION_NO_FILTER => true
            ]),
            Parameters::EXTENSION => new FilterInput([
                FilterInput::CONFIGURATION_NO_FILTER => true
            ]),
            Parameters::FILE_NAME => new FilterInput([
                FilterInput::CONFIGURATION_NO_FILTER => true
            ]),
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of a contactUser request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getContactUserParameters(): array {
        return self::getEmailParameter() + [
            Parameters::FIRST_NAME => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => true,
                FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_HTML_CLEAR
            ]),
            Parameters::LAST_NAME => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => true,
                FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_HTML_CLEAR
            ]),
            Parameters::PHONE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => true,
                FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_HTML_CLEAR
            ])
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters state, city and postalCode.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getStateCityPostalParameters(): array {
        $result = [];

        $result[Parameters::STATE] = new FilterInput([
            FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => true,
            FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_HTML_CLEAR
        ]);
        $result[Parameters::CITY] = new FilterInput([
            FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => true,
            FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_HTML_CLEAR
        ]);
        $result[Parameters::POSTAL_CODE] = new FilterInput([
            FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => true,
            FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_HTML_CLEAR
        ]);

        foreach (
            [
                UserType::BUSINESS,
                UserType::FREELANCE,
                UserType::PARTICULAR
            ] as $type
        ) {
            foreach (
                [
                    SetUserTypeForms::SHIPPING,
                    SetUserTypeForms::USER,
                    SetUserTypeForms::BILLING
                ] as $ut
            ) {
                $result[$type . '_' . $ut . '_' . Parameters::STATE] = $result[Parameters::STATE];
                $result[$type . '_' . $ut . '_' . Parameters::CITY] = $result[Parameters::CITY];
                $result[$type . '_' . $ut . '_' . Parameters::POSTAL_CODE] = $result[Parameters::POSTAL_CODE];
            }
        }
        return $result;
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of an addBaseComment request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getAddBaseCommentParameters(): array {
        return self::getIdParameter() + [
            Parameters::COMMENT => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => true,
                FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_HTML_CLEAR
            ])
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of a recommendUser request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getRecommendUserParameters(): array {
        return self::getAddBaseCommentParameters() + self::getEmailParameter() + [
            Parameters::NAME => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => true,
                FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_HTML_CLEAR
            ]),
            Parameters::TO_NAME => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => true,
                FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_HTML_CLEAR
            ]),
            Parameters::TO_EMAIL => self::getEmailParameter()[Parameters::EMAIL]
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of an addCommentUser request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getAddCommentUserParameters(): array {
        return self::getAddBaseCommentParameters() + [
            Parameters::NICK => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => true,
                FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_HTML_CLEAR
            ]),
            Parameters::VOTE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => true,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_INT
            ])
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters Hash.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getHashParameter(): array {
        return [
            Parameters::HASH => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false
            ])
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters h.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getHParameter(): array {
        return [
            Parameters::H => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false
            ])
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the Unsubscribe request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getUnsubscribeParameters(): array {
        return self::getHashParameter() + self::getIdParameter();
    }

    /**
     * This static method returns the predefined filterInput configurations for the Preview Document Template request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getPreviewDocumentParameters(): array {
        return self::getIdParameter() + [
            Parameters::TEMPLATE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_AVAILABLE_VALUES => [
                    PreviewDocumentTemplateController::TEMPLATE_CORRECTIVE_INVOICE,
                    PreviewDocumentTemplateController::TEMPLATE_DELIVERY_NOTE,
                    PreviewDocumentTemplateController::TEMPLATE_INVOICE,
                    PreviewDocumentTemplateController::TEMPLATE_ORDER,
                    PreviewDocumentTemplateController::TEMPLATE_RETURN,
                    PreviewDocumentTemplateController::TEMPLATE_RMA
                ]
            ])

        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of a deleteRow request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getDeleteRowParameters(): array {
        return self::getHashParameter();
    }


    /**
     * This static method returns the predefined filterInput configurations for the parameters of a basket delete rows request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getDeleteRowsParameters(): array {
        return [
            Parameters::HASHES => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false
                // FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false
                // FilterInput::CONFIGURATION_NO_FILTER => true
            ])
        ];
    }

    /**
     * This static method returns the predefined filterInput configuration for a CountryCode parameter.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getCountryCodeParameter(): array {
        return [
            Parameters::COUNTRY_CODE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                // Country code in ISO 3166-2 format
                FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_TO_UPPER,
                FilterInput::CONFIGURATION_FILTER_KEY_REGEX_VALIDATE => '/^[a-zA-Z]{2}$/'
            ])
        ];
    }

    /**
     * This static method returns the predefined filterInput configuration for the languageCode parameter .
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getLanguageCodeParameter(): array {
        return [
            Parameters::LANGUAGE_CODE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                // Language code in ISO 639-1 format
                FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_TO_LOWER,
                FilterInput::CONFIGURATION_FILTER_KEY_REGEX_VALIDATE => '/^[a-zA-Z]{2}$/'
            ])
        ];
    }

    /**
     * This static method returns the predefined filterInput configuration for the parameters of a Geolocation->getCountries request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getCountriesParameters(): array {
        return self::getLanguageCodeParameter() + self::getCountryCodeParameter();
    }

    /**
     * This static method returns the predefined filterInput configuration for the parameters of a Geolocation->getLocations request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getLocationsParameters(): array {
        return self::getCountriesParameters() + [
            Parameters::PARENT_ID => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_INT
            ])
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of a Geolocation->getLocationsPath request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getLocationsPathParameters(): array {
        return self::getCountriesParameters() + [
            Parameters::LOCATION_ID => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_INT
            ])
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of a Geolocation->getLocationsLocalities request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getLocationsLocalitiesParameters(): array {
        return self::getCountriesParameters() + [
            Parameters::Q => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false
            ])
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of a User->Internal->LocationsPath request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getUserLocationsPathParameters(): array {
        return self::getLocationsPathParameters() +  [
            Parameters::FIELD_NAME => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false
            ]),
            Parameters::COUNTRY_CODE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_TO_UPPER,
                FilterInput::CONFIGURATION_FILTER_KEY_REGEX_VALIDATE => '/^[a-zA-Z]{3}$/'
            ]),
            Parameters::STATE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false
            ]),
            Parameters::CITY => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false
            ]),
            Parameters::POSTAL_CODE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false
            ])
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of a Customer Id request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getCustomerIdParameter(): array {
        return [
            Parameters::CUSTOMER_ID => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_REGEX_VALIDATE => FilterInput::REGEX_VALIDATE_INT_LIST
            ]),
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of a Role Id request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getRoleIdParameter(): array {
        return [
            Parameters::ROLE_ID => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
            ]),
        ];
    }
    /**
     * This static method returns the predefined filterInput configurations for the parameters of a Job request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getJobParameter(): array {
        return [
            Parameters::JOB => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
            ]),
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of a Redirect request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getRedirectParameter(): array {
        return [
            Parameters::REDIRECT => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
            ]),
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of a Account Id request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getAccountIdParameter(): array {
        return [
            Parameters::ACCOUNT_ID => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
            ]),
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of a Plugin Module request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getPluginModuleParameter(): array {
        return [
            Parameters::PLUGIN_MODULE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
            ]),
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of a Plugin Module request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getPluginModuleCodeParameter(): array {
        return [
            Parameters::PLUGIN_MODULE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
            ]),
            Parameters::CODE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
            ]),
            Parameters::BUYER_TOKEN => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
            ])
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of a selectedOption request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getSelectedOptions(): array {
        return [
            Parameters::OPTION_ID => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_INT
            ])
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of a editPaymentSystem request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getEditPaymentSystemParameters(): array {
        return self::getIdParameter() + [
            Parameters::MODULE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => true,
                FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_HTML_CLEAR
            ]),
            Parameters::PROPERTY => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => true,
                FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_HTML_CLEAR
            ]),
            Parameters::ADDITIONAL_DATA => new FilterInput([
                FilterInput::CONFIGURATION_NO_FILTER => true
            ])
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of a setDelivery request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getSetDeliveryParameters(): array {
        return [
            Parameters::SHIPMENTS => new FilterInput([
                FilterInput::CONFIGURATION_NO_FILTER => true
            ]),
            Parameters::DELIVERY_HASH => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => true,
                FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_HTML_CLEAR
            ]),
            Parameters::TYPE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_TO_UPPER,
                FilterInput::CONFIGURATION_FILTER_KEY_FUNCTION_VALIDATOR => function ($value) {
                    return $value === DeliveryType::SHIPPING || $value === DeliveryType::PICKING;
                }
            ]),
            Parameters::PROVIDER_PICKUP_POINT_HASH => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => true,
                FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_HTML_CLEAR
            ]),
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for UserName parameter.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getUserNameParameter(): array {
        return [
            Parameters::USERNAME => FormFactory::getUserKeyElement()->getFilterInput()
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of a setCurrency request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getSetCurrencyParameters(): array {
        return [
            Parameters::CODE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => true,
                FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_HTML_CLEAR
            ])
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of a newsLetter request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getNewsletterParameters(): array {
        return self::getEmailParameter();
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of a setAccount request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getSetAccount(): array {
        return self::getUserNameParameter() + [
            Parameters::PASSWORD => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => true,
                FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_HTML_CLEAR
            ])
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of a checkoutNextStep request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getCheckoutNextStep(): array {
        return [
            Parameters::UPDATE_BASKET_ROWS => new FilterInput([
                FilterInput::CONFIGURATION_NO_FILTER => true
            ]),
            Parameters::ACTION => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_TO_UPPER,
                FilterInput::CONFIGURATION_FILTER_KEY_FUNCTION_VALIDATOR => 'SDK\Enums\RouteType::isValid'
            ]),
            Parameters::COMMENT => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => true,
                FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_HTML_CLEAR
            ]),
            Parameters::INVOICE_NAME => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => true,
                FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_HTML_CLEAR
            ]),
            Parameters::INVOICE_TYPE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => true,
                FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_HTML_CLEAR
            ]),
            Parameters::FAPIAO_ACTIVED => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_INT
            ]),
            Parameters::DELIVERY_DATE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => true,
                FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_HTML_CLEAR
            ]),
            Parameters::DELIVERY_HOUR => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => true,
                FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_HTML_CLEAR
            ])
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of a one step checkout "nextStep" request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getOSCNextStep(): array {
        return [
            Parameters::OSC => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => true,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_BOOLEAN
            ]),
            Parameters::USER_FORM => new FilterInput([
                FilterInput::CONFIGURATION_NO_FILTER => true
            ])
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters CustomTags.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getCustomTagsParameter(): array {
        return [
            Parameters::CUSTOM_TAGS => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false
            ])
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of a data request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getDataParameters(): array {
        return [
            Parameters::DATA => new FilterInput([
                FilterInput::CONFIGURATION_NO_FILTER => true
            ])
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of a editQuantity request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getEditQuantityParameters(): array {
        return [
            Parameters::QUANTITY => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_INT
            ]),
            Parameters::TYPE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_TO_UPPER,
                FilterInput::CONFIGURATION_FILTER_KEY_FUNCTION_VALIDATOR => 'SDK\Enums\BasketRowType::isValid'
            ]),
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of an addVoucher request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getVoucherCodeParameters(): array {
        return [
            Parameters::CODE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => true,
                FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_HTML_CLEAR
            ]),
            Parameters::MODE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => true,
                FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_TO_UPPER,
                FilterInput::CONFIGURATION_FILTER_KEY_FUNCTION_VALIDATOR => function ($value) {
                    return $value === self::ADD || $value === self::DELETE;
                }
            ]),
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of an LockedStockRenew request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getLockedStockRenewParameters(): array {
        return [
            Parameters::U_ID => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false
            ]),
            Parameters::EXPIRES_AT_EXTEND_MINUTES => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_INT
            ]),
            Parameters::EXPIRES_AT_EXTEND_MINUTES_UPON_USER_REQUEST => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => true,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_BOOLEAN
            ]),
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of an redeem reward points request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getRedeemRewardPointsParameters(): array {
        return
            self::getIdParameter() + [
                Parameters::VALUE => new FilterInput([
                    FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                    FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_INT
                ]),
            ];
    }

    public static function getAddressTypeParameter(): array {
        return [
            Parameters::TYPE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_TO_LOWER,
                FilterInput::CONFIGURATION_FILTER_KEY_FUNCTION_VALIDATOR => function ($value) {
                    return $value === SetUserTypeForms::BILLING || $value === SetUserTypeForms::SHIPPING;
                }
            ])
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of a setAddressBook request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getSetAddressBookParameters(): array {
        return
            FilterInputFactory::getIdParameter() +
            self::getAddressTypeParameter() +
            self::getLocationListParameters() +
            self::getStateCityPostalParameters() +
            [
                Parameters::ACTION => new FilterInput([
                    FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                    FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_TO_LOWER
                ])
            ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of a setCompanyDivision request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getSetCompanyDivisionParameters(): array {
        return [
            Parameters::DATA => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false
            ])
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of a Locations List request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getLocationListParameters(): array {
        $result = [];

        $result[Parameters::LOCATION_LIST] = new FilterInput([
            FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
            FilterInput::CONFIGURATION_FILTER_KEY_REGEX_VALIDATE => FilterInput::REGEX_VALIDATE_INT_LIST
        ]);

        foreach (
            [
                UserType::BUSINESS,
                UserType::FREELANCE,
                UserType::PARTICULAR
            ] as $type
        ) {
            foreach (
                [
                    SetUserTypeForms::SHIPPING,
                    SetUserTypeForms::USER,
                    SetUserTypeForms::BILLING
                ] as $ut
            ) {
                $result[$type . '_' . $ut . '_' . Parameters::LOCATION_LIST] = $result[Parameters::LOCATION_LIST];
            }
        }

        return $result;
    }

    /**
     * This static method returns the predefined filterInput configurations for the FieldName parameter.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getFieldNameParameter(): array {
        return [
            Parameters::FIELD_NAME => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => true,
                FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_HTML_CLEAR
            ])
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the PDF parameter.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getPdfParameter(): array {
        return [
            Parameters::PDF => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_INT
            ])
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the from and to date parameters.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getFromToDateParameters(): array {
        return [
            Parameters::FROM_DATE => new FilterInput([
                FilterInput::CONFIGURATION_DATE_TIME_FORMAT => \DateTime::ATOM
            ]),
            Parameters::TO_DATE => new FilterInput([
                FilterInput::CONFIGURATION_DATE_TIME_FORMAT => \DateTime::ATOM
            ])
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of a ConfirmOrder request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getConfirmOrderParameters(): array {
        return [
            Parameters::ORDER_ID => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_REGEX_VALIDATE => FilterInput::REGEX_VALIDATE_INT_LIST
            ]),
            Parameters::TRANSACTION_ID => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_REGEX_VALIDATE => FilterInput::REGEX_VALIDATE_INT_LIST
            ]),
            Parameters::TYPE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false
            ]),
            Parameters::TOKEN => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false
            ])
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of a DeniedOrder request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getDeniedOrderParameters(): array {
        return [
            Parameters::CODE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_TO_UPPER,
                FilterInput::CONFIGURATION_FILTER_KEY_FUNCTION_VALIDATOR => function ($value) {
                    return ($value === 'A01000-ENDORDER' || $value === 'A01000-INVALID_REQUEST_BODY' || $value === 'ALLOW_DIFFERENT_COUNTRIES_ON_BILLING_AND_SHIPPING_ADDRESS_COUNTRY_ERROR');
                }
            ]),
            Parameters::FIELDS => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
            ]),
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of a Blog RSS request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getBlogRssParameters(): array {
        return self::getPaginableItemsParameter() + self::getSortableItemsParameters('BlogPostSort') + self::getIdParameter() + self::getFromToDateParameters() + self::getPIdParameter() + [
            Parameters::ID_LIST => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_REGEX_VALIDATE => FilterInput::REGEX_VALIDATE_INT_LIST,
                FilterInput::CONFIGURATION_FILTER_KEY_FUNCTION_VALIDATOR_EXPLONDE_PARAM_SEPARATOR => ','
            ]),
            Parameters::TAG_ID => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_REGEX_VALIDATE => FilterInput::REGEX_VALIDATE_INT_LIST
            ]),
            Parameters::BLOGGER_ID => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_REGEX_VALIDATE => FilterInput::REGEX_VALIDATE_INT_LIST
            ]),
            Parameters::CATEGORY_ID => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_REGEX_VALIDATE => FilterInput::REGEX_VALIDATE_INT_LIST
            ]),
            Parameters::Q => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false
            ]),
            Parameters::RANDOM_ITEMS => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_REGEX_VALIDATE => FilterInput::REGEX_VALIDATE_INT_LIST
            ])
        ];
    }

    /**
     * This static method returns the predefined filterInput for the Customize Js request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     * In this case the array only contains a position 'ID' with its corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getCustomizeJsParameters(): array {
        return self::getIdParameter() + [
            Parameters::TYPE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_TO_UPPER
            ])
        ];
    }

    /**
     * This static method returns the predefined filterInput for the resource RelatedItems request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     * In this case the array only contains a position 'ID' with its corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getResourceRelatedItemsParameters(): array {
        return
            self::getIdParameter() +
            self::getPIdParameter() +
            [
                Parameters::SERVICE => new FilterInput([
                    FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                    FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_TO_LOWER,
                    FilterInput::CONFIGURATION_FILTER_KEY_FUNCTION_VALIDATOR => function ($value) {
                        return $value === Services::BLOG || $value === Services::CATEGORY || $value === Services::BLOG || $value === Services::PAGE;
                    }
                ]),
                Parameters::TYPE => new FilterInput([
                    FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                    FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_TO_LOWER,
                    FilterInput::CONFIGURATION_FILTER_KEY_FUNCTION_VALIDATOR => function ($value) {
                        if (strlen(trim($value)) !== 0) {
                            if (!RelatedItemsType::isValid($value)) {
                                return false;
                            }
                        }
                        return true;
                    }
                ]),
                Parameters::POSITION => new FilterInput([
                    FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                    FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_INT
                ]),
                Parameters::POSITION_LIST => new FilterInput([
                    FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                    FilterInput::CONFIGURATION_FILTER_KEY_REGEX_VALIDATE => FilterInput::REGEX_VALIDATE_INT_LIST
                ]),
                Parameters::LIMIT => new FilterInput([
                    FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                    FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_INT
                ]),
                Parameters::CATEGORY_PRODUCTS => new FilterInput([
                    FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                    FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_BOOLEAN
                ]),
            ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of a PhysicalLocation request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getPhysicalLocationParameters(): array {
        return [
            Parameters::COUNTRY_CODE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_TO_UPPER,
                FilterInput::CONFIGURATION_FILTER_KEY_REGEX_VALIDATE => '/^[a-zA-Z]{2}$/'
            ]),
            Parameters::LOCATION_ID => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_INT
            ]),
            Parameters::LATITUDE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_INT
            ]),
            Parameters::LONGITUDE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_INT
            ]),
            Parameters::PER_PAGE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_INT
            ]),
            Parameters::PAGE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_INT
            ]),
            Parameters::VISIBLE_ON_MAP => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => true,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_BOOLEAN
            ]),
            Parameters::DELIVERY_POINT => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => true,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_BOOLEAN
            ]),
            Parameters::RETURN_POINT => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => true,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_BOOLEAN
            ])
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of a PickingDeliveryPoints request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getPickingDeliveryPointsParameters(): array {
        return [
            Parameters::COUNTRY_CODE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_TO_UPPER,
                FilterInput::CONFIGURATION_FILTER_KEY_REGEX_VALIDATE => '/^[a-zA-Z]{2}$/'
            ]),
            Parameters::LOCATION_ID => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_INT
            ]),
            Parameters::LATITUDE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_INT
            ]),
            Parameters::LONGITUDE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_INT
            ]),
            Parameters::RADIUS => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_FLOAT
            ]),
            Parameters::PER_PAGE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_INT
            ]),
            Parameters::PAGE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_INT
            ]),
            Parameters::VISIBLE_ON_MAP => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => true,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_BOOLEAN
            ]),
            Parameters::DELIVERY_POINT => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => true,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_BOOLEAN
            ]),
            Parameters::RETURN_POINT => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => true,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_BOOLEAN
            ]),
            Parameters::CITY => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false
            ]),
            Parameters::POSTAL_CODE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false
            ]),
            Parameters::PICKUP_POINT_PROVIDER_ID => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_INT
            ]),
            Parameters::ID_LIST => self::getIdListFilterInput()
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of a send mail request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getSendMailParameters(): array {
        return [
            Parameters::TYPE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_HTML_CLEAR
            ]),
            Parameters::ATTACHMENTS => new FilterInput([
                FilterInput::CONFIGURATION_NO_FILTER => true
            ])
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of a send mail attachment.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getSendMailAttachmentParameters(): array {
        return [
            Parameters::FILE_NAME => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false
            ]),
            Parameters::DATA => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false
            ])
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of a expressCheckout request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getExpressCheckout(): array {
        return
            FilterInputFactory::getIdParameter() +
            [
                Parameters::ACTION => new FilterInput([
                    FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                    FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_TO_LOWER
                ])
            ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of a PluginExecute request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getPluginExecute(): array {
        return
            FilterInputFactory::getIdParameter() +
            [
                Parameters::EVENT => new FilterInput([
                    FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                    FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_TO_UPPER,
                    FilterInput::CONFIGURATION_FILTER_KEY_FUNCTION_VALIDATOR => 'SDK\Enums\PluginEvents::isValid'
                ]),
                Parameters::DATA => new FilterInput([
                    FilterInput::CONFIGURATION_NO_FILTER => true
                ])
            ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of a Registered User Id request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getRegisteredUserIdParameter(): array {
        return [
            Parameters::REGISTERED_USER_ID => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_REGEX_VALIDATE => FilterInput::REGEX_VALIDATE_INT_LIST
            ]),
        ];
    }

    /**
     * This static method returns the predefined filterInput configurations for the parameters of a Registered User Id request.
     * Returns an array where the key of each position is the name of the parameter and the value of the posisiton is the corresponding properly initialized filterInput.
     *
     * @return array
     */
    public static function getOrdersApprovalDecisionParameter(): array {
        return [
            Parameters::ORDER_ID => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_REGEX_VALIDATE => FilterInput::REGEX_VALIDATE_INT_LIST
            ]),
            Parameters::DECISION => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false
            ]),
        ];
    }
}
