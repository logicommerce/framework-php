<?php

namespace FWK\Core\Theme;

use SDK\Core\Enums\SortableEnum;
use SDK\Enums\OptionsPriceMode;
use SDK\Enums\BlogPostSort;
use SDK\Enums\ProductSearchDeep;
use SDK\Enums\ProductSearchType;
use SDK\Enums\ProductSort;
use SDK\Enums\UserType;
use SDK\Services\Parameters\Groups\AreaCategoriesTreeParametersGroup;
use FWK\Core\Resources\Response;
use FWK\Core\Theme\Dtos\ApplicableFilter;
use FWK\Core\Theme\Dtos\ApplicableFilters;
use FWK\Core\Theme\Dtos\Blog;
use FWK\Core\Theme\Dtos\Configuration;
use FWK\Core\Theme\Dtos\FormComments;
use FWK\Core\Theme\Dtos\Forms;
use FWK\Core\Theme\Dtos\FormSetUser;
use FWK\Core\Theme\Dtos\FormSetUserFields;
use FWK\Core\Theme\Dtos\FormSetUserFieldsByUserType;
use FWK\Core\Theme\Dtos\FormSetUserFieldsByUserTypeElement;
use FWK\Core\Theme\Dtos\ItemList;
use FWK\Core\Theme\Dtos\News;
use FWK\Core\Theme\Dtos\Pages;
use FWK\Core\Theme\Dtos\Pagination;
use FWK\Core\Theme\Dtos\ProductList;
use FWK\Core\Theme\Dtos\Search;
use FWK\Core\Theme\Dtos\SearchItem;
use FWK\Core\Theme\Dtos\ViewOption;
use FWK\Core\Theme\Dtos\ViewOptionPerPage;
use FWK\Core\Theme\Dtos\ViewOptions;
use FWK\Core\Theme\Dtos\ViewOptionSort;
use FWK\Core\Theme\Dtos\ViewOptionSortItem;
use FWK\Core\Theme\Dtos\ViewOptionSortItems;
use FWK\Core\Theme\Dtos\ViewOptionTemplate;
use FWK\Enums\Parameters;
use FWK\Enums\TcDataItems;
use FWK\Core\Theme\Dtos\FormFieldsSetUser;
use FWK\Core\Theme\Dtos\FormField;
use FWK\Core\Theme\Dtos\User;
use SDK\Services\Parameters\Groups\User\UserVouchersParametersGroup;
use SDK\Services\Parameters\Groups\User\UserCustomTagsParametersGroup;
use FWK\Core\Resources\RoutePaths;
use FWK\Core\Theme\Dtos\Account;
use FWK\Core\Theme\Dtos\Basket;
use FWK\Core\Theme\Dtos\BlogPostViewOptionSort;
use FWK\Core\Theme\Dtos\BlogPostViewOptionSortItems;
use FWK\Core\Theme\Dtos\Commerce;
use FWK\Core\Theme\Dtos\CommerceLockedStock;
use FWK\Core\Theme\Dtos\CompanyRolesViewOptionSort;
use FWK\Core\Theme\Dtos\CompanyRolesViewOptionSortItems;
use FWK\Core\Theme\Dtos\DataValidators;
use FWK\Core\Theme\Dtos\Discounts;
use FWK\Core\Theme\Dtos\DiscountsViewOptionSort;
use FWK\Core\Theme\Dtos\DiscountsViewOptionSortItems;
use FWK\Core\Theme\Dtos\FormAccountTabPane;
use FWK\Core\Theme\Dtos\FormCompanyDivision;
use FWK\Core\Theme\Dtos\FormContact;
use FWK\Core\Theme\Dtos\FormElement;
use FWK\Core\Theme\Dtos\FormElementButton;
use FWK\Core\Theme\Dtos\FormElementInput;
use FWK\Core\Theme\Dtos\FormElements;
use FWK\Core\Theme\Dtos\FormFieldsContact;
use FWK\Core\Theme\Dtos\FormFieldsProductContact;
use FWK\Core\Theme\Dtos\FormFieldsShoppingList;
use FWK\Core\Theme\Dtos\FormFieldsShoppingListRowNote;
use FWK\Core\Theme\Dtos\FormCompanyDivisionGeneral;
use FWK\Core\Theme\Dtos\FormCompanyDivisionGeneralFields;
use FWK\Core\Theme\Dtos\FormCompanyDivisionInvoicingFields;
use FWK\Core\Theme\Dtos\FormAccount;
use FWK\Core\Theme\Dtos\FormAccountFields;
use FWK\Core\Theme\Dtos\FormCompanyRoles;
use FWK\Core\Theme\Dtos\FormCompanyRolesFields;
use FWK\Core\Theme\Dtos\FormMaster;
use FWK\Core\Theme\Dtos\FormProductContact;
use FWK\Core\Theme\Dtos\FormRegisteredUser;
use FWK\Core\Theme\Dtos\FormRegisteredUserApproveFields;
use FWK\Core\Theme\Dtos\FormRegisteredUserFields;
use FWK\Core\Theme\Dtos\FormAccountRegisteredUserFields;
use FWK\Core\Theme\Dtos\FormAccountTabPaneField;
use FWK\Core\Theme\Dtos\FormShoppingList;
use FWK\Core\Theme\Dtos\FormShoppingListRowNote;
use FWK\Core\Theme\Dtos\FormUseCaptcha;
use FWK\Core\Theme\Dtos\NewsViewOptionSort;
use FWK\Core\Theme\Dtos\NewsViewOptionSortItems;
use FWK\Core\Theme\Dtos\OrderList;
use FWK\Core\Theme\Dtos\RegisteredUsersViewOptionSort;
use FWK\Core\Theme\Dtos\RegisteredUsersViewOptionSortItems;
use FWK\Core\Theme\Dtos\OrdersViewOptionSort;
use FWK\Core\Theme\Dtos\OrdersViewOptionSortItems;
use FWK\Core\Theme\Dtos\SalesAgentCustomers;
use FWK\Core\Theme\Dtos\ShoppingList;
use FWK\Core\Theme\Dtos\ShoppingListViewOptionSort;
use FWK\Core\Theme\Dtos\ShoppingListViewOptionSortItems;
use FWK\Enums\ApplicableFilterPriceRangeMode;
use FWK\Enums\RouteType;
use FWK\Enums\RouteTypes\InternalUser;
use SDK\Enums\CompanyRolesSort;
use SDK\Enums\DiscountSort;
use SDK\Enums\GeneralRestriction;
use SDK\Enums\NewsSort;
use SDK\Enums\OrderSort;
use SDK\Enums\RegisteredUserSort;
use SDK\Enums\ShoppingListsSort;

/**
 * This is the main Tc (theme configuration) class.
 * The purpose of this class is to represent a theme configuration.
 * This class implements FWK\Core\Theme\TcInterface, see this interface.
 *
 * @abstract
 *
 * @see Tc::getConfiguration()
 * @see Tc::getConfigurationData()
 * @see Tc::runForbiddenResponse()
 *
 * @see TCInterface
 *
 * @package FWK\Core\Theme
 */
abstract class Tc implements TcInterface {

    protected static ?Configuration $configuration = null;

    /**
     * This method returns the 'theme configuration' data, encapsulated in a Configuration object.
     *
     * @return Configuration
     *
     * @see Configuration
     */
    public function getConfiguration(): Configuration {
        if (is_null(self::$configuration)) {
            self::$configuration = new Configuration($this->getConfigurationData());
        }
        return self::$configuration;
    }

    /**
     * This method unsets the Configuration instance (singleton).
     *
     * @return void
     */
    final public static function resetInstance(): void {
        self::$configuration = null;
    }

    /**
     * This method returns the 'theme configuration' data, in array format.
     *
     * @return array
     */
    public function getConfigurationData(): array {
        $defaultPagination = [
            Pagination::PAGES_TO_SHOW => 5,
            Pagination::BEFORE_CLASS => 'arrow first',
            Pagination::BEFORE_LABEL => '<<',
            Pagination::PAGE_CLASS => '',
            Pagination::PAGE_LABEL => '- {{pageNumber}} -',
            Pagination::AFTER_CLASS => 'arrow last',
            Pagination::AFTER_LABEL => '>>',
            Pagination::SEPARATOR_CLASS => 'unavailable disabled',
            Pagination::SEPARATOR_LABEL => '...',
            Pagination::SELECTED_CLASS => 'current active',
            Pagination::LINK_TARGET => '_self',
            Pagination::LINK_IS_FOLLOW => true,
        ];

        $defaultCompanyRolesViewOptions = $defaultRegisteredUsersViewOptions = $defaultOrdersViewOptions = $defaultDiscountsViewOptions = $defaultBlogViewOptions = $defaultNewsViewOptions = $defaultShoppingListViewOptions = $defaultViewOptions = [
            ViewOptions::TEMPLATE => [
                ViewOption::ENABLED => true,
                ViewOption::SHOW_LABEL => true,
                ViewOption::VIEW_PRIORITY => 0,
                ViewOptionTemplate::AVAILABLE_TEMPLATES => [
                    1,
                    2
                ]
            ],
            ViewOptions::PER_PAGE => [
                ViewOption::ENABLED => true,
                ViewOption::SHOW_LABEL => true,
                ViewOption::VIEW_PRIORITY => 1,
                ViewOptionPerPage::AVAILABLE_PAGINATIONS => [
                    9,
                    18,
                    27
                ]
            ],
            ViewOptions::SORT => [
                ViewOption::ENABLED => true,
                ViewOption::SHOW_LABEL => true,
                ViewOption::VIEW_PRIORITY => 2,
                ViewOptionSort::ITEMS => [
                    ViewOptionSortItems::ID => [
                        ViewOptionSortItem::ENABLED => true,
                        ViewOptionSortItem::SHOW_LABEL => true,
                        ViewOptionSortItem::VIEW_PRIORITY => 0,
                        ViewOptionSortItem::ASC => true,
                        ViewOptionSortItem::DESC => true
                        /*
                        ,ViewOptionSortItem::SORTS => 
                            [
                                ViewOptionSortItems::PRIORITY . '.'  . ProductSort::SORT_DIRECTION_ASC,
                                ViewOptionSortItems::NAME . '.'  . ProductSort::SORT_DIRECTION_ASC
                            ]
                        */
                    ],
                    ViewOptionSortItems::PID => [
                        ViewOptionSortItem::ENABLED => true,
                        ViewOptionSortItem::SHOW_LABEL => true,
                        ViewOptionSortItem::VIEW_PRIORITY => 1,
                        ViewOptionSortItem::ASC => true,
                        ViewOptionSortItem::DESC => true
                    ],
                    ViewOptionSortItems::SKU => [
                        ViewOptionSortItem::ENABLED => true,
                        ViewOptionSortItem::SHOW_LABEL => true,
                        ViewOptionSortItem::VIEW_PRIORITY => 2,
                        ViewOptionSortItem::ASC => true,
                        ViewOptionSortItem::DESC => true
                    ],
                    ViewOptionSortItems::NAME => [
                        ViewOptionSortItem::ENABLED => true,
                        ViewOptionSortItem::SHOW_LABEL => true,
                        ViewOptionSortItem::VIEW_PRIORITY => 3,
                        ViewOptionSortItem::ASC => true,
                        ViewOptionSortItem::DESC => true
                    ],
                    ViewOptionSortItems::PRIORITY => [
                        ViewOptionSortItem::ENABLED => true,
                        ViewOptionSortItem::SHOW_LABEL => true,
                        ViewOptionSortItem::VIEW_PRIORITY => 4,
                        ViewOptionSortItem::ASC => true,
                        ViewOptionSortItem::DESC => true
                    ],
                    ViewOptionSortItems::PRICE => [
                        ViewOptionSortItem::ENABLED => true,
                        ViewOptionSortItem::SHOW_LABEL => true,
                        ViewOptionSortItem::VIEW_PRIORITY => 5,
                        ViewOptionSortItem::ASC => true,
                        ViewOptionSortItem::DESC => true
                    ],
                    ViewOptionSortItems::OFFER => [
                        ViewOptionSortItem::ENABLED => true,
                        ViewOptionSortItem::SHOW_LABEL => true,
                        ViewOptionSortItem::VIEW_PRIORITY => 6,
                        ViewOptionSortItem::ASC => true,
                        ViewOptionSortItem::DESC => true
                    ],
                    ViewOptionSortItems::FEATURED => [
                        ViewOptionSortItem::ENABLED => true,
                        ViewOptionSortItem::SHOW_LABEL => true,
                        ViewOptionSortItem::VIEW_PRIORITY => 7,
                        ViewOptionSortItem::ASC => true,
                        ViewOptionSortItem::DESC => true
                    ],
                    ViewOptionSortItems::DATEADDED => [
                        ViewOptionSortItem::ENABLED => true,
                        ViewOptionSortItem::SHOW_LABEL => true,
                        ViewOptionSortItem::VIEW_PRIORITY => 8,
                        ViewOptionSortItem::ASC => true,
                        ViewOptionSortItem::DESC => true
                    ],
                    ViewOptionSortItems::PUBLICATIONDATE => [
                        ViewOptionSortItem::ENABLED => false,
                        ViewOptionSortItem::SHOW_LABEL => true,
                        ViewOptionSortItem::VIEW_PRIORITY => 9,
                        ViewOptionSortItem::ASC => true,
                        ViewOptionSortItem::DESC => true
                    ],
                ]
            ]
        ];

        $defaultShoppingListViewOptions[ViewOptions::SORT][ViewOptionSort::SORT_ITEMS] = ShoppingListViewOptionSort::SORT_ITEMS;
        $defaultShoppingListViewOptions[ViewOptions::SORT][ViewOptionSort::ITEMS] = [
            ShoppingListViewOptionSortItems::NAME => [
                ViewOptionSortItem::ENABLED => true,
                ViewOptionSortItem::SHOW_LABEL => true,
                ViewOptionSortItem::VIEW_PRIORITY => 1,
                ViewOptionSortItem::ASC => true,
                ViewOptionSortItem::DESC => true
            ],
            ShoppingListViewOptionSortItems::PRIORITY => [
                ViewOptionSortItem::ENABLED => true,
                ViewOptionSortItem::SHOW_LABEL => true,
                ViewOptionSortItem::VIEW_PRIORITY => 2,
                ViewOptionSortItem::ASC => true,
                ViewOptionSortItem::DESC => true
            ],
            ShoppingListViewOptionSortItems::ADDEDDATE => [
                ViewOptionSortItem::ENABLED => true,
                ViewOptionSortItem::SHOW_LABEL => true,
                ViewOptionSortItem::VIEW_PRIORITY => 3,
                ViewOptionSortItem::ASC => true,
                ViewOptionSortItem::DESC => true
            ]
        ];

        $defaultBlogViewOptions[ViewOptions::SORT][ViewOptionSort::SORT_ITEMS] = BlogPostViewOptionSort::SORT_ITEMS;
        $defaultBlogViewOptions[ViewOptions::SORT][ViewOptionSort::ITEMS] = [
            BlogPostViewOptionSortItems::ID => [
                ViewOptionSortItem::ENABLED => true,
                ViewOptionSortItem::SHOW_LABEL => true,
                ViewOptionSortItem::VIEW_PRIORITY => 1,
                ViewOptionSortItem::ASC => true,
                ViewOptionSortItem::DESC => true
            ],
            BlogPostViewOptionSortItems::PUBLICATIONDATE => [
                ViewOptionSortItem::ENABLED => true,
                ViewOptionSortItem::SHOW_LABEL => true,
                ViewOptionSortItem::VIEW_PRIORITY => 2,
                ViewOptionSortItem::ASC => true,
                ViewOptionSortItem::DESC => true
            ],
            BlogPostViewOptionSortItems::HITS => [
                ViewOptionSortItem::ENABLED => true,
                ViewOptionSortItem::SHOW_LABEL => true,
                ViewOptionSortItem::VIEW_PRIORITY => 3,
                ViewOptionSortItem::ASC => true,
                ViewOptionSortItem::DESC => true
            ],
            BlogPostViewOptionSortItems::LIKES => [
                ViewOptionSortItem::ENABLED => true,
                ViewOptionSortItem::SHOW_LABEL => true,
                ViewOptionSortItem::VIEW_PRIORITY => 4,
                ViewOptionSortItem::ASC => true,
                ViewOptionSortItem::DESC => true
            ],
            BlogPostViewOptionSortItems::DISLIKES => [
                ViewOptionSortItem::ENABLED => true,
                ViewOptionSortItem::SHOW_LABEL => true,
                ViewOptionSortItem::VIEW_PRIORITY => 5,
                ViewOptionSortItem::ASC => true,
                ViewOptionSortItem::DESC => true
            ],
            BlogPostViewOptionSortItems::VOTES => [
                ViewOptionSortItem::ENABLED => true,
                ViewOptionSortItem::SHOW_LABEL => true,
                ViewOptionSortItem::VIEW_PRIORITY => 6,
                ViewOptionSortItem::ASC => true,
                ViewOptionSortItem::DESC => true
            ],
            BlogPostViewOptionSortItems::RATE => [
                ViewOptionSortItem::ENABLED => true,
                ViewOptionSortItem::SHOW_LABEL => true,
                ViewOptionSortItem::VIEW_PRIORITY => 7,
                ViewOptionSortItem::ASC => true,
                ViewOptionSortItem::DESC => true
            ]
        ];

        $defaultNewsViewOptions[ViewOptions::SORT][ViewOptionSort::SORT_ITEMS] = NewsViewOptionSort::SORT_ITEMS;
        $defaultNewsViewOptions[ViewOptions::SORT][ViewOptionSort::ITEMS] = [
            NewsViewOptionSortItems::PUBLICATIONDATE => [
                ViewOptionSortItem::ENABLED => true,
                ViewOptionSortItem::SHOW_LABEL => true,
                ViewOptionSortItem::VIEW_PRIORITY => 1,
                ViewOptionSortItem::ASC => true,
                ViewOptionSortItem::DESC => true
            ],
            NewsViewOptionSortItems::PRIORITY => [
                ViewOptionSortItem::ENABLED => true,
                ViewOptionSortItem::SHOW_LABEL => true,
                ViewOptionSortItem::VIEW_PRIORITY => 2,
                ViewOptionSortItem::ASC => true,
                ViewOptionSortItem::DESC => true
            ]
        ];

        $defaultOrdersViewOptions[ViewOptions::SORT][ViewOptionSort::SORT_ITEMS] = OrdersViewOptionSort::SORT_ITEMS;
        $defaultOrdersViewOptions[ViewOptions::SORT][ViewOptionSort::ITEMS] = [
            OrdersViewOptionSortItems::DATE => [
                ViewOptionSortItem::ENABLED => true,
                ViewOptionSortItem::SHOW_LABEL => true,
                ViewOptionSortItem::VIEW_PRIORITY => 3,
                ViewOptionSortItem::ASC => true,
                ViewOptionSortItem::DESC => true
            ],
            OrdersViewOptionSortItems::DOCUMENTNUMBER => [
                ViewOptionSortItem::ENABLED => true,
                ViewOptionSortItem::SHOW_LABEL => true,
                ViewOptionSortItem::VIEW_PRIORITY => 1,
                ViewOptionSortItem::ASC => true,
                ViewOptionSortItem::DESC => true
            ],
        ];

        $defaultDiscountsViewOptions[ViewOptions::SORT][ViewOptionSort::SORT_ITEMS] = DiscountsViewOptionSort::SORT_ITEMS;
        $defaultDiscountsViewOptions[ViewOptions::SORT][ViewOptionSort::ITEMS] = [
            DiscountsViewOptionSortItems::DISPLAYPRIORITY => [
                ViewOptionSortItem::ENABLED => true,
                ViewOptionSortItem::SHOW_LABEL => true,
                ViewOptionSortItem::VIEW_PRIORITY => 1,
                ViewOptionSortItem::ASC => true,
                ViewOptionSortItem::DESC => true
            ],
            DiscountsViewOptionSortItems::PRIORITY => [
                ViewOptionSortItem::ENABLED => true,
                ViewOptionSortItem::SHOW_LABEL => true,
                ViewOptionSortItem::VIEW_PRIORITY => 2,
                ViewOptionSortItem::ASC => true,
                ViewOptionSortItem::DESC => true
            ],
            DiscountsViewOptionSortItems::NAME => [
                ViewOptionSortItem::ENABLED => true,
                ViewOptionSortItem::SHOW_LABEL => true,
                ViewOptionSortItem::VIEW_PRIORITY => 3,
                ViewOptionSortItem::ASC => true,
                ViewOptionSortItem::DESC => true
            ],
            // Deprecated, do not use
            DiscountsViewOptionSortItems::EXPIRATIONDATE => [
                ViewOptionSortItem::ENABLED => false,
            ],
            // Deprecated, do not use
            DiscountsViewOptionSortItems::ACTIVATIONDATE => [
                ViewOptionSortItem::ENABLED => false,
            ]
        ];

        $defaultRegisteredUsersViewOptions[ViewOptions::SORT][ViewOptionSort::SORT_ITEMS] = RegisteredUsersViewOptionSort::SORT_ITEMS;
        $defaultRegisteredUsersViewOptions[ViewOptions::SORT][ViewOptionSort::ITEMS] = [
            RegisteredUsersViewOptionSortItems::ID => [
                ViewOptionSortItem::ENABLED => true,
                ViewOptionSortItem::SHOW_LABEL => true,
                ViewOptionSortItem::VIEW_PRIORITY => 1,
                ViewOptionSortItem::ASC => true,
                ViewOptionSortItem::DESC => true
            ],
            RegisteredUsersViewOptionSortItems::FIRSTNAME => [
                ViewOptionSortItem::ENABLED => true,
                ViewOptionSortItem::SHOW_LABEL => true,
                ViewOptionSortItem::VIEW_PRIORITY => 3,
                ViewOptionSortItem::ASC => true,
                ViewOptionSortItem::DESC => true
            ],
            RegisteredUsersViewOptionSortItems::LASTNAME => [
                ViewOptionSortItem::ENABLED => true,
                ViewOptionSortItem::SHOW_LABEL => true,
                ViewOptionSortItem::VIEW_PRIORITY => 3,
                ViewOptionSortItem::ASC => true,
                ViewOptionSortItem::DESC => true
            ],
            RegisteredUsersViewOptionSortItems::EMAIL => [
                ViewOptionSortItem::ENABLED => true,
                ViewOptionSortItem::SHOW_LABEL => true,
                ViewOptionSortItem::VIEW_PRIORITY => 3,
                ViewOptionSortItem::ASC => true,
                ViewOptionSortItem::DESC => true
            ],
            RegisteredUsersViewOptionSortItems::USERNAME => [
                ViewOptionSortItem::ENABLED => true,
                ViewOptionSortItem::SHOW_LABEL => true,
                ViewOptionSortItem::VIEW_PRIORITY => 3,
                ViewOptionSortItem::ASC => true,
                ViewOptionSortItem::DESC => true
            ],
            RegisteredUsersViewOptionSortItems::PID => [
                ViewOptionSortItem::ENABLED => true,
                ViewOptionSortItem::SHOW_LABEL => true,
                ViewOptionSortItem::VIEW_PRIORITY => 3,
                ViewOptionSortItem::ASC => true,
                ViewOptionSortItem::DESC => true
            ],
            RegisteredUsersViewOptionSortItems::DATEADDED => [
                ViewOptionSortItem::ENABLED => true,
                ViewOptionSortItem::SHOW_LABEL => true,
                ViewOptionSortItem::VIEW_PRIORITY => 3,
                ViewOptionSortItem::ASC => true,
                ViewOptionSortItem::DESC => true
            ]
        ];

        $defaultCompanyRolesViewOptions[ViewOptions::SORT][ViewOptionSort::SORT_ITEMS] = CompanyRolesViewOptionSort::SORT_ITEMS;
        $defaultCompanyRolesViewOptions[ViewOptions::SORT][ViewOptionSort::ITEMS] = [
            CompanyRolesViewOptionSortItems::ID => [
                ViewOptionSortItem::ENABLED => true,
                ViewOptionSortItem::SHOW_LABEL => true,
                ViewOptionSortItem::VIEW_PRIORITY => 1,
                ViewOptionSortItem::ASC => true,
                ViewOptionSortItem::DESC => true
            ],
            CompanyRolesViewOptionSortItems::NAME => [
                ViewOptionSortItem::ENABLED => true,
                ViewOptionSortItem::SHOW_LABEL => true,
                ViewOptionSortItem::VIEW_PRIORITY => 3,
                ViewOptionSortItem::ASC => true,
                ViewOptionSortItem::DESC => true
            ],
        ];

        $productListApplicableFilters = [
            ApplicableFilters::CATEGORY_ID_LIST => [
                ApplicableFilter::ENABLED => true,
                ApplicableFilter::ORDER_BY => SortableEnum::SORT_DIRECTION_ASC,
                ApplicableFilter::SORT => ''
            ],
            ApplicableFilters::BRANDS_LIST => [
                ApplicableFilter::ENABLED => true,
                ApplicableFilter::ORDER_BY => SortableEnum::SORT_DIRECTION_ASC,
                ApplicableFilter::SORT => ''
            ],
            ApplicableFilters::FILTER_CUSTOMTAG => [
                ApplicableFilter::ENABLED => true,
                ApplicableFilter::ORDER_BY => SortableEnum::SORT_DIRECTION_ASC,
                ApplicableFilter::SORT => '',
                ApplicableFilter::ITEMS_TO_SHOW => [],
                ApplicableFilter::ITEMS_TO_RANGE_SLIDER => []
            ],
            ApplicableFilters::FILTER_CUSTOMTAG_GROUP => [
                ApplicableFilter::ENABLED => true,
                ApplicableFilter::ORDER_BY => SortableEnum::SORT_DIRECTION_ASC,
                ApplicableFilter::SORT => '',
                ApplicableFilter::ITEMS_TO_SHOW => []
            ],
            ApplicableFilters::FILTER_OPTION => [
                ApplicableFilter::ENABLED => true,
                ApplicableFilter::ORDER_BY => SortableEnum::SORT_DIRECTION_ASC
            ],
            ApplicableFilters::PRICE_RANGE => [
                ApplicableFilter::ENABLED => true,
                ApplicableFilter::PRICE_RANGE_MODE => ApplicableFilterPriceRangeMode::MODE_RANGE_SLIDER
            ],
            ApplicableFilters::Q => [
                ApplicableFilter::ENABLED => false,
                ApplicableFilter::Q_MIN_CHARACTERS => 3
            ]
        ];

        $defaultParametersValues = [
            Parameters::TEMPLATE => $defaultViewOptions[ViewOptions::TEMPLATE][ViewOptionTemplate::AVAILABLE_TEMPLATES][0],
            Parameters::PER_PAGE => $defaultViewOptions[ViewOptions::PER_PAGE][ViewOptionPerPage::AVAILABLE_PAGINATIONS][0],
            Parameters::SORT => ProductSort::PRIORITY . '.' . ProductSort::SORT_DIRECTION_ASC . ',' . ProductSort::NAME . '.' . ProductSort::SORT_DIRECTION_ASC
        ];

        $productsListRequestParameters = [
            Parameters::Q_DEEP => ProductSearchDeep::SHORT,
            Parameters::Q_TYPE => ProductSearchType::COMPLETE,
            // Parameters::CUSTOMTAGS_SEARCH_TYPE => CustomTagsSearchType::COMPLETE,
            Parameters::OPTION_PRICE_MODE => OptionsPriceMode::CHEAPEST
        ];

        $siteMapPageParameterGroup = new AreaCategoriesTreeParametersGroup();
        $siteMapPageParameterGroup->setPerPage(9);
        $siteMapPageParameterGroup->setLevels(3);

        $userVouchersParametersGroup = new UserVouchersParametersGroup();
        $userVouchersParametersGroup->setPerPage(8);

        $userCustomTagsParametersGroup = new UserCustomTagsParametersGroup();
        $userCustomTagsParametersGroup->setPerPage(20);

        return [
            Configuration::SEARCH => [
                Search::PRODUCTS => [
                    SearchItem::ACTIVED => false,
                    SearchItem::LIST => [
                        ItemList::REQUEST_PARAMETERS => $productsListRequestParameters,
                        ItemList::PAGINATION => $defaultPagination,
                        ItemList::VIEW_OPTIONS => $defaultViewOptions,
                        ItemList::APPLICABLE_FILTERS => $productListApplicableFilters + [
                            Parameters::ONLY_OFFERS => [
                                ApplicableFilter::ENABLED => true
                            ],
                            Parameters::ONLY_FEATURED => [
                                ApplicableFilter::ENABLED => true
                            ]
                        ],
                        ItemList::DEFAULT_PARAMETERS_VALUES => $defaultParametersValues
                    ]
                ],
                Search::CATEGORIES => [
                    SearchItem::ACTIVED => false
                ],
                Search::NEWS => [
                    SearchItem::ACTIVED => false
                ],
                Search::PAGES => [
                    SearchItem::ACTIVED => false
                ]
            ],
            Configuration::CATEGORY => [
                ProductList::PRODUCT_LIST => [
                    ItemList::VIEW_OPTIONS => $defaultViewOptions,
                    ItemList::REQUEST_PARAMETERS => $productsListRequestParameters,
                    ItemList::APPLICABLE_FILTERS => $productListApplicableFilters + [
                        Parameters::ONLY_OFFERS => [
                            ApplicableFilter::ENABLED => true
                        ],
                        Parameters::ONLY_FEATURED => [
                            ApplicableFilter::ENABLED => true
                        ]
                    ],
                    ItemList::DEFAULT_PARAMETERS_VALUES => $defaultParametersValues,
                    ItemList::PAGINATION => $defaultPagination
                ]
            ],
            Configuration::BRAND => [
                ProductList::PRODUCT_LIST => [
                    ItemList::VIEW_OPTIONS => $defaultViewOptions,
                    ItemList::REQUEST_PARAMETERS => $productsListRequestParameters,
                    ItemList::APPLICABLE_FILTERS => $productListApplicableFilters + [
                        Parameters::ONLY_OFFERS => [
                            ApplicableFilter::ENABLED => true
                        ],
                        Parameters::ONLY_FEATURED => [
                            ApplicableFilter::ENABLED => true
                        ]
                    ],
                    ItemList::DEFAULT_PARAMETERS_VALUES => $defaultParametersValues,
                    ItemList::PAGINATION => $defaultPagination
                ]
            ],
            Configuration::BLOG => [
                Blog::POST_LIST => [
                    ItemList::VIEW_OPTIONS => $defaultBlogViewOptions,
                    ItemList::REQUEST_PARAMETERS => $productsListRequestParameters + [
                        Parameters::INCLUDE_SUBCATEGORIES => true
                    ],
                    ItemList::DEFAULT_PARAMETERS_VALUES => [
                        Parameters::SORT => BlogPostSort::PUBLICATIONDATE . '.' . BlogPostSort::SORT_DIRECTION_DESC
                    ] + $defaultParametersValues,
                    ItemList::PAGINATION => $defaultPagination
                ]
            ],
            Configuration::OFFERS => [
                ProductList::PRODUCT_LIST => [
                    ItemList::VIEW_OPTIONS => $defaultViewOptions,
                    ItemList::REQUEST_PARAMETERS => $productsListRequestParameters + [
                        Parameters::ONLY_OFFERS => true
                    ],
                    ItemList::APPLICABLE_FILTERS => $productListApplicableFilters + [
                        Parameters::ONLY_FEATURED => [
                            ApplicableFilter::ENABLED => true
                        ]
                    ],
                    ItemList::DEFAULT_PARAMETERS_VALUES => $defaultParametersValues,
                    ItemList::PAGINATION => $defaultPagination
                ]
            ],
            Configuration::FEATURED => [
                ProductList::PRODUCT_LIST => [
                    ItemList::VIEW_OPTIONS => $defaultViewOptions,
                    ItemList::REQUEST_PARAMETERS => $productsListRequestParameters + [
                        Parameters::ONLY_FEATURED => true
                    ],
                    ItemList::APPLICABLE_FILTERS => $productListApplicableFilters + [
                        Parameters::ONLY_OFFERS => [
                            ApplicableFilter::ENABLED => true
                        ]
                    ],
                    ItemList::DEFAULT_PARAMETERS_VALUES => $defaultParametersValues,
                    ItemList::PAGINATION => $defaultPagination
                ]
            ],
            Configuration::NEWS => [
                News::NEWS_LIST => [
                    ItemList::VIEW_OPTIONS => $defaultNewsViewOptions,
                    ItemList::DEFAULT_PARAMETERS_VALUES => [
                        Parameters::SORT => NewsSort::PUBLICATIONDATE . '.' . NewsSort::SORT_DIRECTION_DESC
                    ] + $defaultParametersValues,
                    ItemList::PAGINATION => $defaultPagination
                ]
            ],
            Configuration::SHOPPING_LIST => [
                ShoppingList::ROWS_LIST => [
                    ItemList::VIEW_OPTIONS => $defaultShoppingListViewOptions,
                    ItemList::DEFAULT_PARAMETERS_VALUES => [
                        Parameters::PER_PAGE => 10,
                        Parameters::SORT => ShoppingListViewOptionSortItems::ADDEDDATE . '.' . ShoppingListsSort::SORT_DIRECTION_DESC
                    ],
                    ItemList::PAGINATION => $defaultPagination,
                    ItemList::APPLICABLE_FILTERS => [
                        Parameters::Q => [
                            ApplicableFilter::ENABLED => true
                        ]
                    ],
                ]
            ],
            Configuration::ORDER_LIST => [
                OrderList::ROWS_LIST => [
                    ItemList::VIEW_OPTIONS => $defaultOrdersViewOptions,
                    ItemList::DEFAULT_PARAMETERS_VALUES => [
                        Parameters::PER_PAGE => 25,
                        Parameters::SORT => OrderSort::DATE . '.' . OrderSort::SORT_DIRECTION_DESC
                    ],
                    ItemList::PAGINATION => $defaultPagination,
                ],
            ],
            Configuration::DISCOUNTS => [
                Discounts::DISCOUNTS_LIST => [
                    ItemList::VIEW_OPTIONS => $defaultDiscountsViewOptions,
                    ItemList::DEFAULT_PARAMETERS_VALUES => [
                        Parameters::SORT => DiscountSort::NAME . '.' . DiscountSort::SORT_DIRECTION_DESC
                    ] + $defaultParametersValues,
                    ItemList::PAGINATION => $defaultPagination
                ],
                ProductList::PRODUCT_LIST => [
                    ItemList::VIEW_OPTIONS => $defaultViewOptions,
                    ItemList::APPLICABLE_FILTERS => $productListApplicableFilters + [
                        Parameters::ONLY_OFFERS => [
                            ApplicableFilter::ENABLED => true
                        ]
                    ],
                    ItemList::DEFAULT_PARAMETERS_VALUES => $defaultParametersValues,
                    ItemList::PAGINATION => $defaultPagination
                ]
            ],
            Configuration::WISHLIST => [
                ProductList::PRODUCT_LIST => [
                    ItemList::VIEW_OPTIONS => $defaultViewOptions,
                    ItemList::REQUEST_PARAMETERS => $productsListRequestParameters,
                    ItemList::APPLICABLE_FILTERS => $productListApplicableFilters + [
                        Parameters::ONLY_OFFERS => [
                            ApplicableFilter::ENABLED => true
                        ],
                        Parameters::ONLY_FEATURED => [
                            ApplicableFilter::ENABLED => true
                        ]
                    ],
                    ItemList::DEFAULT_PARAMETERS_VALUES => $defaultParametersValues,
                    ItemList::PAGINATION => $defaultPagination
                ]
            ],
            Configuration::SALES_AGENT_CUSTOMERS => [
                SalesAgentCustomers::ROWS_LIST => [
                    ItemList::DEFAULT_PARAMETERS_VALUES => [
                        Parameters::PER_PAGE => 25,
                    ],
                    ItemList::PAGINATION => $defaultPagination,
                ],
            ],
            Configuration::ACCOUNT => [
                Account::USED_ACCOUNT_PATH => false,
                Account::ACCOUNT_TYPE => GeneralRestriction::ONLY_GENERAL,
                Account::REGISTERED_USERS_LIST => [
                    ItemList::VIEW_OPTIONS => $defaultRegisteredUsersViewOptions,
                    ItemList::DEFAULT_PARAMETERS_VALUES => [
                        Parameters::PER_PAGE => 25,
                        Parameters::SORT => RegisteredUserSort::DATEADDED . '.' . RegisteredUserSort::SORT_DIRECTION_DESC
                    ],
                    ItemList::PAGINATION => $defaultPagination,
                ],
                Account::COMPANY_ROLES_LIST => [
                    ItemList::VIEW_OPTIONS => $defaultCompanyRolesViewOptions,
                    ItemList::DEFAULT_PARAMETERS_VALUES => [
                        Parameters::PER_PAGE => 25,
                        Parameters::SORT => CompanyRolesSort::NAME . '.' . CompanyRolesSort::SORT_DIRECTION_ASC
                    ],
                    ItemList::PAGINATION => $defaultPagination,
                ],
            ],
            Configuration::FORMS => [
                Forms::SET_USER => [
                    FormSetUser::DEFAULT_USER_TYPE => UserType::PARTICULAR,
                    FormSetUser::AVAILABLE_CUSTOM_TAG_POSITIONS => [],
                    FormSetUser::UNAVAILABLE_FIELDS_WITH_LOGIN => [
                        FormFieldsSetUser::EMAIL => [FormField::INCLUDED => true],
                        FormFieldsSetUser::NIF => [FormField::INCLUDED => true],
                        FormFieldsSetUser::VAT => [FormField::INCLUDED => true],
                        FormFieldsSetUser::COMPANY => [FormField::INCLUDED => true],
                        FormFieldsSetUser::ADDRESS => [FormField::INCLUDED => true],
                        FormFieldsSetUser::ADDRESS_ADDITIONAL_INFORMATION => [FormField::INCLUDED => true],
                        FormFieldsSetUser::NUMBER => [FormField::INCLUDED => true],
                        FormFieldsSetUser::PHONE => [FormField::INCLUDED => true],
                        FormFieldsSetUser::FAX => [FormField::INCLUDED => true],
                        FormFieldsSetUser::LOCATION => [FormField::INCLUDED => true],
                        FormFieldsSetUser::PASSWORD => [FormField::INCLUDED => true],
                        FormFieldsSetUser::PASSWORD_RETYPE => [FormField::INCLUDED => true]
                    ],
                    FormSetUser::AVAILABLE_FIELDS_ONLY_WITH_LOGIN => [
                        FormFieldsSetUser::CUSTOM_TAGS => [FormField::INCLUDED => false]
                    ],
                    // Deshabilita los campos de shipping, y por userType, solo muestra los que estén aqui
                    FormSetUser::AVAILABLE_FIELDS_FAST_REGISTER => [
                        FormFieldsSetUser::FIRST_NAME => [FormField::INCLUDED => true],
                        FormFieldsSetUser::LAST_NAME => [FormField::INCLUDED => true],
                        FormFieldsSetUser::COMPANY => [FormField::INCLUDED => true],
                        FormFieldsSetUser::EMAIL => [FormField::INCLUDED => true],
                        FormFieldsSetUser::PASSWORD => [FormField::INCLUDED => true],
                        FormFieldsSetUser::PASSWORD_RETYPE => [FormField::INCLUDED => true],
                    ],
                    FormSetUser::USER_FIELDS => [
                        FormSetUserFields::FIELDS_BY_USER_TYPE => [
                            UserType::PARTICULAR => [
                                FormSetUserFieldsByUserType::INCLUDED => true,
                                FormSetUserFieldsByUserType::PRIORITY => 0,
                                FormSetUserFieldsByUserType::USER => [
                                    FormSetUserFieldsByUserTypeElement::FIELDS => [
                                        FormFieldsSetUser::FIRST_NAME => [FormField::INCLUDED => true, FormField::PRIORITY => 1, FormField::REQUIRED => true],
                                        FormFieldsSetUser::LAST_NAME => [FormField::INCLUDED => true, FormField::PRIORITY => 2, FormField::REQUIRED => true],
                                        FormFieldsSetUser::NIF => [FormField::INCLUDED => true, FormField::PRIORITY => 3],
                                        FormFieldsSetUser::EMAIL => [FormField::INCLUDED => true, FormField::PRIORITY => 4, FormField::REQUIRED => true],
                                        FormFieldsSetUser::PHONE => [FormField::INCLUDED => true, FormField::PRIORITY => 5, FormField::REQUIRED => true],
                                        FormFieldsSetUser::ADDRESS => [FormField::INCLUDED => true, FormField::PRIORITY => 6, FormField::REQUIRED => true],
                                        FormFieldsSetUser::LOCATION => [FormField::INCLUDED => true, FormField::PRIORITY => 7, FormField::REQUIRED => true],
                                        FormFieldsSetUser::SUBSCRIBED => [FormField::INCLUDED => true, FormField::PRIORITY => 8],
                                        FormFieldsSetUser::PASSWORD => [FormField::INCLUDED => true, FormField::PRIORITY => 9, FormField::REQUIRED => true],
                                        FormFieldsSetUser::PASSWORD_RETYPE => [FormField::INCLUDED => true, FormField::PRIORITY => 10, FormField::REQUIRED => true],
                                        FormFieldsSetUser::USE_SHIPPING_ADDRESS => [FormField::INCLUDED => true, FormField::PRIORITY => 11],
                                        FormFieldsSetUser::CUSTOM_TAGS => [FormField::INCLUDED => true, FormField::PRIORITY => 12]
                                    ]
                                ]
                            ],
                            UserType::BUSINESS => [
                                FormSetUserFieldsByUserType::INCLUDED => true,
                                FormSetUserFieldsByUserType::PRIORITY => 1,
                                FormSetUserFieldsByUserType::USER => [
                                    FormSetUserFieldsByUserTypeElement::FIELDS => [
                                        FormFieldsSetUser::COMPANY => [FormField::INCLUDED => true, FormField::PRIORITY => 1, FormField::REQUIRED => true],
                                        FormFieldsSetUser::VAT => [FormField::INCLUDED => true, FormField::PRIORITY => 2, FormField::REQUIRED => true],
                                        FormFieldsSetUser::EMAIL => [FormField::INCLUDED => true, FormField::PRIORITY => 3, FormField::REQUIRED => true],
                                        FormFieldsSetUser::PHONE => [FormField::INCLUDED => true, FormField::PRIORITY => 4, FormField::REQUIRED => true],
                                        FormFieldsSetUser::ADDRESS => [FormField::INCLUDED => true, FormField::PRIORITY => 5, FormField::REQUIRED => true],
                                        FormFieldsSetUser::LOCATION => [FormField::INCLUDED => true, FormField::PRIORITY => 6, FormField::REQUIRED => true],
                                        FormFieldsSetUser::SUBSCRIBED => [FormField::INCLUDED => true, FormField::PRIORITY => 7],
                                        FormFieldsSetUser::PASSWORD => [FormField::INCLUDED => true, FormField::PRIORITY => 8, FormField::REQUIRED => true],
                                        FormFieldsSetUser::PASSWORD_RETYPE => [FormField::INCLUDED => true, FormField::PRIORITY => 9, FormField::REQUIRED => true],
                                        FormFieldsSetUser::USE_SHIPPING_ADDRESS => [FormField::INCLUDED => true, FormField::PRIORITY => 10],
                                        FormFieldsSetUser::CUSTOM_TAGS => [FormField::INCLUDED => true, FormField::PRIORITY => 11]
                                    ]
                                ]
                            ],
                            UserType::FREELANCE => [
                                FormSetUserFieldsByUserType::INCLUDED => true,
                                FormSetUserFieldsByUserType::PRIORITY => 2,
                                FormSetUserFieldsByUserType::USER => [
                                    FormSetUserFieldsByUserTypeElement::FIELDS => [
                                        FormFieldsSetUser::COMPANY => [FormField::INCLUDED => true, FormField::PRIORITY => 1, FormField::REQUIRED => true],
                                        FormFieldsSetUser::FIRST_NAME => [FormField::INCLUDED => true, FormField::PRIORITY => 2, FormField::REQUIRED => true],
                                        FormFieldsSetUser::LAST_NAME => [FormField::INCLUDED => true, FormField::PRIORITY => 3, FormField::REQUIRED => true],
                                        FormFieldsSetUser::NIF => [FormField::INCLUDED => true, FormField::PRIORITY => 4, FormField::REQUIRED => true],
                                        FormFieldsSetUser::PHONE => [FormField::INCLUDED => true, FormField::PRIORITY => 5, FormField::REQUIRED => true],
                                        FormFieldsSetUser::ADDRESS => [FormField::INCLUDED => true, FormField::PRIORITY => 6, FormField::REQUIRED => true],
                                        FormFieldsSetUser::EMAIL => [FormField::INCLUDED => true, FormField::PRIORITY => 7, FormField::REQUIRED => true],
                                        FormFieldsSetUser::LOCATION => [FormField::INCLUDED => true, FormField::PRIORITY => 8, FormField::REQUIRED => true],
                                        FormFieldsSetUser::SUBSCRIBED => [FormField::INCLUDED => true, FormField::PRIORITY => 9],
                                        FormFieldsSetUser::PASSWORD => [FormField::INCLUDED => true, FormField::PRIORITY => 10, FormField::REQUIRED => true],
                                        FormFieldsSetUser::PASSWORD_RETYPE => [FormField::INCLUDED => true, FormField::PRIORITY => 11, FormField::REQUIRED => true],
                                        FormFieldsSetUser::USE_SHIPPING_ADDRESS => [FormField::INCLUDED => true, FormField::PRIORITY => 12],
                                        FormFieldsSetUser::CUSTOM_TAGS => [FormField::INCLUDED => true, FormField::PRIORITY => 13]
                                    ]
                                ]
                            ]
                        ],
                        FormSetUserFields::SHIPPING_FIELDS => [
                            FormFieldsSetUser::FIRST_NAME => [FormField::INCLUDED => true, FormField::PRIORITY => 1, FormField::REQUIRED => true],
                            FormFieldsSetUser::LAST_NAME => [FormField::INCLUDED => true, FormField::PRIORITY => 2, FormField::REQUIRED => true],
                            FormFieldsSetUser::ADDRESS => [FormField::INCLUDED => true, FormField::PRIORITY => 3, FormField::REQUIRED => true],
                            FormFieldsSetUser::LOCATION => [FormField::INCLUDED => true, FormField::PRIORITY => 4, FormField::REQUIRED => true],
                            FormFieldsSetUser::MOBILE => [FormField::INCLUDED => true, FormField::PRIORITY => 5, FormField::REQUIRED => true]
                        ]
                    ],
                    FormSetUser::ADDRESSBOOK_FIELDS => [
                        FormSetUserFields::FIELDS_BY_USER_TYPE => [
                            UserType::PARTICULAR => [
                                FormSetUserFieldsByUserType::INCLUDED => true,
                                FormSetUserFieldsByUserType::PRIORITY => 0,
                                FormSetUserFieldsByUserType::BILLING => [
                                    FormSetUserFieldsByUserTypeElement::FIELDS => [
                                        FormFieldsSetUser::ALIAS => [FormField::INCLUDED => true, FormField::PRIORITY => 1],
                                        FormFieldsSetUser::FIRST_NAME => [FormField::INCLUDED => true, FormField::PRIORITY => 2, FormField::REQUIRED => true],
                                        FormFieldsSetUser::LAST_NAME => [FormField::INCLUDED => true, FormField::PRIORITY => 3, FormField::REQUIRED => true],
                                        FormFieldsSetUser::NIF => [FormField::INCLUDED => true, FormField::PRIORITY => 4],
                                        FormFieldsSetUser::PHONE => [FormField::INCLUDED => true, FormField::PRIORITY => 5, FormField::REQUIRED => true],
                                        FormFieldsSetUser::ADDRESS => [FormField::INCLUDED => true, FormField::PRIORITY => 6, FormField::REQUIRED => true],
                                        FormFieldsSetUser::LOCATION => [FormField::INCLUDED => true, FormField::PRIORITY => 7, FormField::REQUIRED => true]
                                    ]
                                ]
                            ],
                            UserType::BUSINESS => [
                                FormSetUserFieldsByUserType::INCLUDED => true,
                                FormSetUserFieldsByUserType::PRIORITY => 1,
                                FormSetUserFieldsByUserType::BILLING => [
                                    FormSetUserFieldsByUserTypeElement::FIELDS => [
                                        FormFieldsSetUser::ALIAS => [FormField::INCLUDED => true, FormField::PRIORITY => 1, FormField::REQUIRED => true],
                                        FormFieldsSetUser::COMPANY => [FormField::INCLUDED => true, FormField::PRIORITY => 2, FormField::REQUIRED => true],
                                        FormFieldsSetUser::VAT => [FormField::INCLUDED => true, FormField::PRIORITY => 3, FormField::REQUIRED => true],
                                        FormFieldsSetUser::ADDRESS => [FormField::INCLUDED => true, FormField::PRIORITY => 4, FormField::REQUIRED => true],
                                        FormFieldsSetUser::LOCATION => [FormField::INCLUDED => true, FormField::PRIORITY => 5, FormField::REQUIRED => true],
                                        FormFieldsSetUser::PHONE => [FormField::INCLUDED => true, FormField::PRIORITY => 6, FormField::REQUIRED => true]
                                    ]
                                ]
                            ],
                            UserType::FREELANCE => [
                                FormSetUserFieldsByUserType::INCLUDED => true,
                                FormSetUserFieldsByUserType::PRIORITY => 2,
                                FormSetUserFieldsByUserType::BILLING => [
                                    FormSetUserFieldsByUserTypeElement::FIELDS => [
                                        FormFieldsSetUser::ALIAS => [FormField::INCLUDED => true, FormField::PRIORITY => 1, FormField::REQUIRED => true],
                                        FormFieldsSetUser::COMPANY => [FormField::INCLUDED => true, FormField::PRIORITY => 2, FormField::REQUIRED => true],
                                        FormFieldsSetUser::VAT => [FormField::INCLUDED => true, FormField::PRIORITY => 3, FormField::REQUIRED => true],
                                        FormFieldsSetUser::FIRST_NAME => [FormField::INCLUDED => true, FormField::PRIORITY => 4, FormField::REQUIRED => true],
                                        FormFieldsSetUser::LAST_NAME => [FormField::INCLUDED => true, FormField::PRIORITY => 5, FormField::REQUIRED => true],
                                        FormFieldsSetUser::ADDRESS => [FormField::INCLUDED => true, FormField::PRIORITY => 6, FormField::REQUIRED => true],
                                        FormFieldsSetUser::LOCATION => [FormField::INCLUDED => true, FormField::PRIORITY => 7, FormField::REQUIRED => true],
                                        FormFieldsSetUser::PHONE => [FormField::INCLUDED => true, FormField::PRIORITY => 8, FormField::REQUIRED => true]
                                    ]
                                ]
                            ]
                        ],
                        FormSetUserFields::SHIPPING_FIELDS => [
                            FormFieldsSetUser::ALIAS => [FormField::INCLUDED => true, FormField::PRIORITY => 1],
                            FormFieldsSetUser::FIRST_NAME => [FormField::INCLUDED => true, FormField::PRIORITY => 2, FormField::REQUIRED => true],
                            FormFieldsSetUser::LAST_NAME => [FormField::INCLUDED => true, FormField::PRIORITY => 3, FormField::REQUIRED => true],
                            FormFieldsSetUser::ADDRESS => [FormField::INCLUDED => true, FormField::PRIORITY => 4, FormField::REQUIRED => true],
                            FormFieldsSetUser::LOCATION => [FormField::INCLUDED => true, FormField::PRIORITY => 5, FormField::REQUIRED => true],
                            FormFieldsSetUser::MOBILE => [FormField::INCLUDED => true, FormField::PRIORITY => 6, FormField::REQUIRED => true]
                        ]
                    ]
                ],
                Forms::ACCOUNT => [
                    FormAccount::FIELDS => [
                        FormAccountFields::TYPE => [FormField::INCLUDED => true, FormField::PRIORITY => 1],
                        FormAccountFields::STATUS => [FormField::INCLUDED => true, FormField::PRIORITY => 2],
                        FormAccountFields::DATE_ADDED => [FormField::INCLUDED => true, FormField::PRIORITY => 3],
                        FormAccountFields::LAST_USED => [FormField::INCLUDED => true, FormField::PRIORITY => 4],
                        FormAccountFields::P_ID => [FormField::INCLUDED => true, FormField::PRIORITY => 5, FormField::REQUIRED => false],
                        FormAccountFields::EMAIL => [FormField::INCLUDED => true, FormField::PRIORITY => 6, FormField::REQUIRED => false],
                        FormAccountFields::IMAGE => [FormField::INCLUDED => false, FormField::PRIORITY => 7, FormField::REQUIRED => false],
                        FormAccountFields::DESCRIPTION => [FormField::INCLUDED => true, FormField::PRIORITY => 8, FormField::REQUIRED => false],
                    ],
                    FormAccount::MASTER => [
                        FormMaster::REGISTERED_USER => [
                            FormRegisteredUser::DEFAULT_ACCOUNT_TAB_PANE => FormAccountTabPane::INTERNAL,
                            FormRegisteredUser::ACCOUNT_TAB_PANE => [
                                FormAccountTabPane::INTERNAL => [
                                    FormAccountTabPaneField::INCLUDED => true,
                                ],
                                FormAccountTabPane::EXTERNAL => [
                                    FormAccountTabPaneField::INCLUDED => true,
                                ],
                                FormAccountTabPane::EXISTENT => [
                                    FormAccountTabPaneField::INCLUDED => true,
                                ],
                                FormAccountTabPane::NEW => [
                                    FormAccountTabPaneField::INCLUDED => true,

                                ],
                            ],
                            FormRegisteredUser::FIELDS => [
                                FormRegisteredUserFields::GENDER => [FormField::INCLUDED => true, FormField::PRIORITY => 1, FormField::REQUIRED => false],
                                FormRegisteredUserFields::FIRST_NAME => [FormField::INCLUDED => true, FormField::PRIORITY => 2, FormField::REQUIRED => false],
                                FormRegisteredUserFields::LAST_NAME => [FormField::INCLUDED => true, FormField::PRIORITY => 3, FormField::REQUIRED => false],
                                FormRegisteredUserFields::REGISTERED_USER_EMAIL => [FormField::INCLUDED => true, FormField::PRIORITY => 4, FormField::REQUIRED => true],
                                FormRegisteredUserFields::REGISTERED_USER_USERNAME => [FormField::INCLUDED => true, FormField::PRIORITY => 5, FormField::REQUIRED => false],
                                FormRegisteredUserFields::REGISTERED_USER_P_ID => [FormField::INCLUDED => true, FormField::PRIORITY => 6, FormField::REQUIRED => false],
                                FormRegisteredUserFields::BIRTHDAY => [FormField::INCLUDED => true, FormField::PRIORITY => 7, FormField::REQUIRED => false],
                                FormRegisteredUserFields::REGISTERED_USER_IMAGE => [FormField::INCLUDED => false, FormField::PRIORITY => 8, FormField::REQUIRED => false],
                                FormRegisteredUserFields::ROLE => [FormField::INCLUDED => true, FormField::PRIORITY => 9, FormField::REQUIRED => false],
                                FormRegisteredUserFields::JOB => [FormField::INCLUDED => true, FormField::PRIORITY => 10, FormField::REQUIRED => false],
                                FormRegisteredUserFields::SUBSCRIBED => [FormField::INCLUDED => true, FormField::PRIORITY => 11],
                            ],
                            FormRegisteredUser::APPROVE_FIELDS => [
                                FormRegisteredUserApproveFields::GENDER => [FormField::INCLUDED => true, FormField::PRIORITY => 1, FormField::REQUIRED => false],
                                FormRegisteredUserApproveFields::FIRST_NAME => [FormField::INCLUDED => true, FormField::PRIORITY => 2, FormField::REQUIRED => false],
                                FormRegisteredUserApproveFields::LAST_NAME => [FormField::INCLUDED => true, FormField::PRIORITY => 3, FormField::REQUIRED => false],
                                FormRegisteredUserApproveFields::REGISTERED_USER_EMAIL => [FormField::INCLUDED => true, FormField::PRIORITY => 4, FormField::REQUIRED => true],
                                FormRegisteredUserApproveFields::REGISTERED_USER_USERNAME => [FormField::INCLUDED => true, FormField::PRIORITY => 5, FormField::REQUIRED => false],
                                FormRegisteredUserApproveFields::REGISTERED_USER_P_ID => [FormField::INCLUDED => true, FormField::PRIORITY => 6, FormField::REQUIRED => false],
                                FormRegisteredUserApproveFields::BIRTHDAY => [FormField::INCLUDED => true, FormField::PRIORITY => 7, FormField::REQUIRED => false],
                            ]
                        ],
                    ],
                    FormAccount::CUSTOM_TAGS => [FormField::INCLUDED => false],
                    FormAccount::ADDRESS_BOOK => [FormField::INCLUDED => true],
                    FormAccount::ACCOUNT_REGISTERED_USER_FIELDS => [
                        FormAccountRegisteredUserFields::MASTER => [FormField::INCLUDED => true, FormField::PRIORITY => 1],
                        FormAccountRegisteredUserFields::REGISTERED_USER_STATUS => [FormField::INCLUDED => true, FormField::PRIORITY => 2, FormField::REQUIRED => false],
                        FormAccountRegisteredUserFields::ACCOUNT_ALIAS => [FormField::INCLUDED => true, FormField::PRIORITY => 3, FormField::REQUIRED => false],
                        FormAccountRegisteredUserFields::ROLE => [FormField::INCLUDED => true, FormField::PRIORITY => 4, FormField::REQUIRED => false],
                        FormAccountRegisteredUserFields::JOB => [FormField::INCLUDED => true, FormField::PRIORITY => 5, FormField::REQUIRED => false],
                    ],
                    FormAccount::COMPANY_DIVISION => [
                        FormCompanyDivision::DEFAULT_ACCOUNT_TAB_PANE => FormAccountTabPane::INTERNAL,
                        FormCompanyDivision::ACCOUNT_TAB_PANE => [
                            FormAccountTabPane::INTERNAL => [
                                FormAccountTabPaneField::INCLUDED => true,
                            ],
                            FormAccountTabPane::EXTERNAL => [
                                FormAccountTabPaneField::INCLUDED => true,
                            ],
                            FormAccountTabPane::NEW => [
                                FormAccountTabPaneField::INCLUDED => true,
                            ],
                        ],
                        FormCompanyDivision::INVOICING_FIELDS => [
                            FormCompanyDivisionInvoicingFields::LOCATION => [FormField::INCLUDED => true, FormField::PRIORITY => 1],
                            FormCompanyDivisionInvoicingFields::ADDRESS => [FormField::INCLUDED => true, FormField::PRIORITY => 2],
                            FormCompanyDivisionInvoicingFields::ADDRESS_ADDITIONAL_INFORMATION => [FormField::INCLUDED => true, FormField::PRIORITY => 3, FormField::REQUIRED => false],
                            FormCompanyDivisionInvoicingFields::NUMBER => [FormField::INCLUDED => true, FormField::PRIORITY => 4, FormField::REQUIRED => false],
                            FormCompanyDivisionInvoicingFields::PHONE => [FormField::INCLUDED => true, FormField::PRIORITY => 5, FormField::REQUIRED => false],
                            FormCompanyDivisionInvoicingFields::MOBILE => [FormField::INCLUDED => true, FormField::PRIORITY => 6, FormField::REQUIRED => false],
                            FormCompanyDivisionInvoicingFields::COMPANY => [FormField::INCLUDED => true, FormField::PRIORITY => 7, FormField::REQUIRED => false],
                            FormCompanyDivisionInvoicingFields::VAT => [FormField::INCLUDED => true, FormField::PRIORITY => 8, FormField::REQUIRED => false],
                        ],
                        FormCompanyDivision::GENERAL_FIELDS => [
                            FormCompanyDivisionGeneral::ENABLED => true,
                            FormCompanyDivisionGeneral::FORM_GENERAL_FIELDS => [
                                FormCompanyDivisionGeneralFields::P_ID => [FormField::INCLUDED => true, FormField::PRIORITY => 1, FormField::REQUIRED => false],
                                FormCompanyDivisionGeneralFields::IMAGE => [FormField::INCLUDED => true, FormField::PRIORITY => 2, FormField::REQUIRED => false],
                            ],
                        ]
                    ],
                    FormAccount::COMPANY_ROLES => [
                        FormCompanyRoles::FIELDS => [
                            FormCompanyRolesFields::NAME     => [FormField::INCLUDED => true, FormField::PRIORITY => 1],
                            FormCompanyRolesFields::DESCRIPTION => [FormField::INCLUDED => true, FormField::PRIORITY => 2, FormField::REQUIRED => false],
                            FormCompanyRolesFields::TARGET => [FormField::INCLUDED => true, FormField::PRIORITY => 3],
                            FormCompanyRolesFields::TARGET_DEFAULT => [FormField::INCLUDED => true, FormField::PRIORITY => 4, FormField::REQUIRED => false],
                        ],
                    ],
                ],
                Forms::COMMENTS => [
                    FormComments::MIN_RATING_AMOUNT => 1,
                    FormComments::MAX_RATING_AMOUNT => 5,
                    FormComments::DEFAULT_RATING_AMOUNT => 5,
                    FormComments::COMMENT_FIELD_REQUIRED => true,
                    FormComments::RATINGS_ALLOWED => 10,
                    FormComments::FORM_CLASS => 'commentsForm',
                    FormComments::ANONYMOUS_RATING_ENABLED => true,
                    FormComments::IP_STRICT_ENABLED => false
                ],
                Forms::PRODUCT_CONTACT => [
                    FormProductContact::FIELDS => [
                        FormFieldsProductContact::NAME => [FormField::INCLUDED => true, FormField::PRIORITY => 0, FormField::REQUIRED => true],
                        FormFieldsProductContact::FIRST_NAME => [FormField::INCLUDED => false, FormField::PRIORITY => 1],
                        FormFieldsProductContact::LAST_NAME => [FormField::INCLUDED => false, FormField::PRIORITY => 2],
                        FormFieldsProductContact::EMAIL => [FormField::INCLUDED => true, FormField::PRIORITY => 3, FormField::REQUIRED => true],
                        FormFieldsProductContact::PHONE => [FormField::INCLUDED => true, FormField::PRIORITY => 4],
                        FormFieldsProductContact::COMMENT => [FormField::INCLUDED => true, FormField::PRIORITY => 5, FormField::REQUIRED => true]
                    ]
                ],
                Forms::SHOPPING_LIST => [
                    FormShoppingList::NEW_FIELDS => [
                        FormFieldsShoppingList::NAME => [FormField::INCLUDED => true, FormField::PRIORITY => 0, FormField::REQUIRED => true],
                        FormFieldsShoppingList::DESCRIPTION => [FormField::INCLUDED => false, FormField::PRIORITY => 1],
                        FormFieldsShoppingList::KEEP_PURCHASED_ITEMS => [FormField::INCLUDED => false, FormField::PRIORITY => 2],
                        FormFieldsShoppingList::DEFAULT_ONE => [FormField::INCLUDED => false, FormField::PRIORITY => 3],
                        FormFieldsShoppingList::PRIORITY => [FormField::INCLUDED => false, FormField::PRIORITY => 4]
                    ],
                    FormShoppingList::EDIT_FIELDS => [
                        FormFieldsShoppingList::NAME => [FormField::INCLUDED => true, FormField::PRIORITY => 0, FormField::REQUIRED => true],
                        FormFieldsShoppingList::DESCRIPTION => [FormField::INCLUDED => true, FormField::PRIORITY => 1],
                        FormFieldsShoppingList::KEEP_PURCHASED_ITEMS => [FormField::INCLUDED => true, FormField::PRIORITY => 2],
                        FormFieldsShoppingList::DEFAULT_ONE => [FormField::INCLUDED => true, FormField::PRIORITY => 3],
                        FormFieldsShoppingList::PRIORITY => [FormField::INCLUDED => true, FormField::PRIORITY => 4]
                    ]
                ],
                Forms::SHOPPING_LIST_ROW_NOTE => [
                    FormShoppingListRowNote::FIELDS => [
                        FormFieldsShoppingListRowNote::COMMENT => [FormField::INCLUDED => true, FormField::PRIORITY => 0, FormField::REQUIRED => true],
                        FormFieldsShoppingListRowNote::QUANTITY => [FormField::INCLUDED => true, FormField::PRIORITY => 1],
                        FormFieldsShoppingListRowNote::PRIORITY => [FormField::INCLUDED => true, FormField::PRIORITY => 2],
                        FormFieldsShoppingListRowNote::IMPORTANCE => [FormField::INCLUDED => true, FormField::PRIORITY => 3],
                    ],
                ],
                Forms::CONTACT => [
                    FormContact::FIELDS => [
                        FormFieldsContact::FIRST_NAME => [FormField::INCLUDED => true, FormField::PRIORITY => 1, FormField::REQUIRED => true],
                        FormFieldsContact::LAST_NAME => [FormField::INCLUDED => true, FormField::PRIORITY => 2, FormField::REQUIRED => true],
                        FormFieldsContact::EMAIL => [FormField::INCLUDED => true, FormField::PRIORITY => 3, FormField::REQUIRED => true],
                        FormFieldsContact::PHONE => [FormField::INCLUDED => true, FormField::PRIORITY => 4, FormField::REQUIRED => true],
                        FormFieldsContact::COMMENT => [FormField::INCLUDED => true, FormField::PRIORITY => 5, FormField::REQUIRED => true],
                        FormFieldsContact::MOTIVE_ID => [FormField::INCLUDED => true, FormField::PRIORITY => 6, FormField::REQUIRED => true],
                        FormFieldsContact::COMPANY => [FormField::INCLUDED => false, FormField::PRIORITY => 7],
                        FormFieldsContact::ADDRESS => [FormField::INCLUDED => false, FormField::PRIORITY => 8],
                        FormFieldsContact::ADDRESS_ADDITIONAL_INFORMATION => [FormField::INCLUDED => false, FormField::PRIORITY => 9],
                        FormFieldsContact::NUMBER => [FormField::INCLUDED => false, FormField::PRIORITY => 10],
                        FormFieldsContact::CITY => [FormField::INCLUDED => false, FormField::PRIORITY => 11],
                        FormFieldsContact::STATE => [FormField::INCLUDED => false, FormField::PRIORITY => 12],
                        FormFieldsContact::POSTAL_CODE => [FormField::INCLUDED => false, FormField::PRIORITY => 13],
                        FormFieldsContact::VAT => [FormField::INCLUDED => false, FormField::PRIORITY => 14],
                        FormFieldsContact::NIF => [FormField::INCLUDED => false, FormField::PRIORITY => 15],
                        FormFieldsContact::MOBILE => [FormField::INCLUDED => false, FormField::PRIORITY => 16],
                        FormFieldsContact::FAX => [FormField::INCLUDED => false, FormField::PRIORITY => 17]
                    ]
                ],
                Forms::ELEMENTS => [
                    FormElements::BUTTON => [
                        FormElementButton::BUTTON => [
                            FormElement::ELEMENT_CLASS => '',
                            FormElement::LABEL_CLASS => ''
                        ],
                        FormElementButton::RESET => [
                            FormElement::ELEMENT_CLASS => '',
                            FormElement::LABEL_CLASS => ''
                        ],
                        FormElementButton::SUBMIT => [
                            FormElement::ELEMENT_CLASS => '',
                            FormElement::LABEL_CLASS => ''
                        ]
                    ],
                    FormElements::INPUT => [
                        FormElementInput::BUTTON => [
                            FormElement::ELEMENT_CLASS => '',
                            FormElement::LABEL_CLASS => ''
                        ],
                        FormElementInput::CHECKBOX => [
                            FormElement::ELEMENT_CLASS => '',
                            FormElement::LABEL_CLASS => ''
                        ],
                        FormElementInput::COLOR => [
                            FormElement::ELEMENT_CLASS => '',
                            FormElement::LABEL_CLASS => ''
                        ],
                        FormElementInput::DATE => [
                            FormElement::ELEMENT_CLASS => '',
                            FormElement::LABEL_CLASS => ''
                        ],
                        FormElementInput::DATETIME_LOCAL => [
                            FormElement::ELEMENT_CLASS => '',
                            FormElement::LABEL_CLASS => ''
                        ],
                        FormElementInput::EMAIL => [
                            FormElement::ELEMENT_CLASS => '',
                            FormElement::LABEL_CLASS => ''
                        ],
                        FormElementInput::FILE => [
                            FormElement::ELEMENT_CLASS => '',
                            FormElement::LABEL_CLASS => ''
                        ],
                        FormElementInput::HIDDEN => [
                            FormElement::ELEMENT_CLASS => '',
                            FormElement::LABEL_CLASS => ''
                        ],
                        FormElementInput::IMAGE => [
                            FormElement::ELEMENT_CLASS => '',
                            FormElement::LABEL_CLASS => ''
                        ],
                        FormElementInput::MONTH => [
                            FormElement::ELEMENT_CLASS => '',
                            FormElement::LABEL_CLASS => ''
                        ],
                        FormElementInput::NUMBER => [
                            FormElement::ELEMENT_CLASS => '',
                            FormElement::LABEL_CLASS => ''
                        ],
                        FormElementInput::PASSWORD => [
                            FormElement::ELEMENT_CLASS => '',
                            FormElement::LABEL_CLASS => ''
                        ],
                        FormElementInput::RADIO => [
                            FormElement::ELEMENT_CLASS => '',
                            FormElement::LABEL_CLASS => ''
                        ],
                        FormElementInput::RANGE => [
                            FormElement::ELEMENT_CLASS => '',
                            FormElement::LABEL_CLASS => ''
                        ],
                        FormElementInput::RESET => [
                            FormElement::ELEMENT_CLASS => '',
                            FormElement::LABEL_CLASS => ''
                        ],
                        FormElementInput::SEARCH => [
                            FormElement::ELEMENT_CLASS => '',
                            FormElement::LABEL_CLASS => ''
                        ],
                        FormElementInput::SUBMIT => [
                            FormElement::ELEMENT_CLASS => '',
                            FormElement::LABEL_CLASS => ''
                        ],
                        FormElementInput::TEL => [
                            FormElement::ELEMENT_CLASS => '',
                            FormElement::LABEL_CLASS => ''
                        ],
                        FormElementInput::TEXT => [
                            FormElement::ELEMENT_CLASS => '',
                            FormElement::LABEL_CLASS => ''
                        ],
                        FormElementInput::TIME => [
                            FormElement::ELEMENT_CLASS => '',
                            FormElement::LABEL_CLASS => ''
                        ],
                        FormElementInput::URL => [
                            FormElement::ELEMENT_CLASS => '',
                            FormElement::LABEL_CLASS => ''
                        ],
                        FormElementInput::WEEK => [
                            FormElement::ELEMENT_CLASS => '',
                            FormElement::LABEL_CLASS => ''
                        ],
                    ],
                    FormElements::OPTION => [
                        FormElement::ELEMENT_CLASS => '',
                        FormElement::LABEL_CLASS => ''
                    ],
                    FormElements::SELECT => [
                        FormElement::ELEMENT_CLASS => '',
                        FormElement::LABEL_CLASS => ''
                    ],
                    FormElements::TEXTAREA => [
                        FormElement::ELEMENT_CLASS => '',
                        FormElement::LABEL_CLASS => ''
                    ]
                ],
                Forms::USE_CAPTCHA => [
                    FormUseCaptcha::ADDRESS => true,
                    FormUseCaptcha::BLOG_SUBSCRIBE => true,
                    FormUseCaptcha::COMMENT => true,
                    FormUseCaptcha::CONTACT => true,
                    FormUseCaptcha::DELETE_ACCOUNT => true,
                    FormUseCaptcha::DELETE_PAYMENT_CARD => true,
                    FormUseCaptcha::DELETE_SHOPPING_LIST_ROWS => true,
                    FormUseCaptcha::LOGIN => true,
                    FormUseCaptcha::LOST_PASSWORD => true,
                    FormUseCaptcha::NEW_PASSWORD => true,
                    FormUseCaptcha::NEWSLETTER => true,
                    FormUseCaptcha::POST_COMMENT => true,
                    FormUseCaptcha::PRODUCT_CONTACT => true,
                    FormUseCaptcha::RECOMMEND => true,
                    FormUseCaptcha::RETURN_REQUEST => true,
                    FormUseCaptcha::SEND_MAIL => true,
                    FormUseCaptcha::SEND_SHOPPING_LIST_ROWS => true,
                    FormUseCaptcha::SET_USER => true,
                    FormUseCaptcha::SHOPPING_LIST => true,
                    FormUseCaptcha::SHOPPING_LIST_ROW_NOTES => true,
                    FormUseCaptcha::STOCK_ALERT => true,
                    FormUseCaptcha::UPDATE_PASSWORD => true
                ],

            ],
            Configuration::EVENTS => [
                Parameters::SETUP => []
            ],
            Configuration::PAGES => [
                Pages::PAGE_SUBPAGE_LEVELS => 2,
                Pages::SITE_MAP_PARAMETER_GROUP => $siteMapPageParameterGroup
            ],
            Configuration::USER => [
                User::USER_VOURCHER_PARAMETERS_GROUP => $userVouchersParametersGroup,
                User::USER_CUSTOM_TAGS_PARAMETERS_GROUP => $userCustomTagsParametersGroup
            ],
            Configuration::BASKET => [
                Basket::RECOVERY_BASKET_ROUTE_TYPE_REDIRECT => RouteType::CHECKOUT_BASKET
            ],
            Configuration::COMMERCE => [
                Commerce::USE_ONE_STEP_CHECKOUT => false,
                Commerce::USE_OSC_ASYNC => false,
                Commerce::IMAGE_OFFER_SMALL => IMAGE_OFFER_SMALL,
                Commerce::IMAGE_NOT_FOUND => IMAGE_NOT_FOUND,
                Commerce::IMAGE_FEATURED_SMALL => IMAGE_FEATURED_SMALL,
                Commerce::IMAGE_DISCOUNTS_SMALL => IMAGE_DISCOUNTS_SMALL,
                Commerce::AVAILABLE_FILL_DATA_FIELDS => implode(',', [
                    Parameters::ADDRESS,
                    Parameters::ADDRESS_ADDITIONAL_INFORMATION,
                    Parameters::CITY,
                    Parameters::COMPANY,
                    Parameters::EMAIL,
                    Parameters::FAX,
                    Parameters::FIRST_NAME,
                    Parameters::LAST_NAME,
                    Parameters::MOBILE,
                    Parameters::NAME,
                    Parameters::NICK,
                    Parameters::NIF,
                    Parameters::NUMBER,
                    Parameters::PHONE,
                    Parameters::POSTAL_CODE,
                    Parameters::STATE,
                    Parameters::VAT
                ]),
                Commerce::LOGIN_REQUIRED_AVAILABLE_ROUTES => [
                    InternalUser::LOGIN,
                    InternalUser::ADD_USER,
                    InternalUser::LOST_PASSWORD,
                    RouteType::CHANGE_PASSWORD_ANONYMOUS
                ],
                Commerce::MAINTENANCE_AVAILABLE_ROUTES => [
                    RouteType::CHECKOUT_ASYNC_ORDER,
                    RouteType::CHECKOUT_END_ORDER,
                    RouteType::CHECKOUT_DENIED_ORDER,
                    RouteType::CHECKOUT_CONFIRM_ORDER
                ],
                Commerce::ALLOW_DIFFERENT_COUNTRIES_ON_BILLING_AND_SHIPPING_ADDRESS => true,
                Commerce::COUNTRY_NAVIGATION_ASSIGNAMENT => Commerce::COUNTRY_NAVIGATION_ASSIGNAMENT_BILLING,
                Commerce::DISABLE_SHOW_AS_GRID_PRODUCT_OPTIONS => true,
                Commerce::LOCKED_STOCK => [
                    CommerceLockedStock::EXTEND_BY_ROUTE_VISITED => false,
                    CommerceLockedStock::EXTEND_BY_ROUTE_VISITED_RESET_COUNTER => false,
                    CommerceLockedStock::EXTEND_BY_ROUTE_VISITED_TIME => 0,
                    CommerceLockedStock::EXTEND_BY_BASKET_CHANGE => false,
                    CommerceLockedStock::EXTEND_BY_BASKET_CHANGE_RESET_COUNTER => false,
                    CommerceLockedStock::EXTEND_BY_BASKET_CHANGE_TIME => 0,
                    CommerceLockedStock::EXTEND_BY_PAYMENT_GATEWAY_VISITED => false,
                    CommerceLockedStock::EXTEND_BY_PAYMENT_GATEWAY_VISITED_RESET_COUNTER => false,
                    CommerceLockedStock::EXTEND_BY_PAYMENT_GATEWAY_VISITED_TIME => 0,
                    CommerceLockedStock::SHOW_EXTEND_BUTTON => false,
                    CommerceLockedStock::WARNING_POPUP => false,
                    CommerceLockedStock::WARNING_POPUP_TIME_THRESHOLD => 0,
                    CommerceLockedStock::EXPIRED_POPUP => false
                ],
            ],
            Configuration::DATA_VALIDATORS => [
                DataValidators::NEW_USER_FAST_REGISTER_PARTICULAR => '',
                DataValidators::NEW_USER_FAST_REGISTER_BUSINESS => '',
                DataValidators::NEW_USER_FAST_REGISTER_FREELANCE => '',
                DataValidators::NEW_USER_PARTICULAR => '',
                DataValidators::NEW_USER_BUSINESS => '',
                DataValidators::NEW_USER_FREELANCE => '',
                DataValidators::UPDATE_USER_PARTICULAR => '',
                DataValidators::UPDATE_USER_BUSINESS => '',
                DataValidators::UPDATE_USER_FREELANCE => '',
                DataValidators::UPDATE_USER_BILLING_ADDRESS_PARTICULAR => '',
                DataValidators::UPDATE_USER_BILLING_ADDRESS_BUSINESS => '',
                DataValidators::UPDATE_USER_BILLING_ADDRESS_FREELANCE => '',
                DataValidators::BILLING_ADDRESS_PARTICULAR => '',
                DataValidators::BILLING_ADDRESS_BUSINESS => '',
                DataValidators::BILLING_ADDRESS_FREELANCE => '',
                DataValidators::SHIPPING_ADDRESS => '',
                DataValidators::CONTACT => '',
                DataValidators::PRODUCT_CONTACT => ''
            ]
            /*
            ToDo: under construction
            Configuration::DATA_VALIDATORS => [
                DataValidators::NEW_USER_FAST_REGISTER_PARTICULAR => 'newUserFastRegisterParticular',
	            DataValidators::NEW_USER_FAST_REGISTER_BUSINESS => 'newUserFastRegisterBusiness',
	            DataValidators::NEW_USER_FAST_REGISTER_FREELANCE => 'newUserFastRegisterFreelance',
	            DataValidators::NEW_USER_PARTICULAR => 'newUserParticular',
	            DataValidators::NEW_USER_BUSINESS => 'newUserBusiness',
	            DataValidators::NEW_USER_FREELANCE => 'newUserFreelance',
	            DataValidators::UPDATE_USER_PARTICULAR => 'updateUserParticular',
	            DataValidators::UPDATE_USER_BUSINESS => 'updateUserBusiness',
	            DataValidators::UPDATE_USER_FREELANCE => 'updateUserFreelance',
                DataValidators::UPDATE_USER_BILLING_ADDRESS_PARTICULAR => 'updateUserBillingAddressParticular',
	            DataValidators::UPDATE_USER_BILLING_ADDRESS_BUSINESS => 'updateUserBillingAddressBusiness',
	            DataValidators::UPDATE_USER_BILLING_ADDRESS_FREELANCE => 'updateUserBillingAddressFreelance',
	            DataValidators::BILLING_ADDRESS_PARTICULAR => 'billingAddressParticular',
	            DataValidators::BILLING_ADDRESS_BUSINESS => 'billingAddressBusiness',
	            DataValidators::BILLING_ADDRESS_FREELANCE => 'billingAddressFreelance',
	            DataValidators::SHIPPING_ADDRESS => 'shippingAddress',
	            DataValidators::CONTACT => 'contact',
                DataValidators::PRODUCT_CONTACT => 'productContact' 
            ]
            */
        ];
    }

    /**
     *
     * @see \FWK\Core\Theme\TcInterface::runForbiddenResponse()
     */
    public function runForbiddenResponse(string $routeType): void {
        if (isset(self::FORBIDDEN_RESPONSE_ACTIONS[$routeType])) {
            Response::redirect(RoutePaths::getPath(self::FORBIDDEN_RESPONSE_ACTIONS[$routeType][TcDataItems::FORBIDDEN_ROUTE_TYPE]), self::FORBIDDEN_RESPONSE_ACTIONS[$routeType][TcDataItems::FORBIDDEN_STATUS]);
        } else {
            Response::redirect(RoutePaths::getPath(RouteType::HOME), 301);
        }
    }

    /**
     * Merge multiple configuration arrays into a single array.
     *
     * @param array ...$arrays The arrays to be merged
     * @return array The merged configuration array
     */
    protected function mergeConfigurationDatas(array ...$arrays): array {
        if (count($arrays) <= 1) {
            return $arrays;
        }
        $response = [];
        foreach ($arrays as $array) {
            if (empty($response)) {
                $response = $array;
            } else {
                self::mergeRecursiveConfigurationDatas($response, $array);
            }
        }
        return $response;
    }

    /**
     * Merge recursive configuration datas.
     *
     * @param array &$response The reference to the response array
     * @param array $array The input array
     * @throws Some_Exception_Class description of exception
     */
    protected function mergeRecursiveConfigurationDatas(array &$response, array $array) {
        foreach ($array as $key => $value) {
            if (isset($response[$key]) && is_array($response[$key]) && !array_is_list($response[$key])) {
                self::mergeRecursiveConfigurationDatas($response[$key], $value);
            } else {
                $response[$key] = $value;
            }
        }
    }
}
