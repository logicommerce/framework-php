<?php

namespace FWK\Core\Form;

use FWK\Core\Form\Elements\Form as FormHead;
use FWK\Core\Form\Elements\Inputs\InputEmail;
use FWK\Core\Form\Elements\Inputs\InputHidden;
use FWK\Core\Form\Elements\Inputs\InputTel;
use FWK\Core\Form\Elements\Inputs\InputPassword;
use FWK\Core\Form\Elements\Inputs\InputDate;
use FWK\Core\Form\Elements\Inputs\InputNumber;
use FWK\Core\Form\Elements\Inputs\InputRadio;
use FWK\Core\Form\Elements\Inputs\InputCheckbox;
use FWK\Enums\Parameters;
use SDK\Dtos\User\User;
use FWK\Core\Form\Elements\Inputs\InputText;
use FWK\Core\Form\Elements\Select;
use SDK\Enums\Gender;
use FWK\Core\Resources\Language;
use FWK\Enums\LanguageLabels;
use SDK\Dtos\User\BillingAddress;
use SDK\Enums\UserKeyCriteria;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\FilterInput\FilterInput;
use FWK\Core\Form\Elements\Textarea;
use FWK\Core\Form\Elements\Buttons\ButtonSubmit;
use FWK\Enums\Services;
use FWK\Core\Resources\Loader;
use FWK\Core\Form\Elements\Option;
use FWK\Core\Theme\Dtos\FormSetUser;
use FWK\Core\Form\Elements\Element;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\Form\Elements\Buttons\ButtonButton;
use FWK\Core\Form\Elements\MultiSelect;
use FWK\Core\Form\Elements\Input;
use FWK\Core\Form\Elements\Inputs\InputFile;
use SDK\Core\Dtos\ElementCollection;
use SDK\Core\Enums\CustomTagControlType;
use FWK\Core\Form\Elements\Inputs\InputImage;
use FWK\Core\Form\Elements\Separator;
use FWK\Core\Form\Elements\TableMultiSelect;
use FWK\Core\Resources\DateTimeFormatter;
use FWK\Enums\RouteTypes\InternalCheckout;
use FWK\Core\Resources\RoutePaths;
use FWK\Core\Resources\Session;
use FWK\Core\Resources\Utils;
use FWK\Core\Theme\Dtos\Configuration;
use FWK\Core\Theme\Dtos\FormField;
use FWK\Core\Theme\Dtos\FormFieldsContact;
use FWK\Core\Theme\Dtos\FormFieldsProductContact;
use FWK\Core\Theme\Dtos\FormFieldsSetUser;
use FWK\Core\Theme\Dtos\FormFieldsShoppingList;
use FWK\Core\Theme\Dtos\FormFieldsShoppingListRowNote;
use FWK\Core\Theme\Dtos\FormRegisteredUserFields;
use FWK\Core\Theme\Theme;
use FWK\Enums\NewsletterSubscriptionActions;
use FWK\Enums\RouteTypes\InternalUser;
use FWK\Enums\RouteTypes\InternalProduct;
use FWK\Enums\RouteTypes\InternalBlog;
use FWK\Enums\RouteType;
use FWK\Enums\RouteTypes\InternalAccount;
use FWK\Enums\RouteTypes\InternalPage;
use FWK\Enums\RouteTypes\InternalResources;
use SDK\Services\Parameters\Groups\Account\CompanyDivisionsParametersGroup;
use FWK\Enums\SetUserTypeForms;
use FWK\Services\LmsService;
use PhpParser\Builder\Param;
use SDK\Application;
use SDK\Core\Enums\MethodType;
use SDK\Core\Resources\Date;
use SDK\Dtos\Accounts\Account;
use SDK\Dtos\Accounts\AccountAddress;
use SDK\Dtos\Accounts\AccountEmployee;
use SDK\Dtos\Accounts\AccountTypes\CompanyDivision;
use SDK\Dtos\Accounts\BaseCompanyStructureTreeNode;
use SDK\Dtos\Accounts\CompanyRolePermissionsValues;
use SDK\Dtos\Accounts\CompanyStructureTreeNode;
use SDK\Dtos\Accounts\CustomCompanyRole;
use SDK\Dtos\Accounts\CustomCompanyRoleHeader;
use SDK\Dtos\Accounts\EmployeeVal;
use SDK\Dtos\Accounts\Master;
use SDK\Dtos\Accounts\MasterVal;
use SDK\Dtos\Accounts\RegisteredUser;
use SDK\Dtos\Accounts\RegisteredUserAccount;
use SDK\Dtos\Accounts\RegisteredUserSimpleProfile;
use SDK\Dtos\User\Address;
use SDK\Dtos\User\ShippingAddress;
use SDK\Dtos\User\ShoppingList;
use SDK\Dtos\User\ShoppingListRow;
use SDK\Enums\AccountKey;
use SDK\Enums\AccountRegisteredUserStatus;
use SDK\Enums\AccountStatus;
use SDK\Enums\AccountType;
use SDK\Enums\AddressType;
use SDK\Enums\CustomCompanyRoleTarget;
use SDK\Enums\CustomerType;
use SDK\Enums\PluginConnectorType;
use SDK\Enums\UserType;
use SDK\Enums\Importance;
use SDK\Enums\OrderStatus;
use SDK\Enums\SessionUsageModeType;
use SDK\Services\Parameters\Groups\Account\AccountRegisteredUsersParametersGroup;
use SDK\Services\Parameters\Groups\Account\CompanyRolesParametersGroup;
use SDK\Services\Parameters\Groups\PluginConnectorTypeParametersGroup;

/**
 * This is the FormFactory class, a factory of Form instances.
 * This class facilitates the creation of some predefined Forms:
 * <ul>
 * <li>Login</li>
 * <li>Delete account</li>
 * <li>Lost password</li>
 * <li>Product contact</li>
 * <li>Product recommend</li>
 * <li>Newsletter</li>
 * <li>Stock alert</li>
 * <li>Change password</li>
 * <li>Comment</li>
 * <li>Search</li>
 * <li>Send shopping list</li>
 * <li>...</li>
 *
 * @abstract
 * @see FormFactory::getAccountSalesAgentCustomers
 * @see FormFactory::getAccountSalesAgentSales
 * @see FormFactory::getAddress()
 * @see FormFactory::getBlogSubscribe()
 * @see FormFactory::getComment()
 * @see FormFactory::getContact()
 * @see FormFactory::getDeleteAccount()
 * @see FormFactory::getDeletePaymentCard()
 * @see FormFactory::getDeleteShoppingListRows()
 * @see FormFactory::getLogin()
 * @see FormFactory::getLostPassword()
 * @see FormFactory::getNewPassword()
 * @see FormFactory::getNewsletter()
 * @see FormFactory::getPhysicalLocations()
 * @see FormFactory::getPostComment()
 * @see FormFactory::getProductContact()
 * @see FormFactory::getReturnRequest()
 * @see FormFactory::getSalesAgentCustomers()
 * @see FormFactory::getSalesAgentSales()
 * @see FormFactory::getSearch()
 * @see FormFactory::getSendMail()
 * @see FormFactory::getSendShoppingListRows()
 * @see FormFactory::getShoppingListRowNotes()
 * @see FormFactory::getStockAlert()
 * @see FormFactory::getUpdatePassword()
 * @see FormFactory::getUserKeyElement()
 * @see FormFactory::setUser()
 * 
 * deprecated
 * @see FormFactory::getSendWishlist()
 * @see FormFactory::getDeleteWishlist()
 *
 *
 * @package FWK\Core\Form
 */
abstract class FormFactory {

    public const LOGIN_USERNAME_VALIDATION_EMAIL = 'email';

    public const LOGIN_USERNAME_VALIDATION_ID = 'id';

    public const CLASS_WILDCARD = '{{class}}';

    public const ATTRIBUTE_WILDCARD = '{{attributeWildcard}}';

    public const SET_USER_TYPE_ADD_USER = 'user';

    public const SET_USER_TYPE_ADD_USER_FAST_REGISTER = 'fastRegister';

    public const SET_USER_TYPE_ADD_CUSTOMER = 'customer';

    public const BLOG_SUBSCRIBE = 'subscribe';

    public const BLOG_CATEGORY_SUBSCRIBE = 'categorySubscribe';

    public const BLOG_POST_SUBSCRIBE = 'postSubscribe';

    public const RETURN_PRODUCTS = 'returnProducts';

    public const RETURN_POINTS = 'returnPoints';

    public const RMA_REASONS = 'rmaReasons';

    public const SALES_AGENT_CUSTOMERS = 'salesAgentCustomers';

    public const SALES_AGENT_SALES = 'salesAgentSales';

    private static ?Configuration $themeConfiguration = null;

    private static ?Language $language = null;

    protected static function getConfiguration(): Configuration {
        if (is_null(self::$themeConfiguration)) {
            self::$themeConfiguration = Theme::getInstance()->getConfiguration();
        }
        return self::$themeConfiguration;
    }

    protected static function getLanguage(): Language {
        if (is_null(self::$language)) {
            self::$language = Language::getInstance();
        }
        return self::$language;
    }

    // TODO: CleanCode -> Pasar a trait todo el tema de usuario

    /**
     * This static method returns the 'set user' Form for the current user of the commerce session.
     *
     * @param string $formUserType  To indicate the type of user. Possible values: FormFactory::SET_USER_TYPE_ADD_USER (by default), FormFactory::SET_USER_TYPE_ADD_CUSTOMER, SET_USER_TYPE_ADD_USER_FAST_REGISTER.
     * @param ?User $user   Sets the user for add default values to the form inputs
     * @param ?ElementCollection $userCustomTags Sets the user custom tags to add in the form 
     * @param bool $showUserCustomTags Define if the user custom tags must be show
     * @param bool $showAddressBook Define if the address book must be show        
     *            
     * @throws CommerceException
     *
     * @return Form
     */
    public static function setUser(string $formUserType = '', ?User $user = null, ?ElementCollection $userCustomTags = null, bool $showUserCustomTags = true, bool $showAddressBook = true, bool $thisAccountUpdatePermissions = true): Form {
        $languageSheet = self::getLanguage();
        $idForm = 'customerForm';
        if (is_null($user)) {
            $user = new User();
        }

        $selectedBillingAddressId = $user->getSelectedBillingAddressId() ?? 0;
        if ($selectedBillingAddressId > 0) {
            $billingAddress = $user->getAddress($selectedBillingAddressId, AddressType::BILLING);
        } else {
            $billingAddress = $user->getDefaultBillingAddress();
            if (is_null($billingAddress)) {
                $billingAddress = new BillingAddress();
            }
        }

        $selectedShippingAddressId = $user->getSelectedShippingAddressId() ?? 0;
        if ($selectedShippingAddressId > 0) {
            $shippingAddress = $user->getAddress($selectedShippingAddressId, AddressType::SHIPPING);
        } else {
            $shippingAddress = $user->getDefaultShippingAddress();
            if (is_null($shippingAddress)) {
                $shippingAddress = new ShippingAddress();
            }
        }

        $fastRegisterFields = [];
        $fastRegisterFieldsWithPriority = false;
        $formItems = [];
        $settings = self::getConfiguration()->getForms()->getSetUser();
        $userName = Utils::getUserName($user);

        if ($formUserType === self::SET_USER_TYPE_ADD_CUSTOMER) {
            $formHead = (new FormHead(RoutePaths::getPath(InternalCheckout::ADD_CUSTOMER), 'CustomerForm'))->setAutocomplete(Input::AUTOCOMPLETE_OFF)->setClass('customerForm');
        } elseif ($formUserType === self::SET_USER_TYPE_ADD_USER_FAST_REGISTER) {
            $formHead = (new FormHead(RoutePaths::getPath(InternalUser::ADD_USER_FAST_REGISTER), 'UserFastRegisterForm'))->setAutocomplete(Input::AUTOCOMPLETE_OFF)->setClass('UserFastRegisterForm');
            $fastRegisterFields = $settings->getAvailableFieldsFastRegister()->getSortFilterArrayFields();
            $fastRegisterFieldsWithPriority = $settings->getAvailableFieldsFastRegister()->hasPriority();
        } else {
            $formHead = (new FormHead(RoutePaths::getPath(InternalUser::ADD_USER), 'UserForm'))->setAutocomplete(Input::AUTOCOMPLETE_OFF)->setClass('userForm');
        }
        $formHead = $formHead->setMethod(FormHead::METHOD_POST)->setId($idForm)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);

        if (Utils::isUserLoggedIn($user)) {
            $userKeyCriteria = Application::getInstance()->getEcommerceSettings()->getUserAccountsSettings()->getUserKeyCriteria();
            $userNameLabel = $languageSheet->getLabelValue(LanguageLabels::USER_P_ID . ucwords((new \ReflectionClassConstant(LanguageLabels::class, $userKeyCriteria))->getValue()));
            $formItems[] = new FormItem(Parameters::USERNAME, (new InputText($userName))->setClass(self::CLASS_WILDCARD)->setDisabled(true)->setLabelFor($userNameLabel));
            $formItems[] = new FormItem(Parameters::ADDRESS_ID, new InputHidden($billingAddress->getId()));
            $formItems[] = new FormItem(Parameters::USER_TYPE, new InputHidden($billingAddress->getUserType()));
            if (isset($settings->getUserFields()->getFieldsByUserType()[$billingAddress->getUserType()])) {
                $fields = $settings->getUserFields()->getFieldsByUserType()[$billingAddress->getUserType()]->getUser()->getFields()->getSortFilterArrayFormFields();
                $unavailableFieldsWithLogin = $settings->getUnavailableFieldsWithLogin()->getSortFilterArrayFields();
                foreach ($fields as $field => $formField) {
                    if (!in_array($field, $unavailableFieldsWithLogin)) {
                        if ($showUserCustomTags && $field === Parameters::CUSTOM_TAGS && !is_null($userCustomTags)) {
                            self::setUserCustomTags($user, $userCustomTags, $formItems, $field, $settings, $user->getUserAdditionalInformation()->getSimulatedUser(), $thisAccountUpdatePermissions);
                        } elseif ($field != Parameters::CUSTOM_TAGS) {
                            $newField = self::userFields($field, $formField, $user, $billingAddress, '', $user->getUserAdditionalInformation()->getSimulatedUser());
                            if (!is_null($newField)) {
                                $formItems[$field] = $newField;
                            }
                        }
                    }
                }
            }
            if ($showAddressBook) {
                $formItems[FormSetUser::ADDRESSBOOK_FIELDS] = [];
                $dataFormAddressbook = $settings->getAddressbookFields()->getSortFilterArrayFieldsByUserType();

                $addShippingFields = 0;
                $shippingFields = $settings->getAddressbookFields()->getShippingFields()->getSortFilterArrayFormFields();
                foreach ($dataFormAddressbook as $userType => $typeFields) {
                    $formItems[FormSetUser::ADDRESSBOOK_FIELDS][$userType] = [];
                    foreach (
                        [
                            SetUserTypeForms::BILLING,
                            SetUserTypeForms::SHIPPING
                        ] as $groupFields
                    ) {
                        if ($groupFields === SetUserTypeForms::SHIPPING) {
                            $userType = UserType::PARTICULAR;
                            $fields = $shippingFields;
                            $addShippingFields += 1;
                        } else {
                            $get = 'get' . ucfirst($groupFields);
                            $fields = $typeFields->$get()->getFields()->getSortFilterArrayFormFields();
                        }
                        if ($addShippingFields === 1 || $groupFields === SetUserTypeForms::BILLING) {
                            $formItems[FormSetUser::ADDRESSBOOK_FIELDS][$userType][$groupFields] = [];
                            if ($groupFields === SetUserTypeForms::SHIPPING) {
                                $formItems[FormSetUser::ADDRESSBOOK_FIELDS][$userType][$groupFields][] = new FormItem(Parameters::MODULE, new InputHidden(UserType::PARTICULAR));
                            }
                            foreach ($fields as $field => $formField) {
                                $newField = self::userFields($field, $formField, $user, ${$groupFields . 'Address'}, $userType . '_' . $groupFields);
                                if (!is_null($newField)) {
                                    $formItems[FormSetUser::ADDRESSBOOK_FIELDS][$userType][$groupFields][] = $newField;
                                }
                            }
                        }
                    }
                }
            }
        } else {
            $dataFormUser = $settings->getUserFields()->getSortFilterArrayFieldsByUserType();
            $formItems[] = new FormItem(Parameters::CREATE_ACCOUNT, (new InputHidden('1'))->setFilterInput(new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => FALSE,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_BOOLEAN
            ])));
            if (strlen($userName)) { //Guest user
                $formItems[] = new FormItem(Parameters::USER_TYPE, new InputHidden($billingAddress->getUserType()));
            }
            $formItems[FormSetUser::USER_FIELDS] = [];
            $availableFieldsOnlyWithLogin = $settings->getAvailableFieldsOnlyWithLogin()->getSortFilterArrayFields();
            $shippingFields = $settings->getUserFields()->getShippingFields()->getSortFilterArrayFormFields();
            $useCustomTags = false;
            foreach ($dataFormUser as $userType => $typeFields) {
                $formItems[FormSetUser::USER_FIELDS][$userType] = [];
                if ($formUserType === self::SET_USER_TYPE_ADD_USER_FAST_REGISTER) {
                    $groupsFields = [SetUserTypeForms::USER];
                } else {
                    $groupsFields = [
                        SetUserTypeForms::USER,
                        SetUserTypeForms::SHIPPING
                    ];
                }
                foreach ($groupsFields as $groupFields) {
                    if (strlen($userName) && $groupFields != SetUserTypeForms::USER) {
                        $addressInfo = $shippingAddress;
                    } else {
                        $addressInfo = $billingAddress;
                    }

                    if ($groupFields === SetUserTypeForms::SHIPPING) {
                        $fields = $shippingFields;
                    } else {
                        $get = 'get' . ucfirst($groupFields);
                        $fields = $typeFields->$get()->getFields()->getSortFilterArrayFormFields();
                        if ($formUserType === self::SET_USER_TYPE_ADD_USER_FAST_REGISTER && $fastRegisterFieldsWithPriority) {
                            $fieldsSortedByFastRegisterFields = [];
                            foreach ($fastRegisterFields as $fieldName) {
                                foreach ($fields as $field => $formField) {
                                    if ($field === $fieldName) {
                                        $fieldsSortedByFastRegisterFields[$field] = $formField;
                                        unset($fields[$field]);
                                        break;
                                    }
                                }
                            }
                            $fields = $fieldsSortedByFastRegisterFields;
                        }
                    }
                    $userTypeGroupFields = [];
                    foreach ($fields as $field => $formField) {
                        if (
                            !in_array($field, $availableFieldsOnlyWithLogin)
                            && ($formUserType !== self::SET_USER_TYPE_ADD_USER_FAST_REGISTER ||
                                ($formUserType === self::SET_USER_TYPE_ADD_USER_FAST_REGISTER && in_array($field, $fastRegisterFields))
                            )
                        ) {
                            if ($showUserCustomTags && $field === Parameters::CUSTOM_TAGS && !is_null($userCustomTags)) {
                                $useCustomTags = true;
                            } elseif ($field != Parameters::CUSTOM_TAGS) {
                                $newField = self::userFields($field, $formField, $user, $addressInfo, $userType . '_' . $groupFields);
                                if (!is_null($newField)) {
                                    $userTypeGroupFields[] = $newField;
                                }
                            }
                        }
                    }
                    $formItems[FormSetUser::USER_FIELDS][$userType][$groupFields] = $userTypeGroupFields;

                    if (!is_null($addressInfo->getLocation())) {
                        $formItems[] = new FormItem($userType . '_' . $groupFields . '_' . Parameters::LOCATION_LIST, (new InputHidden($addressInfo->getLocation()->getGeographicalZone()->getLocationId()))->setDisabled(true));
                    }
                }
            }
            if ($useCustomTags) {
                self::setUserCustomTags($user, $userCustomTags, $formItems, Parameters::CUSTOM_TAGS, $settings, false, $thisAccountUpdatePermissions);
            }
        }
        $formItems[] = new FormItem(Form::SUBMIT, (new ButtonSubmit(''))->setClass(self::CLASS_WILDCARD)->setId('customerFormSubmit')->setContentText($languageSheet->getLabelValue(LanguageLabels::SAVE)));
        $form = new Form($formHead, $formItems);
        if (self::getConfiguration()->getForms()->getUseCaptcha()->getSetUser()) {
            $form->addCaptcha();
        };
        return $form;
    }

    private static function setUserCustomTags(User $user, ElementCollection $userCustomTags, array &$formItems, string $field, FormSetUser $settings, bool $simulatedUser = false, bool $thisAccountUpdatePermissions = true): void {
        $customTagsPositions = $settings->getAvailableCustomTagPositions();
        $auxCustomTagsPositions = [];
        foreach ($userCustomTags as $userCustomTag) {
            if (!count($customTagsPositions) || in_array($userCustomTag->getPosition(), $customTagsPositions)) {
                $value = $userCustomTag->getDefaultValue();
                foreach ($user->getCustomTagValues() as $userCustomTagValue) {
                    if ($userCustomTag->getId() === $userCustomTagValue->getCustomTagId()) {
                        $value = $userCustomTagValue->getValue();
                        break;
                    }
                }
                $ctf = self::customTagFields($userCustomTags, $userCustomTag->getId(), $value, $simulatedUser, $thisAccountUpdatePermissions);
                if (!is_null($ctf)) {
                    if (!empty($customTagsPositions)) {
                        if (!isset($auxCustomTagsPositions[$userCustomTag->getPosition()])) {
                            $auxCustomTagsPositions[$userCustomTag->getPosition()] = [];
                        }
                        $auxCustomTagsPositions[$userCustomTag->getPosition()][$userCustomTag->getId()] = $ctf;
                    } else {
                        $formItems[$field . '_' . $userCustomTag->getId()] = $ctf;
                    }
                }
            }
        }
        if (!empty($auxCustomTagsPositions)) {
            foreach ($customTagsPositions as $customTagsPositions) {
                if (isset($auxCustomTagsPositions[$customTagsPositions])) {
                    foreach ($auxCustomTagsPositions[$customTagsPositions] as $customTagId => $auxCustomTagsPosition) {
                        $formItems[$field . '_' . $customTagId] = $auxCustomTagsPosition;
                    }
                }
            }
        }
    }

    private static function customTagFields(?ElementCollection $customTags, int $typeId = 0, string $value = '', bool $simulatedUser = false, bool $thisAccountUpdatePermissions = true): ?FormItem {
        $languageSheet = self::getLanguage();
        $customTagType = null;
        foreach ($customTags->getItems() as $type) {
            if ($typeId == $type->getId()) {
                $customTagType = $type;
                break;
            }
        }
        if (is_null($customTagType)) {
            return null;
        }
        switch ($customTagType->getControlType()) {
            case CustomTagControlType::BOOLEAN:
                $checked = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? '1' : '0';
                $inputElement = (new InputRadio([
                    '0' => $languageSheet->getLabelValue(LanguageLabels::NO),
                    '1' => $languageSheet->getLabelValue(LanguageLabels::YES),
                ], null, $checked));
                break;
            case CustomTagControlType::DATE:
                $inputElement = new InputDate($value);
                break;
            case CustomTagControlType::IMAGE:
                $inputElement = (new InputImage($value));
                break;
            case CustomTagControlType::NUMBER:
                $inputElement = (new InputNumber($value));
                if (is_numeric($customTagType->getMaxValue())) {
                    $inputElement->setMax($customTagType->getMaxValue());
                }
                if (is_numeric($customTagType->getMinValue())) {
                    $inputElement->setMin($customTagType->getMinValue());
                }
                break;
            case CustomTagControlType::SELECTOR:
                $selectableValues = [];
                foreach ($customTagType->getSelectableValues() as $selectableValue) {
                    $selectableValues[] = (new Option($selectableValue->getValue()))->setValue($selectableValue->getId())->setData($selectableValue->toArray());
                }
                $inputElement = new Select($selectableValues, null, $value);
                break;
            case CustomTagControlType::LONG_TEXT:
                $inputElement = (new Textarea($value));
                if (is_numeric($customTagType->getMaxValue())) {
                    $inputElement->setMaxlength($customTagType->getMaxValue());
                }
                if (is_numeric($customTagType->getMinValue())) {
                    $inputElement->setMinlength($customTagType->getMinValue());
                }
                break;
            case CustomTagControlType::SHORT_TEXT:
                $inputElement = (new InputText($value));
                if (is_numeric($customTagType->getMaxValue())) {
                    $inputElement->setMaxlength($customTagType->getMaxValue());
                }
                if (is_numeric($customTagType->getMinValue())) {
                    $inputElement->setMinlength($customTagType->getMinValue());
                }
                break;
            case CustomTagControlType::ATTACHMENT:
                $inputElement = (new InputFile($value));
                break;
            default:
                return null;
        }

        $fieldDisabled = $simulatedUser || Utils::isAccountUpdateBlocked($thisAccountUpdatePermissions);
        $inputElement
            ->setRequired($customTagType->getRequired())
            ->setLabelFor($customTagType->getLanguage()->getName())
            ->setDisabled($fieldDisabled)
            ->setClass(self::CLASS_WILDCARD);
        return (new FormItem(Parameters::CUSTOM_TAGS . '_' . $customTagType->getPId() . '_' . $customTagType->getId(), $inputElement));
    }

    private static function userFields(string $field, FormField $formField, User $user, Address $address, string $namePrefix, bool $disabled = false): ?FormItem {
        $languageSheet = self::getLanguage();
        if (strlen($namePrefix)) {
            $inputName = $namePrefix . '_' . $field;
        } else {
            $inputName = $field;
        }
        $account = Session::getInstance()?->getBasket()?->getAccount();
        $accountRegisteredUser = Session::getInstance()?->getBasket()?->getAccountRegisteredUser();
        $disabled = $disabled || ($namePrefix == "" && $accountRegisteredUser != null && !$accountRegisteredUser?->isMaster());

        switch ($field) {
            case Parameters::ADDRESS:
                $inputElement = (new InputText($address->getAddress()))->setMaxlength(255)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::ADDRESS));
                break;
            case Parameters::ADDRESS_ADDITIONAL_INFORMATION:
                $inputElement = (new InputText($address->getAddressAdditionalInformation()))->setMaxlength(255)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::ADDRESS_ADDITIONAL_INFORMATION));
                break;
            case Parameters::ALIAS:
                $inputElement = (new InputText($address->getAlias()))->setMaxlength(255)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::ALIAS));
                break;
            case Parameters::BIRTHDAY:
                $disabled = $disabled || (!is_null($user->getUserAdditionalInformation()) && $user->getUserAdditionalInformation()->getSimulatedUser());
                $inputElement = (new InputDate(is_null($user->getBirthday()) ? '' : $user->getBirthday()->originalFormat()))->setLabelFor($languageSheet->getLabelValue(LanguageLabels::BIRTH_DATE));
                break;
            case Parameters::CITY:
                $inputElement = (new InputText($address->getCity()))->setMaxlength(255)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::CITY));
                break;
            case Parameters::COMPANY:
                $inputElement = (new InputText($address->getCompany()))->setMaxlength(255)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::COMPANY));
                break;
            case Parameters::COUNTRY:
                $options = [];
                $sessionGeneralSettings = Session::getInstance()->getGeneralSettings();
                foreach (Application::getInstance()->getCountriesSettings($sessionGeneralSettings->getLanguage(), $sessionGeneralSettings->getCountry()) as $country) {
                    $options[] = (new Option($country->getName()))->setValue($country->getCode())->setData($country);
                }
                if (is_null($address->getLocation())) {
                    $selectedCountry = Session::getInstance()->getGeneralSettings()->getCountry();
                } else {
                    $selectedCountry = $address->getLocation()->getGeographicalZone()->getCountryCode();
                }
                $inputElement = (new Select($options, null, $selectedCountry))->setLabelFor($languageSheet->getLabelValue(LanguageLabels::COUNTRY));
                break;
            case Parameters::CREATE_ACCOUNT:
                throw new CommerceException(self::class . ' Duplicated user field: ' . $field . '. Is defined by default in setUser()', CommerceException::FORM_FACTORY_DUPLICATED_USER_FIELD);
            case Parameters::DEFAULT_ADDRESS:
                $inputElement = (new InputCheckbox('1'))->setChecked((!is_null($user->getDefaultBillingAddress()) && $user->getDefaultBillingAddress()->getId() === $address->getId()))->setLabelFor($languageSheet->getLabelValue(LanguageLabels::DEFAULT_ADDRESS));
                break;
            case Parameters::EMAIL:
                $disabled = $disabled || (!is_null($user->getUserAdditionalInformation()) && $user->getUserAdditionalInformation()->getSimulatedUser());
                $inputElement = (new InputEmail($user->getEmail()))->setMaxlength(50)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::EMAIL));
                break;
            case Parameters::FAX:
                $inputElement = (new InputTel($address->getFax()))->setMaxlength(50)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::FAX));
                break;
            case Parameters::FIRST_NAME:
                $disabled = $disabled || (
                    str_contains($inputName, SetUserTypeForms::BILLING) &&
                    $account != null  &&
                    $account->getType() != AccountType::GENERAL
                );
                $inputElement = (new InputText($address->getFirstName()))->setMaxlength(255)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::FIRST_NAME));
                break;
            case Parameters::GENDER:
                $disabled = $disabled || (!is_null($user->getUserAdditionalInformation()) && $user->getUserAdditionalInformation()->getSimulatedUser());
                $inputElement = (new InputRadio([
                    Gender::FEMALE => $languageSheet->getLabelValue(LanguageLabels::FEMALE),
                    Gender::MALE => $languageSheet->getLabelValue(LanguageLabels::MALE),
                    Gender::UNDEFINED => $languageSheet->getLabelValue(LanguageLabels::UNDEFINED)
                ], null, $user->getGender()));
                break;
            case Parameters::IMAGE:
                $disabled = $disabled || (!is_null($user->getUserAdditionalInformation()) && $user->getUserAdditionalInformation()->getSimulatedUser());
                $inputElement = (new InputText($user->getImage()))->setMaxlength(255)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::IMAGE));
                break;
            case Parameters::LOCATION:
                $locationId = 0;
                if (!is_null($address->getLocation())) {
                    $locationId = $address->getLocation()->getGeographicalZone()->getLocationId();
                }
                $inputElement = new InputHidden($locationId);
                break;
            case Parameters::LAST_NAME:
                $disabled = $disabled || (
                    str_contains($inputName, SetUserTypeForms::BILLING) &&
                    $account != null  &&
                    $account->getType() != AccountType::GENERAL
                );
                $inputElement = (new InputText($address->getLastName()))->setMaxlength(255)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::LAST_NAME));
                break;
            case Parameters::MOBILE:
                $inputElement = (new InputTel($address->getMobile()))->setMaxlength(50)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::MOBILE));
                break;
            case Parameters::NICK:
                $disabled = $disabled || (!is_null($user->getUserAdditionalInformation()) && $user->getUserAdditionalInformation()->getSimulatedUser());
                $inputElement = (new InputText($user->getNick()))->setMaxlength(50)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::NICK));
                break;
            case Parameters::NIF:
                $inputElement = (new InputText($address->getNif()))->setMaxlength(50)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::NIF));
                break;
            case Parameters::NUMBER:
                $inputElement = (new InputText($address->getNumber()))->setMaxlength(50)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::NUMBER));
                break;
            case Parameters::P_ID:
                $disabled = $disabled || (!is_null($user->getUserAdditionalInformation()) && $user->getUserAdditionalInformation()->getSimulatedUser());
                $inputElement = (new InputText($user->getPId()))->setMaxlength(50)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::P_ID));
                break;
            case Parameters::PASSWORD:
                $disabled = $disabled || (!is_null($user->getUserAdditionalInformation()) && $user->getUserAdditionalInformation()->getSimulatedUser());
                $inputElement = (new InputPassword())->setMaxlength(50)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::PASSWORD));
                break;
            case Parameters::PASSWORD_RETYPE:
                $disabled = $disabled || (!is_null($user->getUserAdditionalInformation()) && $user->getUserAdditionalInformation()->getSimulatedUser());
                $inputElement = (new InputPassword())->setMaxlength(50)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::RETYPE_PASSWORD));
                break;
            case Parameters::PHONE:
                $inputElement = (new InputTel($address->getPhone()))->setMaxlength(50)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::PHONE));
                break;
            case Parameters::POSTAL_CODE:
                $inputElement = (new InputText($address->getPostalCode()))->setMaxlength(50)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::POSTAL_CODE));
                break;
            case Parameters::RE:
                $checked = filter_var($address->getRe(), FILTER_VALIDATE_BOOLEAN) ? '1' : '0';
                $inputElement = (new InputRadio([
                    '0' => $languageSheet->getLabelValue(LanguageLabels::NO),
                    '1' => $languageSheet->getLabelValue(LanguageLabels::YES),
                ], null, $checked))->setLabelFor($languageSheet->getLabelValue(LanguageLabels::RE));
                break;
            case FormFieldsSetUser::SEPARATOR:
                $inputElement = (new Separator())->setLabelFor($languageSheet->getLabelValue(LanguageLabels::USER_FORM_SEPARATOR, ''));
                break;
            case FormFieldsSetUser::SEPARATOR_2:
                $inputElement = (new Separator())->setLabelFor($languageSheet->getLabelValue(LanguageLabels::USER_FORM_SEPARATOR_2, ''));
                break;
            case FormFieldsSetUser::SEPARATOR_3:
                $inputElement = (new Separator())->setLabelFor($languageSheet->getLabelValue(LanguageLabels::USER_FORM_SEPARATOR_3, ''));
                break;
            case Parameters::STATE:
                $inputElement = (new InputText($address->getState()))->setMaxlength(50)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::STATE));
                break;
            case Parameters::SUBSCRIBED:
                $disabled = $disabled || (!is_null($user->getUserAdditionalInformation()) && $user->getUserAdditionalInformation()->getSimulatedUser());
                /** @var \SDK\Services\PluginService */
                $pluginService = Loader::service(Services::PLUGIN);
                $params = self::getPluginConnectorTypeParametersGroup(PluginConnectorType::MAILING_SYSTEM);
                $mailSystemPlugins = $pluginService->getPlugins($params);
                if (!empty($mailSystemPlugins->getItems())) {
                    if (Utils::isUserLoggedIn($user) or strlen(Utils::getUserName($user))) {
                        $inputElement = (new ButtonButton())->setDisabled(true)->setData([Parameters::EMAIL => $user->getEmail(), Parameters::TYPE => NewsletterSubscriptionActions::CHECK_STATUS])->setContentText($languageSheet->getLabelValue(LanguageLabels::SUBSCRIBE));
                    } else {
                        $inputElement = (new InputCheckbox('1'))->setChecked(false)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::SUBSCRIBED));
                    }
                } else {
                    return null;
                }
                break;
            case Parameters::USE_SHIPPING_ADDRESS:
                $inputElement = (new InputCheckbox('1'))->setChecked($user->getUseShippingAddress())->setLabelFor($languageSheet->getLabelValue(LanguageLabels::USE_SHIPPING_ADDRESS));
                break;
            case Parameters::USER_TYPE:
                $inputElement = new InputHidden(strlen($address->getUserType()) > 0 ? $address->getUserType() : self::getConfiguration()->getForms()->getSetUser()->getDefaultUserType());
                break;
            case Parameters::VAT:
                $inputElement = (new InputText($address->getVat()))->setMaxlength(50)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::VAT_ID));
                break;
            default:
                throw new CommerceException(self::class . ' Undefined user field: ' . $field, CommerceException::FORM_FACTORY_UNDEFINED_USER_FIELD);
        }
        $inputElement = $inputElement->setClass(self::CLASS_WILDCARD . ' formField userField');
        $inputElement = $inputElement->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);
        if (method_exists($inputElement, 'setRequired')) {
            $inputElement = $inputElement->setRequired($formField->getRequired());
        }
        if (method_exists($inputElement, 'setRegex')) {
            $inputElement = $inputElement->setRegex($formField->getRegex());
        }
        if (method_exists($inputElement, 'setDisabled') && !$inputElement->getDisabled() && $field != Parameters::USE_SHIPPING_ADDRESS) {
            $inputElement = $inputElement->setDisabled($disabled);
        }
        return (new FormItem($inputName, $inputElement));
    }

    private static function accountFields(
        string $field,
        FormField $formField,
        ?Account $account = null,
        ?AccountAddress $address = null,
        string $namePrefix = '',
        RegisteredUser|RegisteredUserSimpleProfile $registeredUser = null,
        ?MasterVal $masterVal = null,
        ?CustomCompanyRole $customCompanyRole = null,
        array $companyRoles = [],
        string $rolesFilter = CustomCompanyRoleTarget::COMPANY_STRUCTURE_NON_MASTER,
        string $paramsPrefix = "",
        bool $required = false,
        bool $disabled = false,
        bool $isCreateField = false,
    ): ?FormItem {
        $languageSheet = self::getLanguage();
        $labels = $languageSheet->getLabels();
        $userKeyCriteria = Application::getInstance()->getEcommerceSettings()->getUserAccountsSettings()->getUserKeyCriteria();
        $accountType = Session::getInstance()?->getBasket()?->getAccount()?->getType() ?? AccountType::GENERAL;
        $customer = Session::getInstance()->getBasket()->getCustomer();
        if (is_null($registeredUser) && !is_null($account)) {
            $registeredUser = $account?->getMaster()?->getRegisteredUser() ?? null;
        }
        if (strlen($namePrefix)) {
            $inputName = $namePrefix . '_' . $field;
        } else {
            $inputName = $field;
        }
        if (strlen($paramsPrefix)) {
            $paramsName = $paramsPrefix . '_' . $field;
        } else {
            $paramsName = $field;
        }

        if (
            $isCreateField == false and
            (
                ($userKeyCriteria == UserKeyCriteria::EMAIL and $field == FormRegisteredUserFields::REGISTERED_USER_EMAIL) or
                ($userKeyCriteria == UserKeyCriteria::PID and $field == FormRegisteredUserFields::REGISTERED_USER_P_ID) or
                ($userKeyCriteria == UserKeyCriteria::USERNAME and $field == FormRegisteredUserFields::REGISTERED_USER_USERNAME)
            )
        ) {
            $required = true;
            $disabled = true;
        }

        switch ($paramsName) {
            case Parameters::ACCOUNT_ALIAS:
                $inputElement = (new InputText($masterVal->getAccountAlias()))->setMaxlength(255)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::ACCOUNT_REGISTERED_USER_ALIAS));
                break;
            case Parameters::ADDRESS:
                $inputElement = (new InputText($address?->getAddress() ?? ''))->setMaxlength(255)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::ADDRESS));
                break;
            case Parameters::ADDRESS_ADDITIONAL_INFORMATION:
                $inputElement = (new InputText($address?->getAddressAdditionalInformation() ?? ''))->setMaxlength(255)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::ADDRESS_ADDITIONAL_INFORMATION));
                break;
            case Parameters::ALIAS:
                $inputElement = (new InputText($address?->getAlias() ?? ''))->setMaxlength(255)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::ALIAS));
                break;
            case Parameters::BIRTHDAY:
                $inputElement = (new InputDate($registeredUser?->getBirthday() ? $registeredUser->getBirthday()->originalFormat() : ''))->setLabelFor($languageSheet->getLabelValue(LanguageLabels::ACCOUNT_REGISTERED_USER_BIRTH_DATE));
                break;
            case Parameters::CITY:
                $inputElement = (new InputText($address?->getCity() ?? ''))->setMaxlength(255)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::CITY));
                break;
            case Parameters::COMPANY:
                $inputElement = (new InputText($address?->getCompany() ?? ''))->setMaxlength(255)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::COMPANY));
                break;
            case Parameters::COUNTRY:
                $options = [];
                $sessionGeneralSettings = Session::getInstance()->getGeneralSettings();
                foreach (Application::getInstance()->getCountriesSettings($sessionGeneralSettings->getLanguage(), $sessionGeneralSettings->getCountry()) as $country) {
                    $options[] = (new Option($country->getName()))->setValue($country->getCode())->setData($country);
                }
                if (is_null($address?->getLocation())) {
                    $selectedCountry = Session::getInstance()->getGeneralSettings()->getCountry();
                } else {
                    $selectedCountry = $address->getLocation()->getGeographicalZone()->getCountryCode();
                }
                $inputElement = (new Select($options, null, $selectedCountry))->setLabelFor($languageSheet->getLabelValue(LanguageLabels::COUNTRY));
                break;
            case Parameters::CREATE_ACCOUNT:
                throw new CommerceException(self::class . ' Duplicated user field: ' . $field . '. Is defined by default in setUser()', CommerceException::FORM_FACTORY_DUPLICATED_USER_FIELD);
            case Parameters::DEFAULT_ADDRESS:
                $inputElement = (new InputCheckbox('1'))->setChecked((!is_null($account->getDefaultInvoicingAddresses()) && $account->getDefaultInvoicingAddresses()->getId() === $address->getId()))->setLabelFor($languageSheet->getLabelValue(LanguageLabels::DEFAULT_ADDRESS));
                break;
            case Parameters::DATE_ADDED:
                $inputElement = (new InputText($account?->getDateAdded() ? (new DateTimeFormatter())->getFormattedDateTime($account?->getDateAdded()) : ''))->setLabelFor($languageSheet->getLabelValue(LanguageLabels::ACCOUNT_EDIT_DATE_ADDED));
                break;
            case Parameters::DESCRIPTION:
                $inputElement = (new InputText($account?->getDescription() ?? ''))->setMaxlength(255)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::DESCRIPTION));
                break;
            case Parameters::EMAIL:
                $inputElement = (new InputText($account?->getEmail() ?? ''))->setMaxlength(50)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::ACCOUNT_REGISTERED_USER_EMAIL));
                break;
            case Parameters::FAX:
                $inputElement = (new InputTel($address->getFax()))->setMaxlength(50)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::FAX));
                break;
            case Parameters::FIRST_NAME:
                $inputElement = (new InputText(is_null($address) ? $registeredUser?->getFirstName() ?? '' : $address?->getFirstName() ?? ''))->setMaxlength(255)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::ACCOUNT_REGISTERED_USER_FIRST_NAME));
                break;
            case Parameters::GENDER:
                $inputElement = (new InputRadio([
                    Gender::FEMALE => $languageSheet->getLabelValue(LanguageLabels::FEMALE),
                    Gender::MALE => $languageSheet->getLabelValue(LanguageLabels::MALE),
                    Gender::UNDEFINED => $languageSheet->getLabelValue(LanguageLabels::UNDEFINED)
                ], null, $registeredUser?->getGender() ?? ''));
                break;
            case Parameters::IMAGE:
                return null;
                $inputElement = (new InputText($account?->getImage() ?? ''))->setMaxlength(255)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::IMAGE));
                break;
            case Parameters::JOB:
                if (in_array($accountType,  AccountType::getCompanyTypes())) {
                    if (is_null($masterVal)) {
                        $masterVal = $account?->getMaster() ?? null;
                    }
                    $inputElement = (new InputText(($masterVal instanceof AccountEmployee or $masterVal instanceof EmployeeVal) ? $masterVal?->getJob() ?? '' : ''))->setMaxlength(255)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::ACCOUNT_REGISTERED_USER_JOB));
                }
                break;
            case Parameters::LOCATION:
                $locationId = 0;
                if (!is_null($address?->getLocation())) {
                    $locationId = $address->getLocation()->getGeographicalZone()->getLocationId();
                }
                $inputElement = new InputHidden($locationId);
                break;
            case Parameters::LAST_NAME:
                $inputElement = (new InputText(is_null($address) ? $registeredUser?->getLastName() ?? '' : $address?->getLastName() ?? ''))->setMaxlength(255)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::ACCOUNT_REGISTERED_USER_LAST_NAME));
                break;
            case Parameters::LAST_USED:
                $inputElement = (new InputText($account?->getLastUsed() ? (new DateTimeFormatter())->getFormattedDateTime($account?->getLastUsed()) : ''))->setLabelFor($languageSheet->getLabelValue(LanguageLabels::ACCOUNT_EDIT_LAST_USED));
                break;
            case Parameters::MOBILE:
                $inputElement = (new InputTel($address?->getMobile() ?? ''))->setMaxlength(50)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::MOBILE));
                break;
            case Parameters::MASTER:
                $disabled = $masterVal?->getStatus() == AccountRegisteredUserStatus::PENDING_APPROVAL ||
                    $masterVal?->isMaster() ||
                    !Session::getInstance()?->getBasket()?->getAccountRegisteredUser()?->isMaster();
                $inputElement = (new InputCheckbox('1'))->setChecked(($masterVal?->isMaster() ?? false))->setLabelFor($languageSheet->getLabelValue(LanguageLabels::ACCOUNT_REGISTERED_USER_MASTER));
                break;
            case Parameters::NIF:
                $inputElement = (new InputText($address?->getNif() ?? ''))->setMaxlength(50)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::NIF));
                break;
            case Parameters::NUMBER:
                $inputElement = (new InputText($address?->getNumber() ?? ''))->setMaxlength(50)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::NUMBER));
                break;
            case Parameters::P_ID:
                $inputElement = (new InputText($account?->getPId() ?? ''))->setMaxlength(50)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::P_ID));
                break;
            case Parameters::PHONE:
                $inputElement = (new InputTel($address?->getPhone() ?? ''))->setMaxlength(50)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::PHONE));
                break;
            case Parameters::POSTAL_CODE:
                $inputElement = (new InputText($address?->getPostalCode() ?? ''))->setMaxlength(50)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::POSTAL_CODE));
                break;
            case Parameters::RE:
                $checked = filter_var($address?->getRe() ?? '', FILTER_VALIDATE_BOOLEAN) ? '1' : '0';
                $inputElement = (new InputRadio([
                    '0' => $languageSheet->getLabelValue(LanguageLabels::NO),
                    '1' => $languageSheet->getLabelValue(LanguageLabels::YES),
                ], null, $checked))->setLabelFor($languageSheet->getLabelValue(LanguageLabels::RE));
                break;
            case Parameters::TYPE:
                $labelKey = match ($account?->getType() ?? '') {
                    AccountType::GENERAL          => LanguageLabels::ACCOUNT_TYPE_GENERAL,
                    AccountType::INDIVIDUAL       => LanguageLabels::ACCOUNT_TYPE_INDIVIDUAL,
                    AccountType::FREELANCE        => LanguageLabels::ACCOUNT_TYPE_FREELANCE,
                    AccountType::COMPANY          => LanguageLabels::ACCOUNT_TYPE_COMPANY,
                    AccountType::COMPANY_DIVISION => LanguageLabels::ACCOUNT_TYPE_COMPANY_DIVISION,
                    default                        => '',
                };
                $typeLabel = $labelKey !== '' ? $languageSheet->getLabelValue($labelKey) : '';
                $inputElement = (new InputText($typeLabel))->setLabelFor($languageSheet->getLabelValue(LanguageLabels::TYPE));
                break;
            case Parameters::REGISTERED_USER_EMAIL:
                $inputElement = (new InputEmail($registeredUser?->getEmail() ?? ''))->setMaxlength(50)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::ACCOUNT_REGISTERED_USER_EMAIL));
                break;
            case Parameters::REGISTERED_USER_IMAGE:
                return null;
                $inputElement = (new InputText($registeredUser?->getImage() ?? ''))->setMaxlength(255)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::ACCOUNT_REGISTERED_USER_IMAGE));
                break;
            case Parameters::REGISTERED_USER_P_ID:
                $inputElement = (new InputText($registeredUser?->getPId() ?? ''))->setMaxlength(50)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::ACCOUNT_REGISTERED_USER_PID));
                break;
            case Parameters::REGISTERED_USER_USERNAME:
                $inputElement = (new InputText($registeredUser?->getUsername() ?? ''))->setMaxlength(255)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::ACCOUNT_REGISTERED_USER_USERNAME));
                break;
            case Parameters::REGISTERED_USER_STATUS:
                $statusOptions = [];
                foreach (AccountRegisteredUserStatus::getValues() as $statusOption) {
                    $select = $masterVal?->getStatus() == "" ? false : ($statusOption == $masterVal->getStatus());
                    if ($statusOption == AccountRegisteredUserStatus::PENDING_APPROVAL && ! $select) {
                        continue;
                    }
                    $statusOptionLabel = $languageSheet->getLabelValue($labels['ACCOUNT_REGISTERED_USER_STATUS' . '_' . $statusOption]);
                    $statusOptions[] = (new Option($statusOptionLabel))->setValue($statusOption)->setData($statusOption)->setSelected($select);
                }
                $disabled = ($masterVal?->isMaster() or $masterVal?->getStatus() == AccountRegisteredUserStatus::PENDING_APPROVAL);
                $inputElement = (new Select($statusOptions, null, LanguageLabels::ACCOUNT_REGISTERED_USER_STATUS_DEFAULT))->setLabelFor($languageSheet->getLabelValue(LanguageLabels::ACCOUNT_REGISTERED_USER_STATUS));
                break;
            case Parameters::ROLE . '_' . Parameters::DESCRIPTION:
                $inputElement = (new InputText($customCompanyRole?->getDescription() ?? ''))->setMaxlength(255)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::DESCRIPTION))->setId('roleDescription');
                break;
            case Parameters::ROLE . '_' . Parameters::NAME:
                $inputElement = (new InputText($customCompanyRole?->getName() ?? ''))->setMaxlength(50)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::NAME))->setId('roleName');
                break;
            case Parameters::ROLE . '_' . Parameters::P_ID:
                $inputElement = (new InputText($customCompanyRole?->getPId() ?? ''))->setMaxlength(50)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::P_ID))->setId('rolePId');
                break;
            case Parameters::ROLE . '_' . Parameters::TARGET:
                $targetOptions = [
                    (new Option($languageSheet->getLabelValue(LanguageLabels::COMPANY_STRUCTURE_MASTER)))
                        ->setValue(CustomCompanyRoleTarget::COMPANY_DIVISION_MASTER)
                        ->setSelected(($customCompanyRole?->getTarget() ?? null) == CustomCompanyRoleTarget::COMPANY_DIVISION_MASTER),
                    (new Option($languageSheet->getLabelValue(LanguageLabels::COMPANY_STRUCTURE_NON_MASTER)))
                        ->setValue(CustomCompanyRoleTarget::COMPANY_STRUCTURE_NON_MASTER)
                        ->setSelected(($customCompanyRole?->getTarget() ?? null) == CustomCompanyRoleTarget::COMPANY_STRUCTURE_NON_MASTER),
                ];
                $isEdit = $customCompanyRole != null;
                $disabled = $disabled || $isEdit;
                $inputElement =  (new Select($targetOptions, null, LanguageLabels::ACCOUNT_REGISTERED_USER_SEARCH_ROLE_DEFAULT))->setLabelFor($languageSheet->getLabelValue(LanguageLabels::ROLE_TARGET));
                break;
            case Parameters::ROLE . '_' . Parameters::TARGET_DEFAULT:
                $isEdit = $customCompanyRole != null;
                $isChecked = !$isEdit ? false : $customCompanyRole?->getTargetDefault() ?? false;
                $disabled = $disabled || $isChecked;
                $inputElement = (new InputCheckbox('1'))->setId('defaultRole')->setChecked($isChecked)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::DEFAULT));
                break;
            case Parameters::ROLE_ID:
                if (in_array($accountType,  AccountType::getCompanyTypes()) && LmsService::getAdvcaLicense()) {
                    $isMaster = $account?->getMaster()?->isMaster() ?? false;
                    $roleId = $account?->getMaster()?->getRole()?->getId() ?? 0;
                    $roleOptions[] = (new Option($languageSheet->getLabelValue(LanguageLabels::ACCOUNT_REGISTERED_USER_ROLE_DEFAULT)))->setValue(0)->setSelected($roleId === 0);
                    if (!$isMaster or $accountType === AccountType::COMPANY_DIVISION) {
                        foreach ($companyRoles as $companyRole) {
                            if (
                                $companyRole instanceof CustomCompanyRoleHeader &&
                                $companyRole->getTarget() === $rolesFilter
                            ) {
                                $roleOptions[] = (new Option($companyRole->getName()))->setValue($companyRole->getId())->setSelected($roleId === $companyRole->getId());
                            }
                        }
                    } else {
                        $disabled = true;
                    }
                    $inputElement = (new Select($roleOptions))->setLabelFor($languageSheet->getLabelValue(LanguageLabels::ACCOUNT_REGISTERED_USER_ROLE));
                }
                break;
            case FormFieldsSetUser::SEPARATOR:
                $inputElement = (new Separator())->setLabelFor($languageSheet->getLabelValue(LanguageLabels::USER_FORM_SEPARATOR, ''));
                break;
            case FormFieldsSetUser::SEPARATOR_2:
                $inputElement = (new Separator())->setLabelFor($languageSheet->getLabelValue(LanguageLabels::USER_FORM_SEPARATOR_2, ''));
                break;
            case FormFieldsSetUser::SEPARATOR_3:
                $inputElement = (new Separator())->setLabelFor($languageSheet->getLabelValue(LanguageLabels::USER_FORM_SEPARATOR_3, ''));
                break;
            case Parameters::SUBSCRIBED:
                $user = SESSION::getInstance()?->getUser();
                $disabled = $disabled || (!is_null($user->getUserAdditionalInformation()) && $user->getUserAdditionalInformation()->getSimulatedUser());
                /** @var \SDK\Services\PluginService */
                $pluginService = Loader::service(Services::PLUGIN);
                $params = self::getPluginConnectorTypeParametersGroup(PluginConnectorType::MAILING_SYSTEM);
                $mailSystemPlugins = $pluginService->getPlugins($params);
                if (!empty($mailSystemPlugins->getItems())) {
                    if (Utils::isUserLoggedIn($user) or strlen(Utils::getUserName($user))) {
                        $inputElement = (new ButtonButton())->setDisabled(true)->setData([Parameters::EMAIL => $user->getEmail(), Parameters::TYPE => NewsletterSubscriptionActions::SUBSCRIBE])->setContentText($languageSheet->getLabelValue(LanguageLabels::SUBSCRIBE));
                    } else {
                        $inputElement = (new InputCheckbox('1'))->setChecked(false)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::SUBSCRIBED));
                    }
                } else {
                    return null;
                }
                break;
            case Parameters::STATE:
                $inputElement = (new InputText($address?->getState() ?? ''))->setMaxlength(50)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::STATE));
                break;
            case Parameters::STATUS:
                $statusLabelKey = match ($account?->getStatus() ?? '') {
                    AccountStatus::ENABLED => LanguageLabels::ACCOUNT_STATUS_ENABLED,
                    AccountStatus::DISABLED => LanguageLabels::ACCOUNT_STATUS_DISABLED,
                    AccountStatus::PENDING_VERIFICATION => LanguageLabels::ACCOUNT_STATUS_PENDING_VERIFICATION,
                    AccountStatus::PENDING_MERCHANT_ACTIVATION => LanguageLabels::ACCOUNT_STATUS_PENDING_MERCHANT_ACTIVATION,
                    AccountStatus::DENIED => LanguageLabels::ACCOUNT_STATUS_DENIED,
                    default => '',
                };
                $statusLabel = $statusLabelKey !== '' ? $languageSheet->getLabelValue($statusLabelKey) : '';
                $inputElement = (new InputText($statusLabel))->setLabelFor($languageSheet->getLabelValue(LanguageLabels::STATUS));
                break;
            case Parameters::USE_SHIPPING_ADDRESS:
                $inputElement = (new InputCheckbox('1'))->setChecked($customer->isUseShippingAddress())->setLabelFor($languageSheet->getLabelValue(LanguageLabels::USE_SHIPPING_ADDRESS));
                break;
            case Parameters::USER_TYPE:
                $inputElement = new InputHidden(!is_null($address) && $address?->getCustomerType() != CustomerType::EMPTY ? UserType::getEnum($address?->getCustomerType()) : self::getConfiguration()->getForms()->getSetUser()->getDefaultUserType());
                break;
            case Parameters::VAT:
                $inputElement = (new InputText($address?->getVat() ?? ''))->setMaxlength(50)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::VAT_ID));
                break;
            default:
                throw new CommerceException(self::class . ' Undefined user field: ' . $inputName, CommerceException::FORM_FACTORY_UNDEFINED_USER_FIELD);
        }
        if (!is_null($inputElement)) {
            $inputElement = $inputElement->setClass(self::CLASS_WILDCARD . ' formField userField');
            $inputElement = $inputElement->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);
            if (method_exists($inputElement, 'setRequired')) {
                $inputElement = $inputElement->setRequired($required or $formField->getRequired());
            }
            if (method_exists($inputElement, 'setRegex')) {
                $inputElement = $inputElement->setRegex($formField->getRegex());
            }
            if (method_exists($inputElement, 'setDisabled') && !$inputElement->getDisabled() && $paramsName != Parameters::USE_SHIPPING_ADDRESS) {
                $inputElement = $inputElement->setDisabled($disabled);
            }
            return (new FormItem($inputName, $inputElement));
        }
        return null;
    }

    /**
     * This static method returns the 'user key' Input Element.
     *
     * @throws CommerceException
     *
     * @return Element
     */
    public static function getUserKeyElement(): Element {
        $languageSheet = self::getLanguage();
        $userKeyCriteria = Application::getInstance()->getEcommerceSettings()->getUserAccountsSettings()->getUserKeyCriteria();
        $label = LanguageLabels::USER_P_ID . ucwords((new \ReflectionClassConstant(LanguageLabels::class, $userKeyCriteria))->getValue());
        switch ($userKeyCriteria) {
            case UserKeyCriteria::PID:
                $inputElement = (new InputText())->setLabelFor($languageSheet->getLabelValue($label));
                $inputElement->setFilterInput(new FilterInput([
                    FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => FALSE
                ]));
                break;
            case UserKeyCriteria::EMAIL:
                $inputElement = (new InputEmail())->setLabelFor($languageSheet->getLabelValue($label));
                $inputElement->setFilterInput(new FilterInput([
                    FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => FALSE,
                    FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_EMAIL
                ]));
                break;
            case UserKeyCriteria::USERNAME:
                $inputElement = (new InputText())->setLabelFor($languageSheet->getLabelValue($label));
                $inputElement->setFilterInput(new FilterInput([
                    FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => FALSE
                ]));
                break;
            default:
                throw new CommerceException(self::class . 'Undefined user key criteria: ' . $userKeyCriteria, CommerceException::FORM_FACTORY_UNDEFINED_USER_KEY_CRITERIA);
        }
        return $inputElement;
    }

    /**
     * This static method returns the address Form .
     *
     * @throws CommerceException
     * 
     * @param string $type SetUserTypeForms::SHIPPING | SetUserTypeForms::BILLING
     * @param NULL|string $address 
     *
     * @return Form
     */
    public static function getAddress(string $type, ?Address $address = null): Form {
        $auxAddress = null;
        $themeConfiguration = self::getConfiguration();
        $defaultUserType = $themeConfiguration->getForms()->getSetUser()->getDefaultUserType();
        if ($type === SetUserTypeForms::BILLING) {
            $auxAddress = new BillingAddress(['userType' => $defaultUserType]);
        }
        if (is_null($address)) {
            $address = $auxAddress;
        }
        $languageSheet = self::getLanguage();
        $formHead = (new FormHead(RoutePaths::getPath(InternalUser::SET_ADDRESS_BOOK), 'addressForm'))->setData($address)->setAutocomplete(Input::AUTOCOMPLETE_OFF)->setClass('addressForm')->setMethod(FormHead::METHOD_POST)->setId('addressForm')->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);
        $formItems = [];
        $formItems[] = new FormItem(Parameters::ID, (new InputHidden($address->getId()))->setId('addressFormId_' . $address->getId()));
        $formItems[] = new FormItem(Parameters::MODE, new InputHidden('update'));
        $formItems[FormSetUser::ADDRESSBOOK_FIELDS] = [];

        if ($type === SetUserTypeForms::BILLING) {
            $dataFormAddressbook = $themeConfiguration->getForms()->getSetUser()->getAddressbookFields()->getFieldsByUserType();
            foreach ($dataFormAddressbook as $userType => $typeFields) {
                $formItems[FormSetUser::ADDRESSBOOK_FIELDS][$userType] = [];
                $formItems[FormSetUser::ADDRESSBOOK_FIELDS][$userType][$type] = [];
                $get = 'get' . ucfirst($type);
                $fields = $typeFields->$get()->getFields()->getSortFilterArrayFormFields();
                $formItems[FormSetUser::ADDRESSBOOK_FIELDS][$userType][$type][] = new FormItem($userType . '_' . $type . '_' . Parameters::USER_TYPE, new InputHidden($userType));
                foreach ($fields as $field => $formField) {
                    if ($field != Parameters::USER_TYPE) {
                        $newField = self::userFields($field, $formField, new User(), $address->getUserType() == $userType ? $address : $auxAddress, $userType . '_' . $type);
                        if (!is_null($newField)) {
                            $formItems[FormSetUser::ADDRESSBOOK_FIELDS][$userType][$type][] = $newField;
                        }
                    }
                }
            }
        } elseif ($type === SetUserTypeForms::SHIPPING) {
            $fields = $themeConfiguration->getForms()->getSetUser()->getAddressbookFields()->getShippingFields()->getSortFilterArrayFormFields();
            $formItems[FormSetUser::ADDRESSBOOK_FIELDS][UserType::PARTICULAR][$type] = [];
            $formItems[FormSetUser::ADDRESSBOOK_FIELDS][UserType::PARTICULAR][$type][] = new FormItem(Parameters::MODULE, new InputHidden(UserType::PARTICULAR));
            $formItems[FormSetUser::ADDRESSBOOK_FIELDS][UserType::PARTICULAR][$type][] = new FormItem(UserType::PARTICULAR . '_' . $type . '_' . Parameters::USER_TYPE, new InputHidden(UserType::PARTICULAR));
            foreach ($fields as $field => $formField) {
                if ($field != Parameters::USER_TYPE) {
                    $newField = self::userFields($field, $formField, new User(), $address, UserType::PARTICULAR . '_' . $type);
                    if (!is_null($newField)) {
                        $formItems[FormSetUser::ADDRESSBOOK_FIELDS][UserType::PARTICULAR][$type][] = $newField;
                    }
                }
            }
        }
        $formItems[] = new FormItem(Form::SUBMIT, (new ButtonSubmit(''))->setClass(self::CLASS_WILDCARD)->setId('addressFormSubmit')->setContentText($languageSheet->getLabelValue(LanguageLabels::SAVE)));
        $form = new Form($formHead, $formItems);
        if (self::getConfiguration()->getForms()->getUseCaptcha()->getAddress()) {
            $form->addCaptcha();
        };
        return $form;
    }

    /**
     * This static method returns the 'login' Form.
     *
     * @return Form
     */
    public static function getLogin(array $plugins = []): Form {
        $languageSheet = self::getLanguage();
        $formHead = (new FormHead(RoutePaths::getPath(InternalUser::LOGIN), 'loginForm'))->setAutocomplete(Input::AUTOCOMPLETE_ON)->setClass('loginForm')->setMethod(FormHead::METHOD_POST)->setId('loginForm')->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);
        $formItems = [];
        $formItems[] = new FormItem(Parameters::REDIRECT, (new InputHidden(''))->setId('redirectLogin'));
        $userKeyElement = self::getUserKeyElement();
        $userKeyElement->setClass(self::CLASS_WILDCARD . ' moduleField loginFormField validate-email required')->setMaxlength(50)->setRequired(true);
        $formItems[] = new FormItem(Parameters::USERNAME, $userKeyElement);
        $formItems[] = new FormItem(Parameters::PASSWORD, ((new InputPassword())->setRequired(true)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::PASSWORD))->setClass(self::CLASS_WILDCARD . ' moduleField loginFormField required')));
        $formItems[] = new FormItem(Form::SUBMIT, (new ButtonSubmit())->setClass(self::CLASS_WILDCARD . ' moduleButton loginFormButton')->setId('loginSubmit')->setContentText($languageSheet->getLabelValue(LanguageLabels::LOGIN_SUBMIT)));
        $formItems['plugins'] = [];
        /** @var \SDK\Services\PluginService */
        $pluginService = Loader::service(Services::PLUGIN);
        $params = self::getPluginConnectorTypeParametersGroup(PluginConnectorType::OAUTH);
        $oauthSystemPlugins = $pluginService->getPlugins($params);
        foreach ($oauthSystemPlugins->getItems() as $oauthPlugin) {
            $formItems['plugins'][] = new FormItem($oauthPlugin->getModule(), (new ButtonButton())->setContentText(''));
        }
        $form = new Form($formHead, $formItems);
        if (self::getConfiguration()->getForms()->getUseCaptcha()->getLogin()) {
            $form->addCaptcha();
        };
        return $form;
    }

    /**
     * This static method returns the 'delete account' Form.
     *
     * @return Form
     */
    public static function getDeleteAccount(): Form {
        $languageSheet = self::getLanguage();
        $formHead = (new FormHead(RoutePaths::getPath(InternalUser::DELETE_ACCOUNT), 'deleteAccountForm'))->setAutocomplete(Input::AUTOCOMPLETE_ON)->setMethod(FormHead::METHOD_POST)->setId('deleteAccountForm')->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);
        $formItems = [];
        $formItems[] = new FormItem(Parameters::PASSWORD, ((new InputPassword())->setRequired(true)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::PASSWORD))->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)));
        $formItems[] = new FormItem(Form::SUBMIT, (new ButtonSubmit())->setClass(self::CLASS_WILDCARD)->setId('deleteAccountSubmit')->setContentText($languageSheet->getLabelValue(LanguageLabels::SEND)));
        $form = new Form($formHead, $formItems);
        if (self::getConfiguration()->getForms()->getUseCaptcha()->getDeleteAccount()) {
            $form->addCaptcha();
        };
        return $form;
    }

    /**
     * This static method returns the 'delete payment card' Form.
     *
     * @return Form
     */
    public static function getDeletePaymentCard(int $id = 0, string $token = ''): Form {
        $languageSheet = self::getLanguage();
        $formHead = (new FormHead(RoutePaths::getPath(InternalUser::DELETE_PAYMENT_CARD), 'deletePaymentCardForm'))->setMethod(FormHead::METHOD_POST)->setId('deletePaymentCardForm')->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);
        $formItems = [];
        $formItems[] = new FormItem(Parameters::ID, (new InputHidden($id))->setId('deletePaymentCardId_' . $id));
        $formItems[] = new FormItem(Parameters::TOKEN, new InputHidden($token));
        $formItems[] = new FormItem(Form::SUBMIT, (new ButtonSubmit())->setClass(self::CLASS_WILDCARD)->setContentText($languageSheet->getLabelValue(LanguageLabels::DELETE)));
        $form = new Form($formHead, $formItems);
        if (self::getConfiguration()->getForms()->getUseCaptcha()->getDeletePaymentCard()) {
            $form->addCaptcha();
        };
        return $form;
    }

    /**
     * This static method returns the 'lost password' Form.
     *
     * @return Form
     */
    public static function getLostPassword(): Form {
        $languageSheet = self::getLanguage();
        $formHead = (new FormHead(RoutePaths::getPath(InternalUser::LOST_PASSWORD), 'lostPasswordForm'))->setAutocomplete(Input::AUTOCOMPLETE_OFF)->setMethod(FormHead::METHOD_POST)->setId('lostPasswordForm')->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);
        $formItems = [];
        $formItems[] = new FormItem(Parameters::USERNAME, (new InputEmail())->setRequired(true)->setId('emailField')->setClass(self::CLASS_WILDCARD)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::EMAIL))->setFilterInput(new FilterInput([
            FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => FALSE,
            FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_EMAIL
        ])));
        $formItems[] = new FormItem(Form::SUBMIT, (new ButtonSubmit())->setClass(self::CLASS_WILDCARD)->setId('lostPasswordSubmit')->setContentText($languageSheet->getLabelValue(LanguageLabels::SEND)));
        $form = new Form($formHead, $formItems);
        if (self::getConfiguration()->getForms()->getUseCaptcha()->getLostPassword()) {
            $form->addCaptcha();
        };
        return $form;
    }

    /**
     * This static method returns the 'product contact' Form.
     * The given parameters (id, firstName, lastName, email or phone) will autofill the corresponding inputs if you provide them.
     *
     * @param int $id
     *            If set, it autofills the corresponding input of the form.
     * @param string $firstName
     *            If set, it autofills the corresponding input of the form.
     * @param string $lastName
     *            If set, it autofills the corresponding input of the form.
     * @param string $email
     *            If set, it autofills the corresponding input of the form.
     * @param string $phone
     *            If set, it autofills the corresponding input of the form.
     *            
     * @return Form
     */
    public static function getProductContact(int $id = 0, string $name = '', string $firstName = '', string $lastName = '', string $email = '', string $phone = ''): Form {
        $languageSheet = self::getLanguage();
        $formHead = (new FormHead(RoutePaths::getPath(InternalProduct::SET_CONTACT), 'productContactForm'))->setMethod(FormHead::METHOD_POST)->setOnsubmit('return false;')->setId('productContactForm');
        $productContactFormTC = self::getConfiguration()->getForms()->getProductContact();
        $fields = $productContactFormTC->getFields()->getSortFilterArrayFormFields();
        $formItems[] = new FormItem(Parameters::ID, (new InputHidden($id))->setId('productContactId_' . $id));

        foreach ($fields as $field => $formField) {
            $inputElement = null;
            switch ($field) {
                case FormFieldsProductContact::NAME:
                    $inputElement = (new InputText($name))->setLabelFor($languageSheet->getLabelValue(LanguageLabels::NAME))->setId('productContactNameField');
                    break;
                case FormFieldsProductContact::FIRST_NAME:
                    $inputElement = (new InputText($firstName))->setLabelFor($languageSheet->getLabelValue(LanguageLabels::FIRST_NAME))->setId('productContactFirstNameField');
                    break;
                case FormFieldsProductContact::LAST_NAME:
                    $inputElement = (new InputText($lastName))->setLabelFor($languageSheet->getLabelValue(LanguageLabels::LAST_NAME))->setId('productContactLastNameField');
                    break;
                case FormFieldsProductContact::EMAIL:
                    $inputElement = (new InputEmail($email))->setLabelFor($languageSheet->getLabelValue(LanguageLabels::EMAIL))->setId('productContactEmailField');
                    break;
                case FormFieldsProductContact::PHONE:
                    $inputElement = (new InputTel($phone))->setLabelFor($languageSheet->getLabelValue(LanguageLabels::PHONE))->setId('productContactPhoneField');
                    break;
                case FormFieldsProductContact::COMMENT:
                    $inputElement = (new Textarea())->setLabelFor($languageSheet->getLabelValue(LanguageLabels::CONTACT_COMMENT))->setId('productContactQueryField');
                    break;
                case FormFieldsProductContact::SEPARATOR:
                    $inputElement = (new Separator())->setLabelFor($languageSheet->getLabelValue(LanguageLabels::PRODUCT_CONTACT_FORM_SEPARATOR, ''));
                    break;
            }
            if (!is_null($inputElement)) {
                $inputElement = $inputElement->setClass(self::CLASS_WILDCARD);
                $inputElement = $inputElement->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);
                if (method_exists($inputElement, 'setRequired')) {
                    $inputElement = $inputElement->setRequired($formField->getRequired());
                }
                if (method_exists($inputElement, 'setRegex')) {
                    $inputElement = $inputElement->setRegex($formField->getRegex());
                }
                $formItems[] = (new FormItem($field, $inputElement));
            }
        }

        $formItems[] = new FormItem(Form::SUBMIT, (new ButtonSubmit())->setClass(self::CLASS_WILDCARD)->setId('productContactSubmit')->setContentText($languageSheet->getLabelValue(LanguageLabels::CONTACT_SEND)));
        $form = new Form($formHead, $formItems);
        if (self::getConfiguration()->getForms()->getUseCaptcha()->getProductContact()) {
            $form->addCaptcha();
        };
        return $form;
    }

    /**
     * This static method returns the 'recommend' Form.
     * The given parameters (id, username, userEmail) will autofill the corresponding inputs if you provide them.
     *
     * @param int $id
     *            If set, it autofills the corresponding input of the form.
     * @param string $type
     *            If set, it autofills the corresponding input of the form.
     * @param string $userName
     *            If set, it autofills the corresponding input of the form.
     * @param string $userEmail
     *            If set, it autofills the corresponding input of the form.
     *            
     * @return Form
     */
    public static function getRecommend(int $id = 0, string $type = '', string $userName = '', string $userEmail = ''): Form {
        $languageSheet = self::getLanguage();
        $typePrefix = strtolower($type);
        $formHead = (new FormHead(RoutePaths::getPath(InternalProduct::SET_RECOMMEND), 'recommendForm'))->setAutocomplete(Input::AUTOCOMPLETE_ON)->setMethod(FormHead::METHOD_POST)->setId('recommendForm')->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);
        $formItems = [];
        $formItems[] = new FormItem(Parameters::ID, (new InputHidden($id))->setId($typePrefix . 'RecommendId_' . $id));
        $formItems[] = new FormItem(Parameters::TYPE, (new InputHidden($type))->setId($typePrefix . 'RecommendType_' . $id));
        $formItems[] = new FormItem(Parameters::OPTIONS, (new InputHidden())->setId($typePrefix . 'RecommendOptions_' . $id));
        $formItems[] = new FormItem(Parameters::NAME, (new InputText($userName))->setMaxlength(50)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::YOUR_NAME))->setId($typePrefix . 'RecommendNameField')->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)->setRequired(true));
        $formItems[] = new FormItem(Parameters::EMAIL, (new InputEmail($userEmail))->setLabelFor($languageSheet->getLabelValue(LanguageLabels::YOUR_EMAIL))->setId($typePrefix . 'RecommendEmailField')->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)->setRequired(true));
        $formItems[] = new FormItem(Parameters::TO_NAME, (new InputText())->setMaxlength(50)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::FRIEND_NAME))->setId($typePrefix . 'RecommendFriendNameField')->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)->setRequired(true));
        $formItems[] = new FormItem(Parameters::TO_EMAIL, (new InputEmail())->setLabelFor($languageSheet->getLabelValue(LanguageLabels::FRIEND_EMAIL))->setId($typePrefix . 'RecommendFriendEmailField')->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)->setRequired(true));
        $formItems[] = new FormItem(Parameters::COMMENT, (new Textarea())->setMaxlength(4000)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::ITEM_RECOMMEND_COMMENT))->setId($typePrefix . 'RecommendQueryField')->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)->setRequired(true));
        $formItems[] = new FormItem(Form::SUBMIT, (new ButtonSubmit())->setClass(self::CLASS_WILDCARD)->setId($typePrefix . 'RecommendSubmit')->setContentText($languageSheet->getLabelValue(LanguageLabels::ITEM_RECOMMEND_SEND)));
        $form = new Form($formHead, $formItems);
        if (self::getConfiguration()->getForms()->getUseCaptcha()->getRecommend()) {
            $form->addCaptcha();
        };
        return $form;
    }

    /**
     * This static method returns the 'newsletter' Form.
     *
     * @return Form|NULL
     */
    public static function getNewsletter(): ?Form {
        $form = null;
        /** @var \SDK\Services\PluginService */
        $pluginService = Loader::service(Services::PLUGIN);
        $params = self::getPluginConnectorTypeParametersGroup(PluginConnectorType::MAILING_SYSTEM);
        $mailSystemPlugins = $pluginService->getPlugins($params);
        if (!empty($mailSystemPlugins->getItems())) {
            $languageSheet = self::getLanguage();
            $formHead = (new FormHead(RoutePaths::getPath(InternalUser::NEWSLETTER), 'newsletterForm'))->setAutocomplete(Input::AUTOCOMPLETE_ON)->setMethod(FormHead::METHOD_POST)->setId('newsletterForm')->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);
            $formItems = [];
            $user = Session::getInstance()->getUser();
            if (Utils::isUserLoggedIn($user) or strlen(Utils::getUserName($user))) {
                $action = NewsletterSubscriptionActions::CHECK_STATUS;
                $formItems[] = new FormItem(Parameters::EMAIL, (new InputHidden($user->getEmail())));
            } else {
                $action = NewsletterSubscriptionActions::SUBSCRIBE;
                $formItems[] = new FormItem(Parameters::EMAIL, (new InputEmail())->setPlaceholder($languageSheet->getLabelValue(LanguageLabels::EMAIL))->setId('newsletterEmailField')->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)->setRequired(true));
            }
            $formItems[] = new FormItem(
                Parameters::TYPE,
                (new InputHidden($action))->setFilterInput(new FilterInput([
                    FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                    FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT => FilterInput::STR_FORMAT_TO_UPPER,
                    FilterInput::CONFIGURATION_FILTER_KEY_FUNCTION_VALIDATOR => 'FWK\Enums\NewsletterSubscriptionActions::isValid'
                ]))->setAutocomplete(Input::AUTOCOMPLETE_OFF)->setData([Parameters::EMAIL => $user->getEmail(), Parameters::TYPE => $action])
            );
            $formItems[] = new FormItem(Form::SUBMIT, (new ButtonSubmit())->setId('newsletterSubmit')->setClass(self::CLASS_WILDCARD)->setContentText($languageSheet->getLabelValue(LanguageLabels::SMALL_NEWSLETTER_SUBMIT)));
            $form = new Form($formHead, $formItems);
        }
        return $form;
    }

    /**
     * This static method returns the blog subscribe form.
     *
     * @throws CommerceException
     * 
     * @param string $type FormFactory::BLOG_SUBSCRIBE | FormFactory::BLOG_CATEGORY_SUBSCRIBE | FormFactory::BLOG_POST_SUBSCRIBE
     * @param int $address
     * 
     * @return NULL|Form
     */
    public static function getBlogSubscribe(string $type, int $id = 0): ?Form {
        $languageSheet = self::getLanguage();
        $blogSettings = Loader::service(Services::SETTINGS)->getBlogSettings();
        $formItems = [];
        if ($type === self::BLOG_SUBSCRIBE) {
            if (!$blogSettings->getAllowSubscriptions()) {
                return null;
            }
            $formHead = (new FormHead(RoutePaths::getPath(InternalBlog::SUBSCRIBE), 'blogSubscribeForm'))->setId('blogSubscribeForm')->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);
        } else {
            $formItems[] = new FormItem(Parameters::ID, (new InputHidden($id))->setId('blogSubscribeId_' . $id));
            if ($type === self::BLOG_CATEGORY_SUBSCRIBE) {
                if (!$blogSettings->getAllowCategorySubscriptions()) {
                    return null;
                }
                $formHead = (new FormHead(RoutePaths::getPath(InternalBlog::CATEGORY_SUBSCRIBE), 'blogCategorySubscribeForm'))->setId('blogCategorySubscribeForm');
            } elseif ($type === self::BLOG_POST_SUBSCRIBE) {
                if (!$blogSettings->getAllowPostSubscriptions()) {
                    return null;
                }
                $formHead = (new FormHead(RoutePaths::getPath(InternalBlog::POST_SUBSCRIBE), 'blogPostSubscribeForm'))->setId('blogPostSubscribeForm');
            } else {
                throw new CommerceException(self::class . 'Undefined Blog Subscribe Type: ' . $type, CommerceException::FORM_FACTORY_UNDEFINED_BLOG_SUBSCRIBE_TYPE);
            }
        }
        $formHead->setAutocomplete(Input::AUTOCOMPLETE_ON)->setMethod(FormHead::METHOD_POST)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);
        $formItems[] = new FormItem(Parameters::TYPE, new InputHidden($type));
        $formItems[] = new FormItem(Parameters::EMAIL, (new InputEmail())->setPlaceholder($languageSheet->getLabelValue(LanguageLabels::EMAIL))->setId('blogSubcribeEmailField')->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)->setRequired(true));
        $formItems[] = new FormItem(Form::SUBMIT, (new ButtonSubmit())->setId('subscribeSubmit')->setClass(self::CLASS_WILDCARD)->setContentText($languageSheet->getLabelValue(LanguageLabels::SMALL_NEWSLETTER_SUBMIT)));
        $form = new Form($formHead, $formItems);
        if (self::getConfiguration()->getForms()->getUseCaptcha()->getBlogSubscribe()) {
            $form->addCaptcha();
        };
        return $form;
    }

    /**
     * This static method returns the 'stock alert' Form.
     *
     * @return Form
     */
    public static function getStockAlert(): Form {
        $languageSheet = self::getLanguage();
        $formHead = (new FormHead(RoutePaths::getPath(InternalProduct::SUBSCRIBE_STOCK), 'productSubscribeStockForm'))->setAutocomplete(Input::AUTOCOMPLETE_ON)->setMethod(FormHead::METHOD_POST)->setId('productSubscribeStockForm')->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)->setClass(self::CLASS_WILDCARD);
        $formItems = [];
        $formItems[] = new FormItem(Parameters::EMAIL, (new InputEmail())->setLabelFor($languageSheet->getLabelValue(LanguageLabels::EMAIL))->setId('stockAlertInputEmail')->setClass('formField productStockAlertField stockAlertFormInput stockAlertFormInputEmail ' . self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)->setRequired(true));
        $formItems[] = new FormItem(Parameters::COMBINATION_ID, new InputHidden());
        $formItems[] = new FormItem(Form::SUBMIT, (new ButtonSubmit())->setClass('formButton stockAlertFormInput stockAlertFormInputButton ' . self::CLASS_WILDCARD)->setId('stockAlertSubmit')->setContentText($languageSheet->getLabelValue(LanguageLabels::SEND)));
        $form = new Form($formHead, $formItems);
        if (self::getConfiguration()->getForms()->getUseCaptcha()->getStockAlert()) {
            $form->addCaptcha();
        };
        return $form;
    }

    /**
     * This static method returns the 'update password' Form.
     *
     * @return Form
     */
    public static function getUpdatePassword(): Form {
        $languageSheet = self::getLanguage();
        $formHead = (new FormHead(RoutePaths::getPath(InternalUser::UPDATE_PASSWORD), 'updatePasswordForm'))->setId('updatePasswordForm')->setAutocomplete(Input::AUTOCOMPLETE_OFF)->setMethod(FormHead::METHOD_POST)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);
        $formItems = [];
        $formItems[] = new FormItem(Parameters::PASSWORD, ((new InputPassword())->setId('oldPasswordField')->setRequired(true)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::OLD_PASSWORD))->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)));
        $formItems[] = new FormItem(Parameters::NEW_PASSWORD, ((new InputPassword())->setId('passwordField')->setRequired(true)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::NEW_PASSWORD))->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)));
        $formItems[] = new FormItem(Parameters::NEW_PASSWORD_RETYPE, ((new InputPassword())->setId('retypePasswordField')->setRequired(true)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::RETYPE_PASSWORD))->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)));
        $formItems[] = new FormItem(Form::SUBMIT, (new ButtonSubmit())->setClass(self::CLASS_WILDCARD)->setId('updatePasswordSubmit')->setContentText($languageSheet->getLabelValue(LanguageLabels::CONTINUE)));
        $form = new Form($formHead, $formItems);
        if (self::getConfiguration()->getForms()->getUseCaptcha()->getUpdatePassword()) {
            $form->addCaptcha();
        };
        return $form;
    }

    /**
     * This static method returns the 'new password' Form.
     *
     * @return Form
     */
    public static function getNewPassword(string $hash = ''): Form {
        $languageSheet = self::getLanguage();
        $formHead = (new FormHead(RoutePaths::getPath(InternalUser::NEW_PASSWORD), 'newPasswordForm'))->setId('newPasswordForm')->setAutocomplete(Input::AUTOCOMPLETE_OFF)->setMethod(FormHead::METHOD_POST)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);
        $formItems = [];
        $formItems[] = new FormItem(Parameters::HASH, (new InputHidden($hash)));
        $formItems[] = new FormItem(Parameters::NEW_PASSWORD, ((new InputPassword())->setId('passwordField')->setRequired(true)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::NEW_PASSWORD))->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)));
        $formItems[] = new FormItem(Parameters::NEW_PASSWORD_RETYPE, ((new InputPassword())->setId('retypePasswordField')->setRequired(true)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::RETYPE_PASSWORD))->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)));
        $formItems[] = new FormItem(Form::SUBMIT, (new ButtonSubmit())->setClass(self::CLASS_WILDCARD)->setId('changePasswordSubmit')->setContentText($languageSheet->getLabelValue(LanguageLabels::CONTINUE)));
        $form = new Form($formHead, $formItems);
        if (self::getConfiguration()->getForms()->getUseCaptcha()->getNewPassword()) {
            $form->addCaptcha();
        };
        return $form;
    }

    /**
     * This static method returns the products 'comment' Form.
     *
     * @param int $id
     *            If set, it autofills the corresponding input of the form.
     * @param string $nick           
     * 
     * 
     * @return Form
     */
    public static function getComment(int $id = 0, string $nick = ''): Form {
        $form = self::getCommentForm($id, $nick, InternalProduct::ADD_COMMENT, 'productCommentForm', 'productCommentsNickField');
        if (self::getConfiguration()->getForms()->getUseCaptcha()->getComment()) {
            $form->addCaptcha();
        };
        return $form;
    }

    /**
     * This static method returns the posts 'comment' Form.
     *
     * @param int $id
     *            If set, it autofills the corresponding input of the form.
     * @param string $nick
     *            If set, it autofills the corresponding input of the form.
     *            
     * @return Form
     */
    public static function getPostComment(int $id = 0, string $nick = ''): Form {
        $form =  self::getCommentForm($id, $nick, InternalBlog::ADD_COMMENT, 'blogPostCommentForm', 'postCommentsNickField');
        if (self::getConfiguration()->getForms()->getUseCaptcha()->getPostComment()) {
            $form->addCaptcha();
        };
        return $form;
    }

    private static function getCommentForm(int $id, string $nick, string $type, string $formId, string $fieldName): Form {
        $languageSheet = self::getLanguage();
        $formHead = (new FormHead(RoutePaths::getPath($type), $formId . $id))->setId($formId . $id)->setAutocomplete(Input::AUTOCOMPLETE_ON)->setMethod(FormHead::METHOD_POST)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);
        $formItems = [];
        $formItems[] = new FormItem(Parameters::ID, (new InputHidden($id))->setId($formId . '_' . $id));
        $formItems[] = new FormItem(Parameters::RATING, (new InputHidden(''))->setAttributeWildcard(self::ATTRIBUTE_WILDCARD));
        $formItems[] = new FormItem(Parameters::IP_STRICT, (new InputHidden(''))->setAttributeWildcard(self::ATTRIBUTE_WILDCARD));
        $formItems[] = new FormItem(Parameters::NICK, (new InputText($nick))->setLabelFor($languageSheet->getLabelValue(LanguageLabels::VALORATE_NICK))->setId($fieldName)->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)->setRequired(true));
        $formItems[] = new FormItem(Parameters::COMMENT, (new Textarea())->setLabelFor($languageSheet->getLabelValue(LanguageLabels::COMMENT))->setId('comment')->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)->setRequired(true));
        $formItems[] = new FormItem(Form::SUBMIT, (new ButtonSubmit())->setClass(self::CLASS_WILDCARD)->setId($formId . 'Submit')->setContentText($languageSheet->getLabelValue(LanguageLabels::SEND_COMMENT)));
        return new Form($formHead, $formItems);
    }

    /**
     * This static method returns the 'search' Form.
     *
     * @param bool $small
     *            Set classes and attributes to show version normal or small
     * @param string $formSubmitPath
     *            If set, it fill the submit url path. Default value RoutePaths::getPath(RouteType::SEARCH)
     * 
     * @return Form
     */
    public static function getSearch(bool $small = true, ?string $formSubmitPath = null): Form {
        if (is_null($formSubmitPath)) {
            $formSubmitPath = RoutePaths::getPath(RouteType::SEARCH);
        }
        if ($small) {
            $search = 'smallSearch';
        } else {
            $search = 'search';
        }
        $languageSheet = self::getLanguage();
        $formHead = (new FormHead($formSubmitPath,  $search . 'Form'))->setAutocomplete(Input::AUTOCOMPLETE_OFF)->setMethod(FormHead::METHOD_GET)->setId($search . 'Form')->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);
        $formItems = [];
        $formItems[] = new FormItem(Parameters::Q, (new InputText())->setLabelFor($languageSheet->getLabelValue(LanguageLabels::SEARCH))->setId($search . 'CriteriaField')->setClass('moduleField searchField ' . self::CLASS_WILDCARD)->setPlaceholder($languageSheet->getLabelValue(LanguageLabels::SEARCH_CRITERIA_PLACEHOLDER)));
        $formItems[] = new FormItem(Form::SUBMIT, (new ButtonSubmit())->setClass('moduleButton searchButton ' . self::CLASS_WILDCARD)->setId($search . 'Submit')->setContentText($languageSheet->getLabelValue(LanguageLabels::SEARCH_SUBMIT))->setAttributeWildcard(self::ATTRIBUTE_WILDCARD));
        $form = new Form($formHead, $formItems);
        $form->setTemplate($search);
        return $form;
    }

    /**
     * This static method returns the 'send shopping list' Form.
     *
     * @return Form
     */
    public static function getSendShoppingListRows(int $shoppingListId = 0, string $optionPriceMode = ''): Form {
        $languageSheet = self::getLanguage();
        $formHead = (new FormHead(RoutePaths::getPath(InternalUser::SEND_SHOPPING_LIST_ROWS), 'SendShoppingListRowsForm'))->setAutocomplete(Input::AUTOCOMPLETE_ON)->setMethod(FormHead::METHOD_POST)->setId('SendShoppingListRowsForm')->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);
        $formItems = [];
        $formItems[] = new FormItem(Parameters::NAME, (new InputText(''))->setMaxlength(50)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::YOUR_NAME))->setId('SendShoppingListRowsEmailField')->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)->setRequired(true));
        $formItems[] = new FormItem(Parameters::EMAIL, (new InputEmail(''))->setLabelFor($languageSheet->getLabelValue(LanguageLabels::YOUR_EMAIL))->setId('SendShoppingListRowsNameField')->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)->setRequired(true));
        $formItems[] = new FormItem(Parameters::TO_EMAIL, (new InputEmail())->setLabelFor($languageSheet->getLabelValue(LanguageLabels::FRIEND_EMAIL))->setId('SendShoppingListRowsToEmailField')->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)->setRequired(true));
        $formItems[] = new FormItem(Parameters::TO_NAME, (new InputText())->setMaxlength(50)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::FRIEND_NAME))->setId('SendShoppingListRowsToNameField')->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)->setRequired(true));
        $formItems[] = new FormItem(Parameters::COMMENT, (new Textarea())->setMaxlength(4000)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::SEND_SHOPPING_LIST_ROWS_MESSAGE))->setId('SendShoppingListRowsCommentField')->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)->setRequired(true));
        $formItems[] = new FormItem(Parameters::ITEMS, (new InputHidden('')));
        $formItems[] = new FormItem(Parameters::SHOPPING_LIST_ID, (new InputHidden($shoppingListId > 0 ? $shoppingListId : '')));
        $formItems[] = new FormItem(Parameters::OPTION_PRICE_MODE, (new InputHidden(strlen($optionPriceMode) > 0 ? $optionPriceMode : '')));
        $formItems[] = new FormItem(Form::SUBMIT, (new ButtonSubmit())->setClass(self::CLASS_WILDCARD)->setId('itemRecommendSubmit')->setContentText($languageSheet->getLabelValue(LanguageLabels::SEND_SHOPPING_LIST_ROWS_SUBMIT)));
        $form = new Form($formHead, $formItems);
        if (self::getConfiguration()->getForms()->getUseCaptcha()->getSendShoppingListRows()) {
            $form->addCaptcha();
        };
        return $form;
    }

    /**
     * This static method returns the 'send wishlist' Form.
     *
     * @return Form
     * @deprecated
     */
    public static function getSendWishlist(): Form {
        //trigger_error("The function 'getSendWishlist' will be deprecated soon. you must use 'getSendShoppingListRows'", E_USER_NOTICE);
        $languageSheet = self::getLanguage();
        $formHead = (new FormHead(RoutePaths::getPath(InternalUser::SEND_WISHLIST), 'sendWishlistForm'))->setAutocomplete(Input::AUTOCOMPLETE_ON)->setMethod(FormHead::METHOD_POST)->setId('sendWishlistForm')->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);
        $formItems = [];
        $formItems[] = new FormItem(Parameters::TO_EMAIL, (new InputEmail())->setLabelFor($languageSheet->getLabelValue(LanguageLabels::FRIEND_EMAIL))->setId('sendWishlistEmailField')->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)->setRequired(true));
        $formItems[] = new FormItem(Parameters::TO_NAME, (new InputText())->setLabelFor($languageSheet->getLabelValue(LanguageLabels::FRIEND_NAME))->setId('sendWishlistNameField')->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)->setRequired(true));
        $formItems[] = new FormItem(Parameters::COMMENT, (new Textarea())->setLabelFor($languageSheet->getLabelValue(LanguageLabels::SEND_WISHLIST_MESSAGE))->setId('sendWishlistCommentField')->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)->setRequired(true));
        $formItems[] = new FormItem(Parameters::PRODUCT_ID_LIST, (new InputHidden('')));
        $formItems[] = new FormItem(Form::SUBMIT, (new ButtonSubmit())->setClass(self::CLASS_WILDCARD)->setId('productRecommendSubmit')->setContentText($languageSheet->getLabelValue(LanguageLabels::SEND_WISHLIST_SUBMIT)));
        return new Form($formHead, $formItems);
    }

    /**
     * This static method returns the 'send mail' Form.
     *  
     * string $typeFormFields
     * 
     * @return Form
     */
    public static function getSendMail(string $typeFormFields = 'default'): Form {
        $languageSheet = self::getLanguage();
        $formHead = (new FormHead(RoutePaths::getPath(InternalResources::SEND_MAIL), 'sendMailForm'))->setAutocomplete(Input::AUTOCOMPLETE_ON)->setMethod(FormHead::METHOD_POST)->setId('sendMailForm')->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);
        $formFields = self::getConfiguration()->getCommerce()->getSendMailFormFields($typeFormFields);
        $formFields[] = new FormItem(Form::SUBMIT, (new ButtonSubmit())->setClass(self::CLASS_WILDCARD)->setId('sendMailSubmit')->setContentText($languageSheet->getLabelValue(LanguageLabels::SEND_MAIL_SUBMIT)));
        $form = new Form($formHead, $formFields);
        if (self::getConfiguration()->getForms()->getUseCaptcha()->getSendMail()) {
            $form->addCaptcha();
        };
        return $form;
    }

    /**
     * This static method returns the 'delete shopping list rows' Form.
     *
     * @return Forms
     */
    public static function getDeleteShoppingListRows(int $shoppingListId = 0): Form {
        $languageSheet = self::getLanguage();
        $formId = 'deleteShoppingListRowsForm';
        $formHead = (new FormHead(RoutePaths::getPath(InternalUser::DELETE_SHOPPING_LIST_ROWS), $formId))->setAutocomplete(Input::AUTOCOMPLETE_ON)->setMethod(FormHead::METHOD_POST)->setId($formId)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);
        $formItems = [];
        $formItems[] = new FormItem(Parameters::SHOPPING_LIST_ID, (new InputHidden($shoppingListId > 0 ? $shoppingListId : '')));
        $formItems[] = new FormItem(Parameters::PRODUCT_ID_LIST, (new InputHidden(''))->setFilterInput(FilterInputFactory::getIdListFilterInput()));
        $formItems[] = new FormItem(Parameters::BUNDLE_ID_LIST, (new InputHidden(''))->setFilterInput(FilterInputFactory::getIdListFilterInput()));
        $formItems[] = new FormItem(Parameters::ROW_ID_LIST, (new InputHidden(''))->setFilterInput(FilterInputFactory::getIdListFilterInput()));
        $formItems[] = new FormItem(Form::SUBMIT, (new ButtonSubmit())->setClass(self::CLASS_WILDCARD)->setId('productDeleteSubmit')->setContentText($languageSheet->getLabelValue(LanguageLabels::DELETE_SHOPPING_LIST_ROWS_SUBMIT)));
        $form = new Form($formHead, $formItems);
        if (self::getConfiguration()->getForms()->getUseCaptcha()->getDeleteShoppingListRows()) {
            $form->addCaptcha();
        };
        return $form;
    }

    /**
     * This static method returns the 'delete wishlist' Form.
     *
     * @return Form
     * @deprecated
     */
    public static function getDeleteWishlist(): Form {
        //trigger_error("The function 'getDeleteWishlist' will be deprecated soon. you must use 'getDeleteShoppingList'", E_USER_NOTICE);
        $languageSheet = self::getLanguage();
        $formHead = (new FormHead(RoutePaths::getPath(InternalUser::DELETE_WISHLIST_PRODUCT), 'deleteWishlistForm'))->setAutocomplete(Input::AUTOCOMPLETE_ON)->setMethod(FormHead::METHOD_POST)->setId('deleteWishlistForm')->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);
        $formItems = [];
        $formItems[] = new FormItem(Parameters::PRODUCT_ID_LIST, (new InputHidden(''))->setFilterInput(FilterInputFactory::getIdListFilterInput()));
        $formItems[] = new FormItem(Form::SUBMIT, (new ButtonSubmit())->setClass(self::CLASS_WILDCARD)->setId('productDeleteSubmit')->setContentText($languageSheet->getLabelValue(LanguageLabels::DELETE_WISHLIST_SUBMIT)));
        return new Form($formHead, $formItems);
    }

    /**
     * This static method returns the 'contact' Form.
     * The given parameters (firstName, lastName, email or phone) will autofill the corresponding inputs if you provide them.
     *
     * @return Form
     */
    public static function getContact(): Form {
        $languageSheet = self::getLanguage();
        $formHead = (new FormHead(RoutePaths::getPath(InternalPage::SEND_CONTACT), 'contactForm'))->setAutocomplete(Input::AUTOCOMPLETE_ON)->setMethod(FormHead::METHOD_POST)->setId('contactForm')->setOnsubmit('return false;')->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);
        $formItems = [];
        $formItems[] = new FormItem(Parameters::REDIRECT, (new InputHidden(''))->setId('redirectContact'));

        $contactFormTC = self::getConfiguration()->getForms()->getContact();
        $fields = $contactFormTC->getFields()->getSortFilterArrayFormFields();
        foreach ($fields as $field => $formField) {
            $inputElement = null;
            switch ($field) {
                case FormFieldsContact::FIRST_NAME:
                    $inputElement = (new InputText())->setLabelFor($languageSheet->getLabelValue(LanguageLabels::FIRST_NAME))->setId('contactFirstNameField');
                    break;
                case FormFieldsContact::LAST_NAME:
                    $inputElement = (new InputText())->setLabelFor($languageSheet->getLabelValue(LanguageLabels::LAST_NAME))->setId('contactLastNameField');
                    break;
                case FormFieldsContact::COMPANY:
                    $inputElement = (new InputText())->setLabelFor($languageSheet->getLabelValue(LanguageLabels::COMPANY))->setId('contactCompanyField');
                    break;
                case FormFieldsContact::ADDRESS:
                    $inputElement = (new InputText())->setLabelFor($languageSheet->getLabelValue(LanguageLabels::ADDRESS))->setId('contactAddressField');
                    break;
                case FormFieldsContact::ADDRESS_ADDITIONAL_INFORMATION:
                    $inputElement = (new InputText())->setLabelFor($languageSheet->getLabelValue(LanguageLabels::ADDRESS_ADDITIONAL_INFORMATION))->setId('contactAddressAdditionalInformationField');
                    break;
                case FormFieldsContact::NUMBER:
                    $inputElement = (new InputText())->setLabelFor($languageSheet->getLabelValue(LanguageLabels::NUMBER))->setId('contactNumberField');
                    break;
                case FormFieldsContact::CITY:
                    $inputElement = (new InputText())->setLabelFor($languageSheet->getLabelValue(LanguageLabels::CITY))->setId('contactCityField');
                    break;
                case FormFieldsContact::STATE:
                    $inputElement = (new InputText())->setLabelFor($languageSheet->getLabelValue(LanguageLabels::STATE))->setId('contactStateField');
                    break;
                case FormFieldsContact::POSTAL_CODE:
                    $inputElement = (new InputText())->setLabelFor($languageSheet->getLabelValue(LanguageLabels::POSTAL_CODE))->setId('contactPostalCodeField');
                    break;
                case FormFieldsContact::VAT:
                    $inputElement = (new InputText())->setLabelFor($languageSheet->getLabelValue(LanguageLabels::VAT))->setId('contactVatField');
                    break;
                case FormFieldsContact::NIF:
                    $inputElement = (new InputText())->setLabelFor($languageSheet->getLabelValue(LanguageLabels::NIF))->setId('contactNifField');
                    break;
                case FormFieldsContact::PHONE:
                    $inputElement = (new InputTel())->setLabelFor($languageSheet->getLabelValue(LanguageLabels::PHONE))->setId('contactPhoneField');
                    break;
                case FormFieldsContact::MOBILE:
                    $inputElement = (new InputTel())->setLabelFor($languageSheet->getLabelValue(LanguageLabels::MOBILE))->setId('contactMobileField');
                    break;
                case FormFieldsContact::FAX:
                    $inputElement = (new InputTel())->setLabelFor($languageSheet->getLabelValue(LanguageLabels::FAX))->setId('contactFaxField');
                    break;
                case FormFieldsContact::EMAIL:
                    $inputElement = (new InputEmail())->setLabelFor($languageSheet->getLabelValue(LanguageLabels::EMAIL))->setId('contactEmailField');
                    break;
                case FormFieldsContact::MOTIVE_ID:
                    $queryMotives = Loader::service(Services::CONTACT)->getQueryMotives();
                    $options = [];
                    if ($contactFormTC->getDisabledMotive()) {
                        $options[] = (new Option(Language::getInstance()->getLabelValue(LanguageLabels::LOCATION_SELECT_AN_OPTION)))->setDisabled(true);
                    }
                    foreach ($queryMotives as $motive) {
                        $options[] = (new Option($motive->getDescription()))->setValue($motive->getId())->setData($motive);
                    }
                    $inputElement = (new Select($options))->setLabelFor($languageSheet->getLabelValue(LanguageLabels::CONTACT_MOTIVE))->setId('contactMotiveField');
                    break;
                case FormFieldsContact::COMMENT:
                    $inputElement = (new Textarea())->setLabelFor($languageSheet->getLabelValue(LanguageLabels::CONTACT_COMMENT))->setId('contactCommentField');
                    break;
                case FormFieldsContact::SEPARATOR:
                    $inputElement = (new Separator())->setLabelFor($languageSheet->getLabelValue(LanguageLabels::CONTACT_FORM_SEPARATOR, ''));
                    break;
                case FormFieldsContact::SEPARATOR_2:
                    $inputElement = (new Separator())->setLabelFor($languageSheet->getLabelValue(LanguageLabels::CONTACT_FORM_SEPARATOR_2, ''));
                    break;
                case FormFieldsContact::SEPARATOR_3:
                    $inputElement = (new Separator())->setLabelFor($languageSheet->getLabelValue(LanguageLabels::CONTACT_FORM_SEPARATOR_3, ''));
                    break;
            }
            if (!is_null($inputElement)) {
                $inputElement = $inputElement->setClass('contactField ' . self::CLASS_WILDCARD);
                $inputElement = $inputElement->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);
                if (method_exists($inputElement, 'setRequired')) {
                    $inputElement = $inputElement->setRequired($formField->getRequired());
                }
                if (method_exists($inputElement, 'setRegex')) {
                    $inputElement = $inputElement->setRegex($formField->getRegex());
                }
                $formItems[] = (new FormItem($field, $inputElement));
            }
        }
        $formItems[] = new FormItem(Form::SUBMIT, (new ButtonSubmit())->setClass('contactField ' . self::CLASS_WILDCARD)->setId('contactSubmit')->setContentText($languageSheet->getLabelValue(LanguageLabels::SEND)));
        $form = new Form($formHead, $formItems);
        if (self::getConfiguration()->getForms()->getUseCaptcha()->getContact()) {
            $form->addCaptcha();
        };
        return $form;
    }

    /**
     * This static method returns the return products Form.
     *
     * @param ElementCollection $returnProducts
     *            It autofills the corresponding input of the form.
     * @param ElementCollection $returnPoints
     *            If set, it autofills the corresponding input of the form, else generate an input hidden than will be fill with the corresponding return delivery id
     * 
     * @return Form
     */
    public static function getReturnRequest(int $id, ElementCollection $returnProducts, ElementCollection $returnPoints, ElementCollection $rmaReasons): Form {
        $languageSheet = self::getLanguage();
        $formHead = (new FormHead(RoutePaths::getPath(InternalUser::RETURN_REQUEST), 'returnRequestForm'))->setAutocomplete(Input::AUTOCOMPLETE_ON)->setMethod(FormHead::METHOD_POST)->setId('returnRequestForm' . $id)
            ->setData([self::RETURN_PRODUCTS => $returnProducts->toArray(), self::RETURN_POINTS => $returnPoints->toArray(), self::RMA_REASONS => $rmaReasons->toArray()])
            ->setOnsubmit('return false;')->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);
        $reasons = [];
        foreach ($rmaReasons->getItems() as $rmaReason) {
            $reasons[] = (new Option($rmaReason->getLanguage()->getDescription()))->setValue($rmaReason->getId())->setData($rmaReason);
        }
        $formItems = [];
        foreach ($returnProducts->getItems() as $product) {
            if (!is_null($product)) {
                $formItems[] = new FormItem(Parameters::RETURN_CHECK . '_' . $product->getHash(), (new InputCheckbox('1'))->setLabelFor($languageSheet->getLabelValue(LanguageLabels::RETURN_CHECK))->setId('returnCheckField_' . $product->getHash())->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD));
                $inputElement = (new InputNumber($product->getQuantity()))->setLabelFor($languageSheet->getLabelValue(LanguageLabels::RETURN_QUANTITY))
                    ->setMin(1)->setMax($product->getQuantity())
                    ->setClass('returnQuantity ' . self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)
                    ->setRequired(true)->setDisabled(true);
                $inputElement->setFilterInput(new FilterInput([
                    FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => FALSE,
                    FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_INT
                ]));
                $inputElement->setData($product);
                $formItems[] = new FormItem(Parameters::RETURN_QUANTITY . '_' . $product->getHash(), $inputElement);
            }
            if (!empty($reasons)) {
                $formItems[] = new FormItem(
                    Parameters::RMA_REASON_ID . '_' . $product->getHash(),
                    (new Select($reasons, null, ''))
                        ->setLabelFor($languageSheet->getLabelValue(LanguageLabels::RMA_REASON))
                        ->setDisabled(true)
                        ->setClass('rmReasonId ' . self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)
                );
                $formItems[] = new FormItem(
                    Parameters::RMA_REASON_COMMENT . '_' . $product->getHash(),
                    (new Textarea())->setLabelFor($languageSheet->getLabelValue(LanguageLabels::RMA_REASON_COMMENT))
                        ->setId(Parameters::RMA_REASON_COMMENT . '_' . $product->getHash())->setClass('returnRmaReasonComment ' . self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)
                        ->setDisabled(true)
                        ->setRequired(false)
                );
            }
        }

        $formItems[] = new FormItem(
            Parameters::RETURN_COMMENT,
            (new Textarea())->setLabelFor($languageSheet->getLabelValue(LanguageLabels::RETURN_COMMENT))->setId('returnCommentField')->setClass('returnComment ' . self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)
                ->setDisabled(true)
        );

        $formItems[] = new FormItem(Parameters::ID, (new InputHidden($id))->setId('returnRequestId_' . $id));
        $dataOptions = [];
        $options = [];
        $options[0] = $languageSheet->getLabelValue(LanguageLabels::RETURN_DELIVERY_SHIPPING);
        $dataOptions[0] = null;
        foreach ($returnPoints->getItems() as $point) {
            $options[$point->getId()] = $point->getName();
            $dataOptions[$point->getId()] = $point;
        }
        $formItems[] = new FormItem(Parameters::RETURN_DELIVERY, (new InputRadio($options, null, 0))->setData($dataOptions)->setId('returnDelivery')->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)->setRequired(true));
        $formItems[] = new FormItem(Form::SUBMIT, (new ButtonSubmit())->setClass('returnField ' . self::CLASS_WILDCARD)->setId('returnSubmitContainer')->setContentText($languageSheet->getLabelValue(LanguageLabels::SEND)));
        $form = new Form($formHead, $formItems);
        if (self::getConfiguration()->getForms()->getUseCaptcha()->getReturnRequest()) {
            $form->addCaptcha();
        };
        return $form;
    }

    /**
     * This static method returns the sales agent customer Form.
     * 
     * @param string $q
     * @param ?DateTime $fromDate
     * @param ?DateTime $toDate
     * @param bool $includeSubordinates
     *
     * @return Form
     * @deprecated
     */
    public static function getSalesAgentCustomers(string $q = '', \DateTime $fromDate = null, \DateTime $toDate = null, $includeSubordinates = false): Form {
        $languageSheet = self::getLanguage();
        $formHead = (new FormHead(RoutePaths::getPath(InternalUser::SALES_AGENT_CUSTOMERS), 'salesAgentCustomersForm'))->setId('salesAgentCustomersForm')->setAutocomplete(Input::AUTOCOMPLETE_ON)->setMethod(FormHead::METHOD_POST)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);
        $formItems[] = new FormItem(Parameters::Q, ((new InputText($q))->setId('searchClient')->setRequired(false)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::SALES_AGENT_CUSTOMERS_SEARCH_CLIENTS))->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)));
        $formItems[] = new FormItem(Parameters::FROM_DATE, ((new InputDate((!is_null($fromDate) ? $fromDate->format(Date::DATETIME_FORMAT) : '')))
            ->setId('fromDate')->setRequired(true)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::FROM_DATE))
            ->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)));
        $formItems[] = new FormItem(Parameters::TO_DATE, ((new InputDate((!is_null($toDate) ? $toDate->format(Date::DATETIME_FORMAT) : '')))
            ->setId('toDate')->setRequired(true)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::TO_DATE))
            ->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)));

        $options = [2];
        $options[0] = (new Option($languageSheet->getLabelValue(LanguageLabels::NO)))->setValue(0);
        $options[1] = (new Option($languageSheet->getLabelValue(LanguageLabels::YES)))->setValue(1);
        $formItems[] = new FormItem(Parameters::INCLUDE_SUBORDINATES, ((new Select($options, null, $includeSubordinates ? '1' : '0'))->setId('includeSubordinates')->setLabelFor($languageSheet->getLabelValue(LanguageLabels::SALES_AGENT_INCLUDE_SUBORDINATES))->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)));
        $formItems[] = new FormItem(Form::SUBMIT, (new ButtonSubmit())->setClass('searchCustomers')->setId('searchCustomersSubmit')->setContentText($languageSheet->getLabelValue(LanguageLabels::SEND)));
        $form = new Form($formHead, $formItems);
        return $form;
    }

    /**
     * This static method returns the account sales agent customer Form.
     * 
     * @param string $q
     * @param ?DateTime $fromDate
     * @param ?DateTime $toDate
     *
     * @return Form
     */
    public static function getAccountSalesAgentCustomers(string $q = '', \DateTime $fromDate = null, \DateTime $toDate = null, $includeSubordinates = false): Form {
        $languageSheet = self::getLanguage();
        $formHead = (new FormHead(RoutePaths::getPath(RouteType::REGISTERED_USER_SALES_AGENT_CUSTOMERS), 'salesAgentCustomersForm'))->setId('salesAgentCustomersForm')->setAutocomplete(Input::AUTOCOMPLETE_ON)->setMethod(FormHead::METHOD_POST)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);
        $formItems[] = new FormItem(Parameters::Q, ((new InputText($q))->setId('searchClient')->setRequired(false)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::ADVCA_SALES_AGENT_CUSTOMERS_SEARCH_CLIENTS))->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)));
        $formItems[] = new FormItem(Parameters::FROM_DATE, ((new InputDate((!is_null($fromDate) ? $fromDate->format(Date::DATETIME_FORMAT) : '')))
            ->setId('fromDate')->setRequired(true)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::FROM_DATE))
            ->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)));
        $formItems[] = new FormItem(Parameters::TO_DATE, ((new InputDate((!is_null($toDate) ? $toDate->format(Date::DATETIME_FORMAT) : '')))
            ->setId('toDate')->setRequired(true)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::TO_DATE))
            ->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)));

        $options = [2];
        $options[0] = (new Option($languageSheet->getLabelValue(LanguageLabels::NO)))->setValue(0);
        $options[1] = (new Option($languageSheet->getLabelValue(LanguageLabels::YES)))->setValue(1);
        $formItems[] = new FormItem(Parameters::INCLUDE_SUBORDINATES, ((new Select($options, null, $includeSubordinates ? '1' : '0'))->setId('includeSubordinates')->setLabelFor($languageSheet->getLabelValue(LanguageLabels::SALES_AGENT_INCLUDE_SUBORDINATES))->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)));
        $formItems[] = new FormItem(Form::SUBMIT, (new ButtonSubmit())->setClass('searchCustomers')->setId('searchCustomersSubmit')->setContentText($languageSheet->getLabelValue(LanguageLabels::SEND)));
        $form = new Form($formHead, $formItems);
        return $form;
    }

    /**
     * This static method returns the sales agent sales Form.
     * 
     * @param ?DateTime $fromDate
     * @param ?DateTime $toDate
     *
     * @return Form
     * @deprecated
     */
    public static function getSalesAgentSales(\DateTime $fromDate = null, \DateTime $toDate = null): Form {
        $languageSheet = self::getLanguage();
        $formHead = (new FormHead(RoutePaths::getPath(InternalUser::SALES_AGENT_SALES), 'salesAgentSalesForm'))->setId('salesAgentSalesForm')->setAutocomplete(Input::AUTOCOMPLETE_ON)->setMethod(FormHead::METHOD_POST)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);
        $formItems[] = new FormItem(Parameters::FROM_DATE, ((new InputDate((!is_null($fromDate) ? $fromDate->format(Date::DATETIME_FORMAT) : '')))
            ->setId('fromDate')->setLabelFor($languageSheet->getLabelValue(LanguageLabels::FROM_DATE))->setRequired(true)
            ->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)));
        $formItems[] = new FormItem(Parameters::TO_DATE, ((new InputDate((!is_null($toDate) ? $toDate->format(Date::DATETIME_FORMAT) : '')))
            ->setId('toDate')->setLabelFor($languageSheet->getLabelValue(LanguageLabels::TO_DATE))->setRequired(true)
            ->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)));
        $formItems[] = new FormItem(Form::SUBMIT, (new ButtonSubmit())->setClass('searchSales')->setId('searchSalesSubmit')->setContentText($languageSheet->getLabelValue(LanguageLabels::SEND)));
        $form = new Form($formHead, $formItems);
        return $form;
    }

    /**
     * This static method returns the sales agent sales Form.
     * 
     * @param ?DateTime $fromDate
     * @param ?DateTime $toDate
     *
     * @return Form
     */
    public static function getAccountSalesAgentSales(\DateTime $fromDate = null, \DateTime $toDate = null): Form {
        $languageSheet = self::getLanguage();
        $formHead = (new FormHead(RoutePaths::getPath(RouteType::REGISTERED_USER_SALES_AGENT_SALES), 'salesAgentSalesForm'))->setId('salesAgentSalesForm')->setAutocomplete(Input::AUTOCOMPLETE_ON)->setMethod(FormHead::METHOD_POST)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);
        $formItems[] = new FormItem(Parameters::FROM_DATE, ((new InputDate((!is_null($fromDate) ? $fromDate->format(Date::DATETIME_FORMAT) : '')))
            ->setId('fromDate')->setLabelFor($languageSheet->getLabelValue(LanguageLabels::FROM_DATE))->setRequired(true)
            ->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)));
        $formItems[] = new FormItem(Parameters::TO_DATE, ((new InputDate((!is_null($toDate) ? $toDate->format(Date::DATETIME_FORMAT) : '')))
            ->setId('toDate')->setLabelFor($languageSheet->getLabelValue(LanguageLabels::TO_DATE))->setRequired(true)
            ->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)));
        $formItems[] = new FormItem(Form::SUBMIT, (new ButtonSubmit())->setClass('searchSales')->setId('searchSalesSubmit')->setContentText($languageSheet->getLabelValue(LanguageLabels::SEND)));
        $form = new Form($formHead, $formItems);
        return $form;
    }

    /**
     * This static method returns the physical locations Form.
     *
     * @return Form
     */
    public static function getPhysicalLocations(): Form {
        $formId = 'physicalLocationsForm';
        $formHead = (new FormHead(RoutePaths::getPath(InternalUser::SALES_AGENT_SALES), $formId))
            ->setId($formId)
            ->setAutocomplete(Input::AUTOCOMPLETE_OFF)
            ->setMethod(FormHead::METHOD_POST)
            ->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);
        $languageSheet = self::getLanguage();
        $formItems = [];
        $options = [
            (new Option($languageSheet->getLabelValue(LanguageLabels::COUNTRY)))
        ];
        foreach (Application::getInstance()->getCountriesSettings($languageSheet->getLanguage()) as $country) {
            $options[] = (new Option($country->getName()))->setValue($country->getCode())->setData($country);
        }
        $formItems[] = new FormItem(Parameters::COUNTRY, (new Select($options, null, ''))->setLabelFor($languageSheet->getLabelValue(LanguageLabels::COUNTRY))->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD));
        $formItems[] = new FormItem(Parameters::STATE, (new Select([], null, ''))->setLabelFor($languageSheet->getLabelValue(LanguageLabels::STATE))->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD));
        $formItems[] = new FormItem(Parameters::CITY, (new Select([], null, ''))->setLabelFor($languageSheet->getLabelValue(LanguageLabels::CITY))->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD));
        $formItems[] = new FormItem(Form::SUBMIT, (new ButtonSubmit())->setClass('physicalLocations')->setId('physicalLocationsSubmit')->setContentText($languageSheet->getLabelValue(LanguageLabels::SEND))->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD));
        return new Form($formHead, $formItems);
    }

    /**
     * This static method returns the shopping list  Form.
     * 
     * @param string $method
     *            Sets the method SDK\Core\Enums\MethodType::PUT (edit shopping list) or SDK\Core\Enums\MethodType::POST (new shopping list)
     * @param ?ShoppingList $shoppingList
     *            Sets the shoppingList to add
     * 
     * @return Form
     */
    public static function getShoppingList(string $method, ?ShoppingList $shoppingList = null): Form {
        if ($method === MethodType::POST) {
            $formFieldsShoppingList = self::getConfiguration()->getForms()->getShoppingList()->getNewFields();
        } else {
            $formFieldsShoppingList = self::getConfiguration()->getForms()->getShoppingList()->getEditFields();
        }
        $fields = $formFieldsShoppingList->getSortFilterArrayFormFields();
        $shoppingList = is_null($shoppingList) ? new ShoppingList() : $shoppingList;
        $languageSheet = self::getLanguage();
        $formId = 'shoppingListForm';
        $formHead = (new FormHead(RoutePaths::getPath(InternalUser::SET_SHOPPING_LIST), $formId))->setId($formId)->setAutocomplete(Input::AUTOCOMPLETE_ON)->setMethod(FormHead::METHOD_POST)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);
        $formItems = [];
        $formItems[] = new FormItem(Parameters::ID, (new InputHidden($shoppingList->getId())));
        $formItems[] = new FormItem(Parameters::TYPE, (new InputHidden($shoppingList->getId() > 0 ? MethodType::PUT : MethodType::POST)));
        foreach ($fields as $field => $formField) {
            $inputElement = null;
            $fieldName = $field;
            switch ($field) {
                case FormFieldsShoppingList::NAME:
                    $inputElement = (new InputText($shoppingList->getName()))->setLabelFor($languageSheet->getLabelValue(LanguageLabels::SHOPPING_LIST_NAME))->setMaxlength(255);
                    break;
                case FormFieldsShoppingList::DESCRIPTION:
                    $inputElement = (new InputText($shoppingList->getDescription()))->setLabelFor($languageSheet->getLabelValue(LanguageLabels::SHOPPING_LIST_DESCRIPTION))->setMaxlength(255);
                    break;
                case FormFieldsShoppingList::KEEP_PURCHASED_ITEMS:
                    $formItems[] = new FormItem(Parameters::KEEP_PURCHASED_ITEMS, (new InputHidden($shoppingList->getKeepPurchasedItems() ? 'true' : 'false')));
                    $inputElement = (new InputCheckbox('1'))->setLabelFor($languageSheet->getLabelValue(LanguageLabels::SHOPPING_LIST_KEEP_PURCHASED_ITEMS))->setChecked($shoppingList->getKeepPurchasedItems() ? true : false);
                    $fieldName .= 'Checkbox';
                    break;
                case FormFieldsShoppingList::DEFAULT_ONE:
                    $formItems[] = new FormItem(Parameters::DEFAULT_ONE, (new InputHidden($shoppingList->getDefaultOne() ? 'true' : 'false')));
                    $inputElement = (new InputCheckbox('1'))->setLabelFor($languageSheet->getLabelValue(LanguageLabels::SHOPPING_LIST_DEFAULT_ONE))->setChecked($shoppingList->getDefaultOne() ? true : false);
                    $fieldName .= 'Checkbox';
                    break;
                case FormFieldsShoppingList::PRIORITY:
                    $inputElement = (new InputNumber($shoppingList->getPriority()))->setLabelFor($languageSheet->getLabelValue(LanguageLabels::SHOPPING_LIST_PRIORITY))->setMin(1);
                    break;
            }
            if (!is_null($inputElement)) {
                $inputElement = $inputElement->setClass('contactField ' . self::CLASS_WILDCARD);
                $inputElement = $inputElement->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);
                if (method_exists($inputElement, 'setRequired')) {
                    $inputElement = $inputElement->setRequired($formField->getRequired());
                }
                if (method_exists($inputElement, 'setRegex')) {
                    $inputElement = $inputElement->setRegex($formField->getRegex());
                }
                $formItems[] = (new FormItem($fieldName, $inputElement));
            }
        }
        $formItems[] = new FormItem(Form::SUBMIT, (new ButtonSubmit())->setClass(self::CLASS_WILDCARD)->setId($formId . 'Submit')->setContentText($languageSheet->getLabelValue(LanguageLabels::SAVE)));
        $form = new Form($formHead, $formItems);
        if (self::getConfiguration()->getForms()->getUseCaptcha()->getShoppingList()) {
            $form->addCaptcha();
        };
        return $form;
    }

    /**
     * This static method returns the shopping list row Form.
     * 
     * @param ?ShoppingListRow $shoppingListRow
     *            Sets the shoppingListRow to add
     * 
     * @return Form
     */
    public static function getShoppingListRowNotes(?ShoppingListRow $shoppingListRow = null): Form {
        $method = is_null($shoppingListRow) ? MethodType::POST : MethodType::PUT;
        $shoppingListRow = is_null($shoppingListRow) ? new ShoppingListRow() : $shoppingListRow;
        $languageSheet = self::getLanguage();
        $labels = $languageSheet->getLabels();
        $options = [];
        foreach (Importance::getValues() as $importance) {
            $options[] = (new Option($languageSheet->getLabelValue($labels['SHOPPING_LIST_ROW_IMPORTANCE' . '_' . $importance])))->setValue($importance);
        }
        $formId = 'shoppingListRowNotesForm';
        $formHead = (new FormHead(RoutePaths::getPath(InternalUser::SET_SHOPPING_LIST_ROW), $formId))->setId($formId)->setAutocomplete(Input::AUTOCOMPLETE_ON)->setMethod(FormHead::METHOD_POST)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);
        $formItems = [];
        $formItems[] = new FormItem(Parameters::ID, (new InputHidden($shoppingListRow->getId())));
        $formItems[] = new FormItem(Parameters::SHOPPING_LIST_ID, (new InputHidden()));
        $formItems[] = new FormItem(Parameters::TYPE, (new InputHidden($method)));
        $formItems[] = new FormItem(Parameters::TEMPLATE, (new InputHidden()));
        $formFieldsShoppingList = self::getConfiguration()->getForms()->getShoppingListRowNote()->getFields();
        $fields = $formFieldsShoppingList->getSortFilterArrayFormFields();
        foreach ($fields as $field => $formField) {
            $inputElement = null;
            $fieldName = $field;
            switch ($field) {
                case FormFieldsShoppingListRowNote::COMMENT:
                    $inputElement = (new Textarea())->setMaxlength(255)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::SHOPPING_LIST_ROW_COMMENT))->setPlaceholder($languageSheet->getLabelValue(LanguageLabels::SHOPPING_LIST_ROW_COMMENT))->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);
                    break;
                case FormFieldsShoppingListRowNote::QUANTITY:
                    $inputElement = (new InputNumber($shoppingListRow->getQuantity()))->setLabelFor($languageSheet->getLabelValue(LanguageLabels::SHOPPING_LIST_ROW_QUANTITY))->setMin(1)->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);
                    break;
                case FormFieldsShoppingListRowNote::IMPORTANCE:
                    $inputElement = (new Select($options, null, $shoppingListRow->getImportance()))->setLabelFor($languageSheet->getLabelValue(LanguageLabels::SHOPPING_LIST_ROW_IMPORTANCE))->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);
                    break;
                case FormFieldsShoppingListRowNote::PRIORITY:
                    $inputElement = (new InputNumber($shoppingListRow->getPriority()))->setLabelFor($languageSheet->getLabelValue(LanguageLabels::SHOPPING_LIST_ROW_PRIORITY))->setMin(1)->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);
                    break;
            }
            if (!is_null($inputElement)) {
                $inputElement = $inputElement->setClass('contactField ' . self::CLASS_WILDCARD);
                $inputElement = $inputElement->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);
                if (method_exists($inputElement, 'setRequired')) {
                    $inputElement = $inputElement->setRequired($formField->getRequired());
                }
                if (method_exists($inputElement, 'setRegex')) {
                    $inputElement = $inputElement->setRegex($formField->getRegex());
                }
                $formItems[] = (new FormItem($fieldName, $inputElement));
            }
        }
        $formItems[] = new FormItem(Form::SUBMIT, (new ButtonSubmit())->setClass(self::CLASS_WILDCARD)->setId($formId . 'Submit')->setContentText($languageSheet->getLabelValue(LanguageLabels::SAVE)));
        $form = new Form($formHead, $formItems);
        if (self::getConfiguration()->getForms()->getUseCaptcha()->getShoppingListRowNotes()) {
            $form->addCaptcha();
        };
        return $form;
    }

    /**
     * This static method returns the countries links Form.
     *
     * @return Form
     */
    public static function getCountriesLinks(ElementCollection $countriesLinks, ?string $countryCode = null, ?string $languageCode = null): Form {
        $formId = 'countriesLinksForm';
        $formHead = (new FormHead(RoutePaths::getPath(InternalResources::SET_NAVIGATION_COUNTRY), $formId))
            ->setId($formId)
            ->setAutocomplete(Input::AUTOCOMPLETE_OFF)
            ->setMethod(FormHead::METHOD_POST)
            ->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);
        $languageSheet = self::getLanguage();
        $countries[] = (new Option($languageSheet->getLabelValue(LanguageLabels::SELECT_YOUR_COUNTRY)))->setValue('default')->setSelected(is_null($countryCode) ? true : false);
        $languages[] = (new Option($languageSheet->getLabelValue(LanguageLabels::SELECT_YOUR_LANGUAGE)))->setValue('default')->setSelected(is_null($languageCode) ? true : false);
        foreach ($countriesLinks as $countriesLink) {
            $countries[] = (new Option($countriesLink->getName()))->setValue($countriesLink->getCode())->setData($countriesLink)->setSelected(is_null($countryCode) ? false : ($countryCode == $countriesLink->getCode()));
            foreach ($countriesLink->getLanguages() as $language) {
                $languages[] = (new Option($language->getName()))->setValue($countriesLink->getCode() . '-' . $language->getCode())->setData($language)->setSelected(is_null($languageCode) ? false : ($languageCode == $language->getCode()));
            }
        }
        $formItems = [];
        $formItems[] = new FormItem(Parameters::COUNTRY_CODE, new InputHidden(''));
        $formItems[] = new FormItem(Parameters::COUNTRY, (new Select($countries, null, ''))->setLabelFor($languageSheet->getLabelValue(LanguageLabels::COUNTRY))->setRequired(true)->setDisabled(true)->setAutocomplete(Input::AUTOCOMPLETE_OFF)->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD));
        $formItems[] = new FormItem(Parameters::LANGUAGE, (new Select($languages, null, ''))->setLabelFor($languageSheet->getLabelValue(LanguageLabels::LANGUAGES))->setRequired(true)->setDisabled(true)->setAutocomplete(Input::AUTOCOMPLETE_OFF)->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD));
        $formItems[] = new FormItem(Form::SUBMIT, (new ButtonSubmit())->setClass('countriesLinks ' . self::CLASS_WILDCARD)->setId('countriesLinksSubmit')->setContentText($languageSheet->getLabelValue(LanguageLabels::SEND))->setAttributeWildcard(self::ATTRIBUTE_WILDCARD));
        return new Form($formHead, $formItems);
    }

    /**
     * This static method returns the form for switching used accounts.
     *
     * @return Form
     */
    public static function getUsedAccountSwitch(ElementCollection $registeredUserAccounts, int $currentAccountId = 0): Form {
        $formId = 'usedAccountSwitchForm';
        $formHead = (new FormHead(RoutePaths::getPath(InternalAccount::USED_ACCOUNT), $formId))
            ->setId($formId)
            ->setAutocomplete(Input::AUTOCOMPLETE_OFF)
            ->setMethod(FormHead::METHOD_POST)
            ->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);
        $languageSheet = self::getLanguage();
        $labels = $languageSheet->getLabels();

        $accountTypes = AccountType::getValues();

        $types[] = (new Option($languageSheet->getLabelValue(LanguageLabels::SELECT_YOUR_ACCOUNT_TYPE)))->setValue('default')->setSelected(true);
        $accounts[] = (new Option($languageSheet->getLabelValue(LanguageLabels::SELECT_YOUR_ACCOUNT)))->setValue('default')->setSelected(true);
        $typeAdded = [];

        foreach ($registeredUserAccounts->getItems() as $registeredUserAccount) {
            if (!$registeredUserAccount instanceof RegisteredUserAccount) {
                break;
            }

            $type = $registeredUserAccount->getAccount()->getType();
            if (in_array($type, $accountTypes)) {
                $accountValue = "";
                if (!empty($registeredUserAccount->getAccountAlias())) {
                    $accountValue .= $registeredUserAccount->getAccountAlias() . " - ";
                }
                if ($currentAccountId != $registeredUserAccount->getAccount()->getId()) {
                    $accountValue .= $registeredUserAccount->getAccount()->getName();
                    $accountValue .= $registeredUserAccount->isMaster() ? " (master)" : "";
                    $accounts[] = (new Option($accountValue))->setValue($registeredUserAccount->getAccount()->getId())->setData(["type" => $registeredUserAccount->getAccount()->getType()]);
                    if (!in_array($type, $typeAdded)) {
                        $types[] = (new Option($languageSheet->getLabelValue($labels[$type . "_TYPE"])))->setValue($type)->setData($type);
                        array_push($typeAdded, $type);
                    }
                }
            }
        }

        $formItems = [];
        $formItems[] = new FormItem(Parameters::ACCOUNT_TYPE, (new Select($types, null, ''))->setLabelFor($languageSheet->getLabelValue(LanguageLabels::ACCOUNT_TYPE_LABEL))->setRequired(true)->setAutocomplete(Input::AUTOCOMPLETE_OFF)->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD));
        $formItems[] = new FormItem(Parameters::ACCOUNT, (new Select($accounts, null, ''))->setLabelFor($languageSheet->getLabelValue(LanguageLabels::ACCOUNT_LABEL))->setRequired(true)->setDisabled(true)->setAutocomplete(Input::AUTOCOMPLETE_OFF)->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD));
        $formItems[] = new FormItem(Form::SUBMIT, (new ButtonSubmit())->setClass('usedAccount ' . self::CLASS_WILDCARD)->setId('usedAccountSubmit')->setContentText($languageSheet->getLabelValue(LanguageLabels::SEND_USE_SELECTED_ACCOUNT))->setAttributeWildcard(self::ATTRIBUTE_WILDCARD));
        $form = new Form($formHead, $formItems);
        if (self::getConfiguration()->getForms()->getUseCaptcha()->getUsedAccountSwitch()) {
            $form->addCaptcha();
        }
        return $form;
    }

    /**
     * This static method returns the account orders Form.
     * 
     * @param array $data
     *
     * @return Form
     */
    public static function getAccountOrders(array $data = []): Form {
        $languageSheet = self::getLanguage();
        $labels = $languageSheet->getLabels();
        $formHead = (new FormHead(str_replace("{" . Parameters::ID_USED . "}", AccountKey::USED, RoutePaths::getPath(RouteType::ACCOUNT_ORDERS)), 'ordersForm'))->setId('ordersForm')->setAutocomplete(Input::AUTOCOMPLETE_ON)->setMethod(FormHead::METHOD_POST)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);

        $formItems[] = new FormItem(Parameters::ADDED_FROM, ((new InputDate((!is_null($data[Parameters::ADDED_FROM]) ? (new \DateTime($data[Parameters::ADDED_FROM]))->format(Date::DATETIME_FORMAT) : '')))
            ->setId('fromDate')->setRequired(true)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::FROM_DATE))
            ->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)));

        $formItems[] = new FormItem(Parameters::ADDED_TO, ((new InputDate((!is_null($data[Parameters::ADDED_TO])) ? (new \DateTime($data[Parameters::ADDED_TO]))->format(Date::DATETIME_FORMAT) : ''))
            ->setId('toDate')->setRequired(true)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::TO_DATE))
            ->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)));

        if (is_null($data[Parameters::ONLY_CREATED_BY_ME])) {
            $data[Parameters::ONLY_CREATED_BY_ME] = 1;
        }
        $onlyCreatedByMeOptions = [
            (new Option($languageSheet->getLabelValue(LanguageLabels::NO)))->setValue(0)->setSelected($data[Parameters::ONLY_CREATED_BY_ME] == 0),
            (new Option($languageSheet->getLabelValue(LanguageLabels::YES)))->setValue(1)->setSelected($data[Parameters::ONLY_CREATED_BY_ME] == 1)
        ];
        if (is_null($data[Parameters::INCLUDE_SUBCOMPANY_STRUCTURE])) {
            $data[Parameters::INCLUDE_SUBCOMPANY_STRUCTURE] = 1;
        }

        $statusOptions = [];

        $statusIdList = !empty($data[Parameters::STATUS_ID_LIST]) ? explode(',', $data[Parameters::STATUS_ID_LIST]) : [];
        foreach (OrderStatus::getServiceValidStatuses() as $statusOption) {
            $statusOptionLabel = $languageSheet->getLabelValue($labels['STATUS' . '_' . $statusOption]);
            $statusOptions[] = (new Option($statusOptionLabel))->setValue($statusOption)->setData($statusOption)->setSelected(count($statusIdList) > 0 ? false : !empty(array_filter($statusIdList, fn($item) => strpos($item, $statusOption) !== false)));
        }

        $formItems[] = new FormItem(Parameters::ONLY_CREATED_BY_ME, (new Select($onlyCreatedByMeOptions, null, LanguageLabels::ORDER_ONLY_MY_ORDERS))->setLabelFor($languageSheet->getLabelValue(LanguageLabels::ORDER_ONLY_MY_ORDERS))->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD));

        $formItems[] = new FormItem(Parameters::STATUS_ID_LIST, (new MultiSelect($statusOptions, null, $statusIdList))->setLabelFor($languageSheet->getLabelValue(LanguageLabels::ORDER_STATUSES))->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD));

        if (
            in_array(Session::getInstance()?->getBasket()?->getAccount()?->getType(),  AccountType::getCompanyTypes()) &&
            LmsService::getAdvcaLicense() &&
            Application::getInstance()->getEcommerceSettings()->getAccountRegisteredUsersSettings()->getCardinalityPlus()
        ) {

            $includeSubCompanyStructureOptions = [
                (new Option($languageSheet->getLabelValue(LanguageLabels::NO)))->setValue(0)->setSelected($data[Parameters::INCLUDE_SUBCOMPANY_STRUCTURE] == 0),
                (new Option($languageSheet->getLabelValue(LanguageLabels::YES)))->setValue(1)->setSelected($data[Parameters::INCLUDE_SUBCOMPANY_STRUCTURE] == 1)
            ];
            $formItems[] = new FormItem(Parameters::INCLUDE_SUBCOMPANY_STRUCTURE, (new Select($includeSubCompanyStructureOptions, null, LanguageLabels::ORDER_INCLUDE_SUBSTRUCTURE_ORDERS))->setLabelFor($languageSheet->getLabelValue(LanguageLabels::ORDER_INCLUDE_SUBSTRUCTURE_ORDERS))->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD));
        }

        $formItems[] = new FormItem(Form::SUBMIT, (new ButtonSubmit())->setClass('searchOrders')->setId('searchOrdersSubmit')->setContentText($languageSheet->getLabelValue(LanguageLabels::SEND)));

        $form = new Form($formHead, $formItems);
        return $form;
    }

    /**
     * This static method returns the registered users Form.
     *
     * @param AccountRegisteredUsersParametersGroup $accountRegisteredUsersParametersGroup
     * 
     * @return Form
     */
    public static function getAccountRegisteredUsers(AccountRegisteredUsersParametersGroup $accountRegisteredUsersParametersGroup = null, array $companyRoles = []): Form {
        $parameters = [];
        if (!is_null($accountRegisteredUsersParametersGroup)) {
            $parameters = $accountRegisteredUsersParametersGroup->toArray();
        }

        $userKeyCriteria = Application::getInstance()->getEcommerceSettings()->getUserAccountsSettings()->getUserKeyCriteria();
        $q = "";
        switch ($userKeyCriteria) {
            case UserKeyCriteria::PID:
                $q = $parameters[Parameters::P_ID] ?? "";
                break;
            case UserKeyCriteria::EMAIL:
                $q = $parameters[Parameters::EMAIL] ?? "";
                break;
            case UserKeyCriteria::USERNAME:
                $q = $parameters[Parameters::USERNAME] ?? "";
                break;
        }


        $languageSheet = self::getLanguage();
        $labels = $languageSheet->getLabels();
        $formHead = (new FormHead(RoutePaths::getPath(RouteType::ACCOUNT_REGISTERED_USERS), 'registeredUsersForm'))->setId('registeredUsersForm')->setAutocomplete(Input::AUTOCOMPLETE_ON)->setMethod(FormHead::METHOD_POST)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);
        $masterOptions = [
            (new Option($languageSheet->getLabelValue(LanguageLabels::ACCOUNT_REGISTERED_USER_SEARCH_MASTER_DEFAULT)))->setValue("-"),
            (new Option($languageSheet->getLabelValue(LanguageLabels::ACCOUNT_REGISTERED_USER_SEARCH_MASTER_NO)))->setValue(0)->setData(0)->setSelected(!isset($parameters['master']) ? false : (0 == $parameters['master'])),
            (new Option($languageSheet->getLabelValue(LanguageLabels::ACCOUNT_REGISTERED_USER_SEARCH_MASTER_YES)))->setValue(1)->setData(1)->setSelected(!isset($parameters['master']) ? false : (1 == $parameters['master']))
        ];
        $statusOptions = [
            (new Option($languageSheet->getLabelValue(LanguageLabels::ACCOUNT_REGISTERED_USER_STATUS_DEFAULT)))->setValue("-")
        ];
        foreach (AccountRegisteredUserStatus::getValues() as $statusOption) {
            $statusOptionLabel = $languageSheet->getLabelValue($labels['ACCOUNT_REGISTERED_USER_STATUS' . '_' . $statusOption]);
            $statusOptions[] = (new Option($statusOptionLabel))->setValue($statusOption)->setData($statusOption)->setSelected(!isset($parameters['statusList']) || $parameters['statusList'] == "" ? false : ($statusOption == $parameters['statusList']));
        }
        $formItems[] = new FormItem(Parameters::STATUS_LIST, (new Select($statusOptions, null, LanguageLabels::ACCOUNT_REGISTERED_USER_STATUS_DEFAULT))->setLabelFor($languageSheet->getLabelValue(LanguageLabels::ACCOUNT_REGISTERED_USER_STATUS))->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD));

        if (
            in_array(Session::getInstance()?->getBasket()?->getAccount()?->getType(),  AccountType::getCompanyTypes()) &&
            LmsService::getAdvcaLicense() &&
            count($companyRoles) > 0
        ) {
            $roleOptions = [
                (new Option($languageSheet->getLabelValue(LanguageLabels::ACCOUNT_REGISTERED_USER_SEARCH_ROLE_DEFAULT)))->setValue("-")
            ];
            foreach ($companyRoles as $companyRole) {
                if ($companyRole?->getName()) {
                    $roleOptions[] = (new Option($companyRole->getName()))->setValue($companyRole->getId())->setSelected(!isset($parameters['roleId']) || $parameters['roleId'] == 0 ? false : ($companyRole->getId() == $parameters['roleId']));
                }
            }
            $formItems[] = new FormItem(Parameters::ROLE_ID, (new Select($roleOptions, null, LanguageLabels::ACCOUNT_REGISTERED_USER_SEARCH_ROLE_DEFAULT))->setLabelFor($languageSheet->getLabelValue(LanguageLabels::ACCOUNT_REGISTERED_USER_ROLE))->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD));
        }
        $formItems[] = new FormItem(Parameters::MASTER, (new Select($masterOptions, null, LanguageLabels::ACCOUNT_REGISTERED_USER_SEARCH_MASTER_DEFAULT))->setLabelFor($languageSheet->getLabelValue(LanguageLabels::ACCOUNT_REGISTERED_USER_MASTER))->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD));
        $formItems[] = new FormItem(Parameters::Q, ((new InputText($q))->setId('searchClient')->setRequired(false)->setLabelFor($languageSheet->getLabelValue($labels[$userKeyCriteria]))->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)));
        $formItems[] = new FormItem(Parameters::FIRST_NAME, ((new InputText(isset($parameters['fisrtName']) ? $parameters['fisrtName'] : ''))->setId('searchFisrtName')->setRequired(false)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::ACCOUNT_REGISTERED_USER_FIRST_NAME))->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)));
        $formItems[] = new FormItem(Parameters::LAST_NAME, ((new InputText(isset($parameters['lastName']) ? $parameters['lastName'] : ''))->setId('searchLastName')->setRequired(false)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::ACCOUNT_REGISTERED_USER_LAST_NAME))->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)));
        $formItems[] = new FormItem(Parameters::ADDED_FROM, ((new InputDate((isset($parameters['addedFrom']) && !is_null($parameters['addedFrom']) ? $parameters['addedFrom'] : '')))
            ->setId('addedFrom')->setRequired(false)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::ACCOUNT_REGISTERED_USER_SEARCH_ADDED_FROM))
            ->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)));
        $formItems[] = new FormItem(Parameters::ADDED_TO, ((new InputDate((isset($parameters['addedTo']) && !is_null($parameters['addedTo']) ? $parameters['addedTo'] : '')))
            ->setId('addedTo')->setRequired(false)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::ACCOUNT_REGISTERED_USER_SEARCH_ADDED_TO))
            ->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)));
        $formItems[] = new FormItem(Form::SUBMIT, (new ButtonSubmit())->setClass('searchRegisteredUser')->setId('searchRegisteredUserSubmit')->setContentText($languageSheet->getLabelValue(LanguageLabels::SEND)));
        $form = new Form($formHead, $formItems);
        return $form;
    }

    /**
     * This static method returns the 'company roles' Form.
     * @param CompanyRolesParametersGroup|null $companyRolesParametersGroup
     * @return Form
     */
    public static function getCompanyRolesFilters(CompanyRolesParametersGroup $companyRolesParametersGroup = null): Form {
        $parameters = [];
        if (!is_null($companyRolesParametersGroup)) {
            $parameters = $companyRolesParametersGroup->toArray();
        }

        $languageSheet = self::getLanguage();
        $formHead = (new FormHead(RoutePaths::getPath(RouteType::ACCOUNT_COMPANY_ROLES), 'companyRolesForm'))->setId('companyRolesForm')->setAutocomplete(Input::AUTOCOMPLETE_ON)->setMethod(FormHead::METHOD_POST)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);

        $formItems[] = new FormItem(
            Parameters::NAME,
            ((new InputText($parameters[Parameters::NAME] ?? ''))
                ->setId('roleName')
                ->setLabelFor($languageSheet->getLabelValue(LanguageLabels::ROLE))
                ->setClass(self::CLASS_WILDCARD)
                ->setAttributeWildcard(self::ATTRIBUTE_WILDCARD))
        );

        $targetOptions = [
            (new Option($languageSheet->getLabelValue(LanguageLabels::ACCOUNT_REGISTERED_USER_SEARCH_ROLE_DEFAULT)))
                ->setValue("-")
                ->setSelected(!isset($parameters[Parameters::TARGET]) || $parameters[Parameters::TARGET] == "" ? true : false),
            (new Option($languageSheet->getLabelValue(LanguageLabels::COMPANY_STRUCTURE_MASTER)))
                ->setValue(CustomCompanyRoleTarget::COMPANY_DIVISION_MASTER)
                ->setSelected(isset($parameters[Parameters::TARGET]) && $parameters[Parameters::TARGET] == CustomCompanyRoleTarget::COMPANY_DIVISION_MASTER ? true : false),
            (new Option($languageSheet->getLabelValue(LanguageLabels::COMPANY_STRUCTURE_NON_MASTER)))
                ->setValue(CustomCompanyRoleTarget::COMPANY_STRUCTURE_NON_MASTER)
                ->setSelected(isset($parameters[Parameters::TARGET]) && $parameters[Parameters::TARGET] == CustomCompanyRoleTarget::COMPANY_STRUCTURE_NON_MASTER ? true : false),
        ];

        $formItems[] = new FormItem(
            Parameters::TARGET,
            (new Select($targetOptions, null, LanguageLabels::ACCOUNT_REGISTERED_USER_SEARCH_ROLE_DEFAULT))
                ->setLabelFor($languageSheet->getLabelValue(LanguageLabels::ROLE_TARGET))
                ->setClass(self::CLASS_WILDCARD)
                ->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)
        );
        $formItems[] = new FormItem(Form::SUBMIT, (new ButtonSubmit())->setClass('searchRegisteredUser')->setId('searchRegisteredUserSubmit')->setContentText($languageSheet->getLabelValue(LanguageLabels::SEND)));
        $form = new Form($formHead, $formItems);
        return $form;
    }

    /**
     * This static method returns the 'company roles' Form.
     * @param BaseCompanyStructureTreeNode $node
     * @param string $accountId
     * @param int $level
     * @return array
     */
    private static function buildCompanyOptions(BaseCompanyStructureTreeNode $node, string $accountId, int $level = 0): array {
        $opts   = [];
        $indent = str_repeat("- ", $level);
        $label  = $indent . ($node->getName() ?: ('ID ' . $node->getId()));

        $opts[] = (new Option($label))
            ->setValue($node->getId())
            ->setSelected($node->getId() == $accountId);

        if ($node instanceof CompanyStructureTreeNode) {
            $subs = $node->getSubCompanyDivisions();
            if ($subs instanceof ElementCollection) {
                foreach ($subs->getItems() as $child) {
                    $opts = array_merge($opts, self::buildCompanyOptions($child, $accountId, $level + 1));
                }
            }
        }
        return $opts;
    }

    /**
     * This static method returns the registered user move Form.
     * @param array $companyStructures
     * 
     * @return Form
     */
    public static function getAccountRegisteredUserMove(string $accountId = AccountKey::USED, int $registeredUserId = 0, BaseCompanyStructureTreeNode $companyStructure = null): Form {
        $languageSheet = self::getLanguage();
        $idForm = 'registeredUserMoveForm';

        $formHead = (new FormHead(RoutePaths::getPath(InternalAccount::MOVE_ACCOUNT_REGISTERED_USER), $idForm))
            ->setId($idForm)
            ->setAutocomplete(Input::AUTOCOMPLETE_ON)
            ->setMethod(FormHead::METHOD_POST)
            ->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);

        $companyStructureOptions = $companyStructure
            ? self::buildCompanyOptions($companyStructure, $accountId, 0)
            : [];

        $formItems = [];
        $formItems[] = new FormItem(
            Parameters::ACCOUNT_ID,
            (new Select($companyStructureOptions, null))
                ->setLabelFor($languageSheet->getLabelValue(LanguageLabels::ACCOUNT_REGISTERED_USER_COMPANY_STRUCTURE))
                ->setClass(self::CLASS_WILDCARD)
                ->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)
        );
        $formItems[] = new FormItem(Parameters::ID, new InputHidden($accountId));
        $formItems[] = new FormItem(Parameters::REGISTERED_USER_ID, new InputHidden($registeredUserId));

        $formItems[] = new FormItem(
            Form::SUBMIT,
            (new ButtonSubmit())
                ->setClass(self::CLASS_WILDCARD)
                ->setId('registeredUserMoveSubmit')
                ->setDisabled(false)
                ->setContentText($languageSheet->getLabelValue(LanguageLabels::SAVE))
        );

        $form = new Form($formHead, $formItems);
        if (self::getConfiguration()->getForms()->getUseCaptcha()->getAccountRegisteredUserMove()) {
            $form->addCaptcha();
        }
        return $form;
    }
    /**
     * This static method returns the registered user update Form.
     * @param ?Master $registeredUser
     * 
     * @return Form
     */
    public static function getAccountRegisteredUserUpdate(?MasterVal $registeredUser = null, array $companyRoles = []): Form {
        $languageSheet = self::getLanguage();
        $idForm = 'registeredUserUpdateForm';
        if (is_null($registeredUser)) {
            $registeredUser = new MasterVal();
        }
        $formHead = (new FormHead(RoutePaths::getPath(InternalAccount::UPDATE_ACCOUNT_REGISTERED_USER), $idForm))->setId($idForm)->setAutocomplete(Input::AUTOCOMPLETE_ON)->setMethod(FormHead::METHOD_POST)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);

        $fields = self::getConfiguration()->getForms()->getAccount()->getAccountRegisteredUserFields()->getSortFilterArrayFormFields();
        foreach ($fields as $field => $formField) {
            if ($field == Parameters::ROLE_ID) {
                if ($registeredUser instanceof EmployeeVal && LmsService::getAdvcaLicense()) {
                    $roleOptions = [];
                    $select = false;
                    foreach ($companyRoles as $companyRole) {
                        $select = $registeredUser->getRole()?->getId() !== null && $registeredUser->getRole()?->getId() == $companyRole->getId() ? true : false;
                        $isMasterRole = (
                            $companyRole instanceof CustomCompanyRoleHeader &&
                            $companyRole->getTarget() === CustomCompanyRoleTarget::COMPANY_DIVISION_MASTER
                        ) ? 1 : 0;

                        $roleOptions[] = (new Option($companyRole->getName()))
                            ->setValue($companyRole->getId())
                            ->setSelected($select)
                            ->setAttributeWildcard('data-master="' . $isMasterRole . '"');
                    }
                    $defaultOptionLabel = LanguageLabels::ACCOUNT_REGISTERED_USER_ROLE_DEFAULT;
                    if ($registeredUser->isMaster() && $registeredUser->getAccount()->getType() == AccountType::COMPANY) {
                        $defaultOptionLabel = LanguageLabels::ACCOUNT_REGISTERED_USER_COMPANY_MASTER_ROLE_DEFAULT;
                    }

                    $labelBase = $languageSheet->getLabelValue(LanguageLabels::ACCOUNT_REGISTERED_USER_ROLE_DEFAULT);
                    $labelCompanyMaster = $languageSheet->getLabelValue(LanguageLabels::ACCOUNT_REGISTERED_USER_COMPANY_MASTER_ROLE_DEFAULT);

                    $defaultOption = (new Option($languageSheet->getLabelValue($defaultOptionLabel)))
                        ->setValue(0)
                        ->setSelected(!$select)
                        ->setAttributeWildcard('data-master="0" data-label-base="' . htmlspecialchars($labelBase, ENT_QUOTES) . '" data-label-company-master="' . htmlspecialchars($labelCompanyMaster, ENT_QUOTES) . '"');
                    array_unshift($roleOptions, $defaultOption);
                    $accountTypeStr = (string)$registeredUser->getAccount()->getType();
                    $formItems[] = new FormItem(
                        Parameters::ROLE_ID,
                        (new Select($roleOptions, null, LanguageLabels::ACCOUNT_REGISTERED_USER_SEARCH_ROLE_DEFAULT))
                            ->setRequired($formField->getRequired())
                            ->setDisabled($registeredUser->isMaster() and $registeredUser->getAccount()->getType() != AccountType::COMPANY_DIVISION)
                            ->setLabelFor($languageSheet->getLabelValue(LanguageLabels::ACCOUNT_REGISTERED_USER_ROLE))
                            ->setClass(self::CLASS_WILDCARD)
                            ->setAttributeWildcard('data-account-type="' . $accountTypeStr . '" ' . self::ATTRIBUTE_WILDCARD)
                    );
                }
            } else {
                $formItem = self::accountFields($field, $formField, null, null, '', null, $registeredUser, null, $companyRoles);
                if (!is_null($formItem)) {
                    $formItems[] = $formItem;
                }
            }
        }

        $formItems[] = new FormItem(Parameters::ACCOUNT_ID, (new InputHidden($registeredUser->getAccount()?->getId() == null ? "" : $registeredUser->getAccount()?->getId()))->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD));
        $formItems[] = new FormItem(Parameters::REGISTERED_USER_ID, (new InputHidden($registeredUser->getRegisteredUser()?->getId() == null ? "" : $registeredUser->getRegisteredUser()?->getId()))->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD));
        $formItems[] = new FormItem(Form::SUBMIT, (new ButtonSubmit())->setClass(self::CLASS_WILDCARD)->setId('registeredUserUpdateSubmit')->setDisabled(false)->setContentText($languageSheet->getLabelValue(LanguageLabels::SAVE)));
        $form = new Form($formHead, $formItems);
        if (self::getConfiguration()->getForms()->getUseCaptcha()->getAccountRegisteredUserUpdate()) {
            $form->addCaptcha();
        }
        return $form;
    }

    /**
     * This static method returns the registered user create Form.
     * Creates a form for creating new registered users with proper FormFactory pattern
     *
     * @return Form
     */
    public static function getAccountRegisteredUserCreate(string $accountId = AccountKey::USED, array $companyRoles = [], string $rolesFilter = CustomCompanyRoleTarget::COMPANY_STRUCTURE_NON_MASTER): Form {
        $lang = self::getLanguage();
        $labels = $lang->getLabels();
        $userKeyCriteria = Application::getInstance()->getEcommerceSettings()->getUserAccountsSettings()->getUserKeyCriteria();

        $formHead = (new FormHead(RoutePaths::getPath(InternalAccount::CREATE_ACCOUNT_REGISTERED_USER), 'registeredUserCreateForm'))->setId('registeredUserCreateForm')->setAutocomplete(Input::AUTOCOMPLETE_ON)->setMethod(FormHead::METHOD_POST)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);
        $formItems = [];

        $fields = self::getConfiguration()->getForms()->getAccount()->getMaster()->getRegisteredUser()->getFields()->getSortFilterArrayFormFields();

        foreach ($fields as $field => $formField) {
            $formItem = self::accountFields($field, $formField, null, null, '', null, null, null, $companyRoles, $rolesFilter, "", false, false, true);
            if (!is_null($formItem)) {
                $formItems[] = $formItem;
            }
        }
        $formItems[] = new FormItem(Parameters::ACCOUNT_ID, ((new InputHidden()))->setValue($accountId));
        $formItems[] = new FormItem(Parameters::Q, ((new InputText())->setRequired(true)->setId('searchClient')->setLabelFor($lang->getLabelValue($labels[$userKeyCriteria]))->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)));
        $formItems[] = new FormItem(Parameters::REGISTERED_USER_ID, ((new InputHidden())->setMaxlength(255)->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)));

        $formItems[] = new FormItem(Form::SUBMIT, (new ButtonSubmit())->setDisabled(false)->setClass('registeredUsersCreate')->setId('registeredUsersCreateSubmit')->setContentText($lang->getLabelValue(LanguageLabels::SAVE))->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD));
        $form = new Form($formHead, $formItems);
        if (self::getConfiguration()->getForms()->getUseCaptcha()->getAccountRegisteredUserCreate()) {
            $form->addCaptcha();
        }
        return $form;
    }

    /**
     * This static method returns the registered user approve Form.
     * Creates a form for creating new registered users with proper FormFactory pattern
     * 
     * @param MasterVal $registeredUser
     *            Registered user to approve
     * @param string $hash
     * 
     * @return Form
     */
    public static function getAccountRegisteredUsersApprove(?MasterVal $registeredUser = null, $hash = ""): Form {

        if ($registeredUser === null) {
            $registeredUser = new MasterVal();
        }
        $languageSheet = self::getLanguage();

        $formHead = (new FormHead(RoutePaths::getPath(InternalAccount::APPROVE_ACCOUNT_REGISTERED_USER), 'registeredUserApproveForm'))->setId('registeredUserApproveForm')->setAutocomplete(Input::AUTOCOMPLETE_ON)->setMethod(FormHead::METHOD_POST)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);
        $fields = self::getConfiguration()->getForms()->getAccount()->getMaster()->getRegisteredUser()->getApproveFields()->getSortFilterArrayFormFields();

        foreach ($fields as $field => $formField) {
            $formItem = self::accountFields($field, $formField, null, null, '', $registeredUser?->getRegisteredUser());
            if (!is_null($formItem)) {
                $formItems[] = $formItem;
            }
        }

        $formItems[] = new FormItem(Parameters::ACCOUNT_ID, ((new InputHidden($registeredUser?->getAccount()?->getId() ? $registeredUser?->getAccount()?->getId() : ""))->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)));

        $formItems[] = new FormItem(Parameters::REGISTERED_USER_ID, ((new InputHidden($registeredUser?->getRegisteredUser()?->getId() ? $registeredUser?->getRegisteredUser()?->getId() : ""))->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)));

        $formItems[] = new FormItem(Parameters::HASH, ((new InputHidden($hash))->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)));

        $formItems[] = new FormItem(Parameters::PASSWORD, (new InputPassword())->setRequired(true)->setMaxlength(50)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::ACCOUNT_REGISTERED_USER_PASSWORD))->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD));

        $formItems[] = new FormItem(Parameters::PASSWORD_RETYPE, (new InputPassword())->setRequired(true)->setMaxlength(50)->setLabelFor($languageSheet->getLabelValue(LanguageLabels::ACCOUNT_REGISTERED_USER_PASSWORD_RETYPE))->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD));

        $formItems[] = new FormItem(Form::SUBMIT, (new ButtonSubmit())->setDisabled(false)->setClass('registeredUsersApprove')->setId('registeredUsersCreateSubmit')->setContentText($languageSheet->getLabelValue(LanguageLabels::ACCOUNT_REGISTERED_USER_APPROVE_BUTTON)));
        $form = new Form($formHead, $formItems);
        if (self::getConfiguration()->getForms()->getUseCaptcha()->getAccountRegisteredUserApprove()) {
            $form->addCaptcha();
        }
        return $form;
    }

    /**
     * This static method returns the company division create Form.
     * Creates a form for creating new company divisions with proper FormFactory pattern
     *
     * @param CompanyDivisionsParametersGroup|null $companyDivisionsParametersGroup
     * @param array|null $countries
     * @return Form
     */
    public static function getAccountCompanyDivisionCreate(int $parentAccountId = 0, array $companyRoles = []): Form {

        $languageSheet = self::getLanguage();

        $formItems = self::getAccountRegisteredUserCreate(AccountKey::USED, $companyRoles, CustomCompanyRoleTarget::COMPANY_DIVISION_MASTER);

        $formItems->removeInputElement(Form::SUBMIT);
        $formItems = $formItems->getInputItems();

        $formHead = (new FormHead(
            RoutePaths::getPath(InternalAccount::SET_COMPANY_DIVISION),
            'saveCompanyDivisionForm'
        ))
            ->setId('saveCompanyDivisionForm')
            ->setAutocomplete(Input::AUTOCOMPLETE_ON)
            ->setMethod(FormHead::METHOD_POST)
            ->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);
        $formItems[] = new FormItem(Parameters::ID, ((new InputHidden($parentAccountId))->setMaxlength(255)->setClass(self::CLASS_WILDCARD)->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)));

        $requiredFields = [
            Parameters::LOCATION,
            Parameters::ADDRESS
        ];
        $invoicingFields = self::getConfiguration()->getForms()->getAccount()->getCompanyDivision()->getInvoicingFields()->getSortFilterArrayFormFields();
        $generalFields = self::getConfiguration()->getForms()->getAccount()->getCompanyDivision()->getGeneralFields()->getFormGeneralFields()->getSortFilterArrayFormFields();
        $fields = array_merge($invoicingFields, $generalFields);
        foreach ($fields as $field => $formField) {
            $formItem = self::accountFields($field, $formField, null, null, '', null, null, null, $companyRoles, CustomCompanyRoleTarget::COMPANY_DIVISION_MASTER, "", in_array($field, $requiredFields, true), false);
            if (!is_null($formItem)) {
                $formItems[] = $formItem;
            }
            if ($field === Parameters::LOCATION) {
                $formItems[] = self::accountFields(Parameters::COUNTRY, $formField, null, null, '', null, null, null, $companyRoles, CustomCompanyRoleTarget::COMPANY_DIVISION_MASTER, "", true, false);
            }
        }

        $formItems[] = new FormItem(
            Form::SUBMIT,
            (new ButtonSubmit())
                ->setDisabled(false)
                ->setClass('saveCompanyDivision')
                ->setId('saveCompanyDivisionSubmit')
                ->setContentText($languageSheet->getLabelValue(LanguageLabels::SAVE))
        );

        $form = new Form($formHead, $formItems);
        if (self::getConfiguration()->getForms()->getUseCaptcha()->getAccountCompanyDivisionCreate()) {
            $form->addCaptcha();
        }
        return $form;
    }

    /**
     * This static method returns the plugin connector type parameters group.
     * Creates a form for creating new company divisions with proper FormFactory pattern
     * 
     * @param string $connectorType
     * @return PluginConnectorTypeParametersGroup
     */
    private static function getPluginConnectorTypeParametersGroup(String $connectorType): ?PluginConnectorTypeParametersGroup {
        $params = new PluginConnectorTypeParametersGroup();
        $params->setType($connectorType);
        $params->setNavigationHash(Session::getInstance()->getNavigationHash());
        return $params;
    }

    /**
     * This static method returns the save company role form.
     * Creates a form for creating new company divisions with proper FormFactory pattern
     * 
     * @param CustomCompanyRole|null $customCompanyRole
     * @return Form
     */
    public static function getSaveCompanyRoleForm(CustomCompanyRole $customCompanyRole = null): Form {
        $isEdit = $customCompanyRole !== null;
        $parameters = $isEdit ? $customCompanyRole->toArray() : [];
        $perms = $parameters[Parameters::ROLE_PERMISSIONS] ?? [];
        $L = fn($k) => self::getLanguage()->getLabelValue($k);

        $formItems = [];

        $formHead = (new FormHead(
            RoutePaths::getPath(InternalAccount::SAVE_COMPANY_ROLE),
            'saveCompanyRoleForm'
        ))
            ->setId('saveCompanyRoleForm')
            ->setAutocomplete(Input::AUTOCOMPLETE_ON)
            ->setMethod(FormHead::METHOD_POST)
            ->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);

        if ($isEdit) {
            $formItems[] = new FormItem(
                Parameters::ID,
                (new InputHidden($customCompanyRole->getId()))
                    ->setClass(self::CLASS_WILDCARD)
                    ->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)
            );
        }

        $fields = self::getConfiguration()->getForms()->getAccount()->getCompanyRoles()->getFields()->getSortFilterArrayFormFields();
        foreach ($fields as $field => $formField) {
            if ($field === Parameters::NAME || $field === Parameters::TARGET) {
                $required = true;
            } else {
                $required = false;
            }

            $formItem = self::accountFields($field, $formField, null, null, '', null, null, $customCompanyRole, [], CustomCompanyRoleTarget::COMPANY_DIVISION_MASTER, Parameters::ROLE, $required);
            if (!is_null($formItem)) {
                $formItems[] = $formItem;
            }
        }

        $sections = [
            [LanguageLabels::COMPANY, 'data-id="company" data-level="0"', [
                ['permissionCompanyRead',   'companyRead',   'Read'],
                ['permissionCompanyUpdate', 'companyUpdate', 'Update'],
            ]],
            [LanguageLabels::EMPLOYEES, 'data-parent="company" data-id="company-employees" data-level="1"', [
                ['permissionCompanyEmployeesRead',   'companyEmployeesRead',   'Read'],
                ['permissionCompanyEmployeesUpdate', 'companyEmployeesUpdate', 'Update'],
                ['permissionCompanyEmployeesCreate', 'companyEmployeesCreate', 'Create'],
                ['permissionCompanyEmployeesDelete', 'companyEmployeesDelete', 'Delete'],
            ]],
            [LanguageLabels::ROLE, 'data-parent="company-employees" data-level="2"', [
                ['permissionCompanyEmployeesRoleUpdate', 'companyEmployeesRoleUpdate', 'Update'],
            ]],
            [LanguageLabels::THIS_ACCOUNT, 'data-id="this-account" data-level="0"', [
                ['permissionThisAccountUpdate', 'thisAccountUpdate', 'Update'],
                ['permissionThisAccountDelete', 'thisAccountDelete', 'Delete'],
            ]],
            [LanguageLabels::EMPLOYEES, 'data-parent="this-account" data-id="this-account-employees" data-level="1"', [
                ['permissionThisAccountEmployeesRead',   'thisAccountEmployeesRead',   'Read'],
                ['permissionThisAccountEmployeesUpdate', 'thisAccountEmployeesUpdate', 'Update'],
                ['permissionThisAccountEmployeesCreate', 'thisAccountEmployeesCreate', 'Create'],
                ['permissionThisAccountEmployeesDelete', 'thisAccountEmployeesDelete', 'Delete'],
            ]],
            [LanguageLabels::ROLE, 'data-parent="this-account-employees" data-level="2"', [
                ['permissionThisAccountEmployeesRoleUpdate', 'thisAccountEmployeesRoleUpdate', 'Update'],
            ]],
            [LanguageLabels::SUB_COMPANY_STRUCTURE, 'data-id="sub-company" data-level="0"', [
                ['permissionSubCompanyStructureRead',   'subCompanyStructureRead',   'Read'],
                ['permissionSubCompanyStructureUpdate', 'subCompanyStructureUpdate', 'Update'],
                ['permissionSubCompanyStructureCreate', 'subCompanyStructureCreate', 'Create'],
                ['permissionSubCompanyStructureDelete', 'subCompanyStructureDelete', 'Delete'],
            ]],
            [LanguageLabels::EMPLOYEES, 'data-parent="sub-company" data-id="sub-company-employees" data-level="1"', [
                ['permissionSubCompanyStructureEmployeesRead',   'subCompanyStructureEmployeesRead',   'Read'],
                ['permissionSubCompanyStructureEmployeesUpdate', 'subCompanyStructureEmployeesUpdate', 'Update'],
                ['permissionSubCompanyStructureEmployeesCreate', 'subCompanyStructureEmployeesCreate', 'Create'],
                ['permissionSubCompanyStructureEmployeesDelete', 'subCompanyStructureEmployeesDelete', 'Delete'],
            ]],
            [LanguageLabels::ROLE, 'data-parent="sub-company-employees" data-level="2"', [
                ['permissionSubCompanyStructureEmployeesRoleUpdate', 'subCompanyStructureEmployeesRoleUpdate', 'Update'],
            ]],
            [LanguageLabels::MASTER, 'data-parent="sub-company" data-level="1"', [
                ['permissionSubCompanyStructureMasterUpdate', 'subCompanyStructureMasterUpdate', 'Update'],
            ]],
            [LanguageLabels::ROLES, 'data-level="0"', [
                ['permissionRolesRead',   'rolesRead',   'Read'],
                ['permissionRolesUpdate', 'rolesUpdate', 'Update'],
                ['permissionRolesCreate', 'rolesCreate', 'Create'],
                ['permissionRolesDelete', 'rolesDelete', 'Delete'],
            ]],
            [LanguageLabels::ORDERS, 'data-id="orders" data-level="0"', []],
            [LanguageLabels::MY_ORDERS, 'data-parent="orders" data-level="1"', [
                ['permissionOrdersReadOwn', 'ordersReadOwn', 'Read'],
            ]],
            [LanguageLabels::OTHER_EMPLOYEES_ORDERS, 'data-parent="orders" data-level="1"', [
                ['permissionOrdersReadAllEmployees', 'ordersReadAllEmployees', 'Read'],
            ]],
            [LanguageLabels::THIS_ACCOUNT, 'data-parent="orders" data-level="1"', [
                ['permissionOrdersReadThisAccount', 'ordersReadThisAccount', 'Read'],
            ]],
            [LanguageLabels::SUB_ACCOUNTS, 'data-parent="orders" data-level="1"', [
                ['permissionOrdersReadSubAccounts', 'ordersReadSubAccounts', 'Read'],
            ]],
        ];

        $isDivisionMaster = ($parameters[Parameters::TARGET] ?? null) == CustomCompanyRoleTarget::COMPANY_DIVISION_MASTER;

        $options = [];
        foreach ($sections as $section) {
            [$labelKey, $attr, $items] = $section;

            $checkboxes = [];
            foreach ($items as $tuple) {
                [$id, $key, $value, $forceTrue] = array_pad($tuple, 4, false);

                $hasPerm = ($key !== null) && !empty($perms[$key]);
                $checked = $forceTrue ? true : ($isEdit ? $hasPerm : true);

                $checkbox = (new InputCheckbox('1'))
                    ->setId($id)
                    ->setValue($value)
                    ->setClass(self::CLASS_WILDCARD)
                    ->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);

                $forceDisabled = [
                    'permissionThisAccountUpdate',
                    'permissionThisAccountDelete',
                    'permissionThisAccountEmployeesRead',
                    'permissionThisAccountEmployeesUpdate',
                    'permissionThisAccountEmployeesCreate',
                    'permissionThisAccountEmployeesDelete',
                    'permissionThisAccountEmployeesRoleUpdate',
                    'permissionSubCompanyStructureRead',
                    'permissionSubCompanyStructureUpdate',
                    'permissionSubCompanyStructureCreate',
                    'permissionSubCompanyStructureDelete',
                    'permissionSubCompanyStructureEmployeesRead',
                    'permissionSubCompanyStructureEmployeesUpdate',
                    'permissionSubCompanyStructureEmployeesCreate',
                    'permissionSubCompanyStructureEmployeesDelete',
                    'permissionSubCompanyStructureEmployeesRoleUpdate',
                    'permissionSubCompanyStructureMasterUpdate',
                    'permissionOrdersReadOwn',
                    'permissionOrdersReadAllEmployees',
                    'permissionOrdersReadThisAccount',
                    'permissionOrdersReadSubAccounts',
                ];


                if ($isDivisionMaster && in_array($id, $forceDisabled, true)) {
                    $checkbox->setChecked(true)->setDisabled(true);
                } else {
                    $hasPerm = ($key !== null) && !empty($perms[$key]);
                    $checked = $forceTrue ? true : ($isEdit ? $hasPerm : true);
                    $checkbox->setChecked($checked);
                }

                $checkboxes[] = $checkbox;
            }

            $options[] = (new MultiSelect($checkboxes))
                ->setLabelFor($L($labelKey))
                ->setAttributeWildcard($attr);
        }

        $formItems[] = new FormItem(
            Parameters::ROLE_PERMISSIONS,
            (new TableMultiSelect($options, ['Read', 'Update', 'Create', 'Delete']))
                ->setId('rolePermissions')
                ->setClass(self::CLASS_WILDCARD)
                ->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)
        );

        foreach (
            [
                [Parameters::ALLOW_DIRECT_ORDER_CREATION,            'allowDirectOrderCreation',            LanguageLabels::ALLOW_DIRECT_ORDER_CREATION],
                [Parameters::ALLOW_DIRECT_ORDER_APPROVAL_THIS_ACCOUNT, 'allowDirectOrderApprovalThisAccount', LanguageLabels::ALLOW_DIRECT_ORDER_APPROVAL_THIS_ACCOUNT],
                [Parameters::ALLOW_DIRECT_ORDER_APPROVAL_SUB_ACCOUNTS, 'allowDirectOrderApprovalSubAccounts', LanguageLabels::ALLOW_DIRECT_ORDER_APPROVAL_SUB_ACCOUNTS],
            ] as [$pKey, $id, $label]
        ) {
            $checkbox = (new InputCheckbox('1'))
                ->setId($id)
                ->setLabelFor($L($label))
                ->setClass(self::CLASS_WILDCARD)
                ->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);

            if ($isDivisionMaster) {
                $checkbox->setChecked(true)->setDisabled(true);
            } else {
                $checkbox->setChecked($isEdit ? !empty($parameters[Parameters::ROLE_PERMISSIONS][$pKey]) : true);
            }

            $formItems[] = new FormItem($pKey, $checkbox);
        }

        $formItems[] = new FormItem(
            Form::SUBMIT,
            (new ButtonSubmit())
                ->setDisabled(false)
                ->setClass('saveCompanyRole')
                ->setId('saveCompanyRoleSubmit')
                ->setContentText($L(LanguageLabels::SAVE))
        );

        $form = new Form($formHead, $formItems);
        if (self::getConfiguration()->getForms()->getUseCaptcha()->getSaveCompanyRoleForm()) {
            $form->addCaptcha();
        }
        return $form;
    }

    /**
     * This method returns the account edit form.
     * 
     * @param Account|null $account
     * @param array $companyRoles
     * @param ElementCollection|null $accountCustomTags
     * @param string $rolesFilter
     * @return Form
     */
    public static function getAccountEditForm(Account $account = null, array $companyRoles = [], ?CompanyRolePermissionsValues $permissions = null, ?ElementCollection $accountCustomTags = null, string $rolesFilter = CustomCompanyRoleTarget::COMPANY_DIVISION_MASTER): Form {
        $formHead = (new FormHead(
            RoutePaths::getPath(InternalAccount::UPDATE_ACCOUNT),
            'editAccountForm'
        ))->setId('editAccountForm')
            ->setAutocomplete(Input::AUTOCOMPLETE_ON)
            ->setMethod(FormHead::METHOD_POST)
            ->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);

        $languageSheet = self::getLanguage();
        $formItems = [];

        $formItems[] = new FormItem(
            Parameters::ACCOUNT_ID,
            (new InputHidden($account?->getId() ?? ""))
                ->setClass(self::CLASS_WILDCARD)
                ->setAttributeWildcard(self::ATTRIBUTE_WILDCARD)
        );

        $paramDisabled = [
            Parameters::TYPE,
            Parameters::STATUS,
            Parameters::DATE_ADDED,
            Parameters::LAST_USED
        ];
        $hiddenNotCompany = [
            Parameters::EMAIL,
            Parameters::DESCRIPTION,
        ];

        $basket = Session::getInstance()->getBasket();

        if ($account != null) {
            $accountType = $account?->getType();
        } else {
            $accountType = $basket->getAccount()->getType();
        }

        $companyUpdatePermission = true;
        if ($basket->getAccount() instanceof CompanyDivision) {
            $isRoot = $basket->getAccount()->getCompany()->getId() == $account?->getId();
            if ($isRoot) {
                $companyUpdatePermission = $permissions->getCompanyUpdate();
            }
        }


        $allDisabledAccount = $basket->getMode()->getType() == SessionUsageModeType::SALES_AGENT_SIMULATION
            || !$companyUpdatePermission;
        $isCompany = $accountType && in_array($accountType, AccountType::getCompanyTypes());
        $fields = self::getConfiguration()->getForms()->getAccount()->getFields()->getSortFilterArrayFormFields();
        foreach ($fields as $field => $formField) {
            if (!$isCompany && in_array($field, $hiddenNotCompany)) {
                continue;
            }
            $formItem = self::accountFields($field, $formField, $account, null, '', null, null, null, $companyRoles, $rolesFilter, "", false, $allDisabledAccount || in_array($field, $paramDisabled));
            if (!is_null($formItem)) {
                $formItems[] = $formItem;
            }
        }

        $allDisabledRegisteredUser = $allDisabledAccount
            || !$basket->getAccountRegisteredUser()->IsMaster()
            || $basket->getAccountRegisteredUser()->getRegisteredUserId() != $account?->getMaster()?->getRegisteredUser()?->getId();

        $fields = self::getConfiguration()->getForms()->getAccount()->getMaster()->getRegisteredUser()->getFields()->getSortFilterArrayFormFields();
        foreach ($fields as $field => $formField) {
            $formItem = self::accountFields($field, $formField, $account, null, '', null, null, null, $companyRoles, $rolesFilter, "", false, $allDisabledRegisteredUser);

            if (!is_null($formItem)) {
                $formItems[] = $formItem;
            }
        }

        $showCustomTags = self::getConfiguration()->getForms()->getAccount()->getCustomTags()?->getIncluded() ?? false;
        if ($showCustomTags && !is_null($account) && !is_null($accountCustomTags)) {
            self::setAccountCustomTags($account, $accountCustomTags, $formItems, $allDisabledRegisteredUser);
        }

        $showAddressBook = self::getConfiguration()->getForms()->getAccount()->getAddressBook()?->getIncluded() ?? false;
        if ($showAddressBook and $account?->isCompany() ?? false) {
            $formItems[] = new FormItem(Parameters::ADDRESS_BOOK, new InputHidden(''));
        }

        if (!$allDisabledAccount) {
            $formItems[] = new FormItem(
                Form::SUBMIT,
                (new ButtonSubmit())
                    ->setDisabled(false)
                    ->setClass('saveAccount')
                    ->setId('saveAccountSubmit')
                    ->setContentText($languageSheet->getLabelValue(LanguageLabels::SAVE))
            );
        }
        $form = new Form($formHead, $formItems);
        if (self::getConfiguration()->getForms()->getUseCaptcha()->getAccountEditForm()) {
            $form->addCaptcha();
        }
        return $form;
    }

    /**
     * This method returns the account custom tag form items.
     * @param Account|null $account
     * @param ElementCollection $accountCustomTags
     * @param array &$formItems
     * @param bool $simulatedUser
     * @param bool $thisAccountUpdatePermissions
     */
    private static function setAccountCustomTags(?Account $account, ElementCollection $accountCustomTags, array &$formItems, bool $simulatedUser = false, bool $thisAccountUpdatePermissions = true): void {
        foreach ($accountCustomTags as $accountCustomTag) {
            $value = $accountCustomTag->getDefaultValue();

            foreach ($account?->getCustomTagValues() as $accountCustomTagValue) {
                if ($accountCustomTag->getId() === $accountCustomTagValue->getCustomTagId()) {
                    $value = $accountCustomTagValue->getValue();
                    break;
                }
            }

            $ctf = self::customTagFields($accountCustomTags, $accountCustomTag->getId(), $value, $simulatedUser, $thisAccountUpdatePermissions);

            if (!is_null($ctf)) {
                $formItems[] = $ctf;
            }
        }
    }

    /**
     * This method returns the registered user form.
     * 
     * @param RegisteredUser|null $user
     * @return Form
     */

    public static function setRegisteredUser(?RegisteredUser $user): Form {
        if (is_null($user)) {
            $user = new RegisteredUser();
        }
        $allDisabled = Session::getInstance()->getBasket()->getMode()->getType() == SessionUsageModeType::SALES_AGENT_SIMULATION;

        $formHead = (new FormHead(RoutePaths::getPath(InternalAccount::UPDATE_REGISTERED_USER), 'registeredUserUpdateForm'))
            ->setMethod(FormHead::METHOD_POST)
            ->setId('registeredUserUpdateForm')
            ->setAutocomplete(Input::AUTOCOMPLETE_OFF)
            ->setAttributeWildcard(self::ATTRIBUTE_WILDCARD);

        $formItems = [];
        $formItems[] = new FormItem(Parameters::REGISTERED_USER_ID, new InputHidden($user->getId()));
        $formItems[] = new FormItem(Parameters::ACCOUNT_ID, new InputHidden(Session::getInstance()->getBasket()->getAccount()->getId()));

        $fields = self::getConfiguration()->getForms()->getAccount()->getMaster()->getRegisteredUser()->getFields()->getSortFilterArrayFormFields();
        foreach ($fields as $field => $formField) {
            $formItem = self::accountFields($field, $formField, null, null, '', $user, null, null, [], CustomCompanyRoleTarget::COMPANY_STRUCTURE_NON_MASTER, "", false, $allDisabled);
            if (!is_null($formItem)) {
                $formItems[] = $formItem;
            }
        }

        $formItems[] = new FormItem(Form::SUBMIT, (new ButtonSubmit())->setClass(self::CLASS_WILDCARD)->setId('registeredUserUpdateSubmit')->setDisabled($allDisabled)->setContentText(self::getLanguage()->getLabelValue(LanguageLabels::SAVE)));
        $form = new Form($formHead, $formItems);
        if (self::getConfiguration()->getForms()->getUseCaptcha()->getSetRegisteredUser()) {
            $form->addCaptcha();
        }
        return $form;
    }
}
