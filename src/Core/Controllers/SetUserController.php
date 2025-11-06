<?php

namespace FWK\Core\Controllers;

use FWK\Core\Controllers\Traits\CheckCaptcha;
use SDK\Core\Dtos\Element;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Common\Route;
use SDK\Services\Parameters\Groups\User\Addresses\ShippingAddressParametersGroup;
use FWK\Enums\Services;
use FWK\Core\Resources\Loader;
use SDK\Services\Parameters\Groups\User\Addresses\BillingAddressParametersGroup;
use SDK\Services\Parameters\Groups\User\CreateUserParametersGroup;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Enums\LanguageLabels;
use SDK\Services\Parameters\Groups\User\UpdateUserParametersGroup;
use FWK\Enums\Parameters;
use FWK\Core\Form\FormFactory;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\Resources\Session;
use FWK\Core\Resources\Utils;
use FWK\Enums\SetUserTypeForms;
use FWK\Services\AccountService;
use SDK\Services\Parameters\Groups\User\UserCustomTagParametersGroup;
use SDK\Services\Parameters\Groups\User\UserParametersGroup;
use SDK\Core\Dtos\Error;
use SDK\Core\Services\Parameters\Groups\NewsletterSubscriptionParametersGroup;
use SDK\Dtos\User\User;
use SDK\Services\Parameters\Groups\LocationParametersGroup;
use FWK\Services\UserService;
use SDK\Application;
use SDK\Core\Services\Parameters\Factories\UserToAccountFactory;
use SDK\Core\Services\Parameters\Groups\CustomTagDataParametersGroup;
use SDK\Enums\AccountKey;
use SDK\Enums\MasterType;
use SDK\Services\Parameters\Groups\Account\AccountParametersGroup;
use SDK\Services\Parameters\Groups\Account\UpdateAccountRegisteredUsersParametersGroup;
use SDK\Services\Parameters\Groups\User\Addresses\AddressValidateParametersGroup;

/**
 * This is the base controller for the set user controllers.
 *
 * This class extends BaseJsonController, see this class.
 *
 * @abstract
 *
 * @see SetUserController::parseData()
 * @see SetUserController::fillParametersGroup()
 * @see SetUserController::getInputFilterParameters()
 *
 * @see BaseJsonController
 * 
 * @uses CheckCaptcha
 *
 * @package FWK\Core\Controllers
 */
abstract class SetUserController extends BaseJsonController {
    use CheckCaptcha;

    /**
     * This method returns the type of the form.
     *
     * @abstract
     *
     * @return string Indicates the type of the form. See FormFactory constants for the types.
     *        
     * @see FormFactory
     */
    abstract protected function getTypeForm(): string;

    /**
     * This method returns the url to redirect.
     *
     * @abstract
     *
     * @return string
     */
    abstract protected function getUrlRedirect(): string;

    protected ?UserService $userService = null;

    private array $data = [];

    private ?Error $responseError = null;

    protected ?AddressValidateParametersGroup $billingAddressValidateParametersGroup = null;

    protected ?AddressValidateParametersGroup $shippingAddressValidateParametersGroup = null;

    protected ?BillingAddressParametersGroup $billingAddressParametersGroup = null;

    protected ?ShippingAddressParametersGroup $shippingAddressParametersGroup = null;

    protected ?UserParametersGroup $userParametersGroup = null;

    protected ?CreateUserParametersGroup $createUserParametersGroup = null;

    protected ?UpdateUserParametersGroup $updateUserParametersGroup = null;

    protected ?AccountService $accountService = null;

    protected ?AccountParametersGroup $accountParametersGroup = null;


    public const USER = 'user';

    public const BILLING = 'billing';

    public const BILLING_VALIDATE = 'billingValidate';

    public const SHIPPING = 'shipping';

    public const SHIPPING_VALIDATE = 'shippingValidate';

    public const SUBSCRIBED = 'subscribed';

    public const CUSTOM_TAGS = 'customTags';

    /**
     * This constant is an array that defines the fields to consider when creating a user.
     */
    public const CREATE_ACCOUNT_FIELDS = [
        Parameters::EMAIL,
        Parameters::PASSWORD,
        Parameters::GODFATHER_CODE,
        Parameters::CREATE_ACCOUNT,
        Parameters::CUSTOM_TAGS,
        Parameters::P_ID,
        Parameters::NICK,
        Parameters::GENDER,
        Parameters::BIRTHDAY,
        Parameters::USE_SHIPPING_ADDRESS,
        Parameters::SUBSCRIBED,
        Parameters::IMAGE
    ];

    /**
     * This constant is an array that defines the fields to consider when updating a user.
     */
    public const UPDATE_ACCOUNT_FIELDS = [
        Parameters::P_ID,
        Parameters::NICK,
        Parameters::GENDER,
        Parameters::BIRTHDAY,
        Parameters::USE_SHIPPING_ADDRESS,
        Parameters::SUBSCRIBED,
        Parameters::IMAGE
    ];

    private const ADDRESS_FIELDS = [
        Parameters::ALIAS,
        Parameters::ADDRESS_ID,
        Parameters::USER_TYPE,
        Parameters::DEFAULT_ADDRESS,
        Parameters::FIRST_NAME,
        Parameters::LAST_NAME,
        Parameters::COMPANY,
        Parameters::ADDRESS,
        Parameters::ADDRESS_ADDITIONAL_INFORMATION,
        Parameters::NUMBER,
        Parameters::CITY,
        Parameters::STATE,
        Parameters::POSTAL_CODE,
        Parameters::VAT,
        Parameters::NIF,
        Parameters::PHONE,
        Parameters::MOBILE,
        Parameters::FAX,
        Parameters::LOCATION_LIST,
        Parameters::COUNTRY
    ];

    /**
     * This constant is an array that defines the fields to consider for a billing address.
     */
    public const BILLING_FIELDS = [...self::ADDRESS_FIELDS, Parameters::RE];

    /**
     * This constant is an array that defines the fields to consider for a shipping address.
     */
    public const SHIPPING_FIELDS = self::ADDRESS_FIELDS;

    /**
     * This constant is an array that defines the fields of type BOOL.
     */
    public const BOOL_FIELDS = [
        Parameters::USE_SHIPPING_ADDRESS,
        Parameters::SUBSCRIBED,
        Parameters::CREATE_ACCOUNT
    ];

    /**
     * This constant is an array that defines the fields of type DATE.
     */
    public const DATE_FIELDS = [
        Parameters::BIRTHDAY
    ];

    /**
     * This constant is an array that defines the fields of type ARRAY.
     */
    public const ARRAY_FIELDS = [
        Parameters::CUSTOM_TAGS
    ];

    /**
     * This method returns an array of the input params indicating in each node the param name, and the filter to apply.
     *
     * @param ?string $typeForm
     * @param bool $isAddress
     *
     * @return array
     */
    public static function getInputFilterParameters(string $typeForm, bool $isUserAddress = false, ?User $user = null) {
        $uct = Loader::service(Services::USER)->getCustomTags(self::getTheme()->getConfiguration()->getUser()->getUserCustomTagsParametersGroup());
        $filterParameters = FormFactory::setUser($typeForm, $user, $uct)->getInputFilterParametersInOneLevel();

        foreach ($filterParameters as $key => $value) {
            $newKey = preg_replace('/(' . SetUserTypeForms::BILLING . '_|' . SetUserTypeForms::SHIPPING . '_|' . SetUserTypeForms::USER . '_)/', '', $key, 1);
            if ($isUserAddress) {
                $newKey = preg_replace('/(_' . SetUserTypeForms::USER . '_)/', '_' . SetUserTypeForms::BILLING . '_', $newKey);
            }
            unset($filterParameters[$key]);
            $filterParameters[$newKey] = $value;
        }
        return $filterParameters + FilterInputFactory::getLocationListParameters() + FilterInputFactory::getStateCityPostalParameters();
    }

    /**
     * This method parses the request params
     *
     * @return array
     */
    public static function parseData($requestParams, bool $simulaterUser = false): array {
        $response = [
            self::USER => [],
            self::BILLING => [],
            self::SHIPPING => [],
            self::CUSTOM_TAGS => []
        ];

        if (!$simulaterUser) {
            foreach ($requestParams as $fieldName => $fieldValue) {
                $fieldNameData = [];
                // New way
                // This regex split by "_" but first match cant have "_"
                // Examples: ['PARTICULAR', 'user', 'firstName'] or ['customTags', 'PUBLIC_ID_VALUE', '50'] or ['customTags', '', '50']
                $isMultipleName = preg_match('/^((?:(?!_).)+)_(.*)_(.+)$/', $fieldName, $matches);
                if ($isMultipleName === 1) {
                    $matches = array_slice($matches, 1);
                    foreach ($matches as $match) {
                        $fieldNameData[] = $match;
                    }
                }
                // Field names simples:
                // Examples: ['firstName'] or ['addressId']
                else {
                    $fieldNameData = [$fieldName];
                }
                // Old way
                // $fieldNameData = explode('_', $fieldName);

                $isShipping = false;
                if (count($fieldNameData) === 3) {
                    if ($fieldNameData[0] === Parameters::CUSTOM_TAGS) {
                        $response[self::CUSTOM_TAGS][$fieldNameData[2]] = $fieldValue;
                        continue;
                    } else {
                        $fieldName = $fieldNameData[2];
                        $isShipping = $fieldNameData[1] === self::SHIPPING ? true : false;
                    }
                }
                if (in_array($fieldName, self::CREATE_ACCOUNT_FIELDS, true)) {
                    if (in_array($fieldName, self::DATE_FIELDS, true)) {
                        if (strlen(trim($fieldValue))) {
                            $response[self::USER][$fieldName] = new \DateTime($fieldValue);
                        }
                    } else {
                        $response[self::USER][$fieldName] = $fieldValue;
                    }
                } else if (in_array($fieldName, self::BILLING_FIELDS, true) && $isShipping === false && count($fieldNameData) > 0) {
                    if (!isset($response[self::BILLING][Parameters::USER_TYPE])) {
                        $response[self::BILLING][Parameters::USER_TYPE] = $fieldNameData[0];
                    }
                    $response[self::BILLING][$fieldName] = $fieldValue;
                    if ($fieldName === Parameters::COUNTRY || $fieldName === Parameters::LOCATION_LIST) {
                        if (!isset($response[self::BILLING]['location'])) {
                            $response[self::BILLING]['location'] = new LocationParametersGroup();
                            $response[self::BILLING]['locationAppliedParameters'] = [];
                        }
                        $response[self::BILLING] = self::buildLocationParametersGroup($fieldName, $fieldValue, $response[self::BILLING]);
                    }
                } else if (in_array($fieldName, self::SHIPPING_FIELDS, true) && $isShipping === true && count($fieldNameData) > 0) {
                    if (!isset($response[self::SHIPPING][Parameters::USER_TYPE])) {
                        $response[self::SHIPPING][Parameters::USER_TYPE] = $fieldNameData[0];
                    }
                    $response[self::SHIPPING][$fieldName] = $fieldValue;
                    if ($fieldName === Parameters::COUNTRY || $fieldName === Parameters::LOCATION_LIST) {
                        if (!isset($response[self::SHIPPING]['location'])) {
                            $response[self::SHIPPING]['location'] = new LocationParametersGroup();
                            $response[self::SHIPPING]['locationAppliedParameters'] = [];
                        }
                        $response[self::SHIPPING] = self::buildLocationParametersGroup($fieldName, $fieldValue, $response[self::SHIPPING]);
                    }
                }
            }
        }
        return $response;
    }

    private static function buildLocationParametersGroup(string $fieldName, string $fieldValue, array &$locationParam): array {
        if ($fieldName === Parameters::COUNTRY && strlen($fieldValue)) {
            $locationParam['locationAppliedParameters'][Parameters::COUNTRY_CODE] = $fieldValue;
            $locationParam['location']->setCountryCode($fieldValue);
        } elseif ($fieldName === Parameters::LOCATION_LIST && strlen($fieldValue)) {
            $list = explode(",", $fieldValue);
            if (!empty($list)) {
                $locationId = $list[count($list) - 1];
                $locationParam['locationAppliedParameters'][Parameters::LOCATION_ID] = $locationId;
                $locationParam['location']->setLocationId($locationId);
            }
        }
        return $locationParam;
    }

    /**
     * Constructor method.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->userService = Loader::service(Services::USER);
        $this->createUserParametersGroup = new CreateUserParametersGroup();
        $this->billingAddressValidateParametersGroup = new AddressValidateParametersGroup();
        $this->shippingAddressValidateParametersGroup = new AddressValidateParametersGroup();
        $this->billingAddressParametersGroup = new BillingAddressParametersGroup();
        $this->shippingAddressParametersGroup = new ShippingAddressParametersGroup();
        $this->updateUserParametersGroup = new UpdateUserParametersGroup();
        $this->responseMessage = $this->language->getLabelValue(LanguageLabels::SAVED, $this->responseMessage);
        $this->accountService = Loader::service(Services::ACCOUNT);
    }

    /**
     * This method initialize applied parameters, runs previously to run preSendControllerBaseBatchData
     *
     */
    protected function initializeAppliedParameters(): void {
        if (!Utils::isSimulatedUser($this->getSession())) {
            if (Utils::isSessionLoggedIn($this->getSession())) {
                $this->fillParametersGroup(false);
            } else {
                $this->fillParametersGroup(true);
            }
        }
    }

    /**
     * This method calculates the UserGroupId to apply to the user
     *
     * @param bool $newUser
     * 
     * @return string
     */
    public function getUserGroupPId(bool $newUser): string {
        return '';
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return self::getInputFilterParameters($this->getTypeForm(), false, $this->getSession()->getUser());
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
        return FilterInputHandler::PARAMS_FROM_POST_DATA_OBJECT;
    }

    protected function resetAddressParametersGroup(BillingAddressParametersGroup $addressParametersGroup): void {
        $addressParametersGroup->setAlias('');
        $addressParametersGroup->setFirstName('');
        $addressParametersGroup->setLastName('');
        $addressParametersGroup->setCompany('');
        $addressParametersGroup->setAddress('');
        $addressParametersGroup->setAddressAdditionalInformation('');
        $addressParametersGroup->setNumber('');
        $addressParametersGroup->setCity('');
        $addressParametersGroup->setState('');
        $addressParametersGroup->setPostalCode('');
        $addressParametersGroup->setVat('');
        $addressParametersGroup->setNif('');
        $addressParametersGroup->setPhone('');
        $addressParametersGroup->setMobile('');
        $addressParametersGroup->setFax('');
    }

    protected function fillParametersGroup(bool $newUser): void {
        $this->data = $this->getParsedParamsData();
        if (!empty($this->data)) {
            $this->userService->generateParametersGroupFromArray($this->billingAddressValidateParametersGroup, $this->data[self::BILLING]);
            $this->appliedParameters[self::BILLING] = $this->data[self::BILLING];
            if ($newUser) {
                $this->billingAddressParametersGroup = new BillingAddressParametersGroup();
                $sessionUserType = Session::getInstance()->getUser()?->getDefaultBillingAddress()?->getUserType();
                if (!empty($sessionUserType) && $this->data[self::BILLING][Parameters::USER_TYPE] != Session::getInstance()->getUser()?->getDefaultBillingAddress()?->getUserType()) {
                    $this->resetAddressParametersGroup($this->billingAddressParametersGroup);
                }
                $this->userService->generateParametersGroupFromArray($this->billingAddressParametersGroup, $this->data[self::BILLING]);
                $this->shippingAddressParametersGroup = new ShippingAddressParametersGroup();
                $this->userService->generateParametersGroupFromArray($this->shippingAddressParametersGroup, $this->data[self::SHIPPING]);
                $this->userService->generateParametersGroupFromArray($this->shippingAddressValidateParametersGroup, $this->data[self::SHIPPING]);
                $this->appliedParameters[self::SHIPPING] = $this->data[self::SHIPPING];
                $this->userParametersGroup = new CreateUserParametersGroup();
                $this->appliedParameters[self::USER] = $this->userService->generateParametersGroupFromArray($this->userParametersGroup, $this->data[self::USER]);
                $this->appliedParameters[self::BILLING]['defaultAddress'] = true;
                $this->billingAddressParametersGroup->setDefaultAddress(true);
                if (!empty($this->appliedParameters[self::BILLING])) {
                    $this->userParametersGroup->setBillingAddress($this->billingAddressParametersGroup);
                }
                if (!empty($this->appliedParameters[self::SHIPPING])) {
                    $this->userParametersGroup->setShippingAddress($this->shippingAddressParametersGroup);
                }
            } else {
                $this->billingAddressParametersGroup = new BillingAddressParametersGroup();
                $this->userService->generateParametersGroupFromArray($this->billingAddressParametersGroup, $this->data[self::BILLING]);
                $this->setValidateAddressData($this->billingAddressValidateParametersGroup);
                $this->userParametersGroup = new UpdateUserParametersGroup();
                $this->appliedParameters[self::USER] = $this->userService->generateParametersGroupFromArray($this->userParametersGroup, $this->data[self::USER]);
            }
            $customTags = [];
            $this->appliedParameters[self::CUSTOM_TAGS] = [];
            foreach ($this->data[self::CUSTOM_TAGS] as $id => $value) {
                if (!is_null($value)) {
                    $customTag = new UserCustomTagParametersGroup();
                    $customTag->setCustomTagId($id);
                    $customTagData = new CustomTagDataParametersGroup();
                    $objValue = json_decode($value);
                    if (is_object($objValue) && property_exists($objValue, 'extension') && property_exists($objValue, 'fileName') && property_exists($objValue, 'value')) {
                        $customTagData->setExtension($objValue->extension);
                        $customTagData->setFileName($objValue->fileName);
                        $customTagData->setValue($objValue->value);
                        $customTag->setData($customTagData);
                    } else {
                        $customTagData->setValue($value);
                        $customTag->setData($customTagData);
                    }
                    $this->appliedParameters[self::CUSTOM_TAGS][] = [
                        'customTagId' => $id,
                        'data' => $customTagData->toArray()
                    ];
                    $customTags[] = $customTag;
                }
            }
            $this->userParametersGroup->setCustomTags($customTags);
            $userGroupPId = $this->getUserGroupPId($newUser);
            if (strlen($userGroupPId)) {
                $this->userParametersGroup->setGroupPId($userGroupPId);
                $this->appliedParameters[Parameters::GROUP_PID] = $userGroupPId;
            }
        }
    }

    protected function setValidateAddressData(?AddressValidateParametersGroup $parametersGroup): void {
        $addressId = $this->data[self::BILLING][Parameters::ADDRESS_ID] ?? 0;
        if ($addressId > 0) {
            $addressData = $this->userService->getAddress($addressId);
            $parametersGroup->setFirstName($addressData->getFirstName());
            $parametersGroup->setLastName($addressData->getLastName());
            $parametersGroup->setCompany($addressData->getCompany());
            $parametersGroup->setAddress($addressData->getAddress());
            $parametersGroup->setAddressAdditionalInformation($addressData->getAddressAdditionalInformation());
            $parametersGroup->setNumber($addressData->getNumber());
            $parametersGroup->setCity($addressData->getCity());
            $parametersGroup->setState($addressData->getState());
            $parametersGroup->setPostalCode($addressData->getPostalCode());
            $parametersGroup->setVat($addressData->getVat());
            $parametersGroup->setNif($addressData->getNif());
            $parametersGroup->setPhone($addressData->getPhone());
            $parametersGroup->setMobile($addressData->getMobile());
            $parametersGroup->setFax($addressData->getFax());

            $location = new LocationParametersGroup();
            $locationData = $addressData->getLocation();
            if (!is_null($locationData)) {
                $geographicalZone = $locationData->getGeographicalZone();
                if (!is_null($geographicalZone)) {
                    $location->setLocationId($geographicalZone->getLocationId());
                    $location->setCountryCode($geographicalZone->getCountryCode());
                }
            }
            $parametersGroup->setLocation($location);
        } else {
            $this->userService->generateParametersGroupFromArray($parametersGroup, $this->data[self::BILLING]);
        }
    }

    protected function getParsedParamsData(): array {
        return self::parseData($this->getRequestParams(), Utils::isSimulatedUser($this->getSession()));
    }

    protected function validateUserAddress() {
        $this->data = $this->getParsedParamsData();
        $this->userService->generateParametersGroupFromArray($this->billingAddressValidateParametersGroup, $this->data[self::BILLING]);
        $this->appliedParameters[self::BILLING] = $this->data[self::BILLING];
        $this->setValidateAddressData($this->billingAddressValidateParametersGroup);
        $addressIsValid = true;
        $messages = [];
        if (
            !empty($this->appliedParameters[self::BILLING])
            && key_exists(Parameters::ADDRESS, $this->appliedParameters[self::BILLING])
            && !empty($this->appliedParameters[self::BILLING][Parameters::ADDRESS])
        ) {
            $response[self::BILLING_VALIDATE] = $this->userService->addressValidate($this->billingAddressValidateParametersGroup);
            if (!$response[self::BILLING_VALIDATE]->getValid()) {
                $addressIsValid = false;
                $messages = $response[self::BILLING_VALIDATE]->getMessages();
            }
        }
        if (
            !empty($this->appliedParameters[self::SHIPPING])
            && key_exists(Parameters::ADDRESS, $this->appliedParameters[self::SHIPPING])
            && !empty($this->appliedParameters[self::SHIPPING][Parameters::ADDRESS])
        ) {
            $response[self::SHIPPING_VALIDATE] = $this->userService->addressValidate($this->shippingAddressValidateParametersGroup);
            if (!$response[self::SHIPPING_VALIDATE]->getValid()) {
                $addressIsValid = false;
                $messages = array_merge($messages, $response[self::SHIPPING_VALIDATE]->getMessages());
            }
        }
        return new class($addressIsValid, $messages) extends Element {

            public bool $addressIsValid = true;

            public ?array $messages = null;

            public function __construct(bool $addressIsValid, ?array $messages) {
                $this->addressIsValid = $addressIsValid;
                $this->messages = $messages;
            }

            public function getMessages(): ?array {
                return $this->messages;
            }

            public function isValid(): bool {
                return $this->addressIsValid;
            }

            public function jsonSerialize(): mixed {
                return $this->error;
            }
        };
    }

    /**
     * This method launches the adequate actions against the SDK (through the FWK services) and returns the response data. 
     *
     * @return Element
     */
    protected function getResponseData(): ?Element {
        $this->checkCaptcha();
        $response = [];
        $response[self::USER] = null;
        $response[self::BILLING] = null;
        $response[self::BILLING_VALIDATE] = null;
        $response[self::SHIPPING] = null;
        $response[self::SHIPPING_VALIDATE] = null;
        $response[self::SUBSCRIBED] = null;
        if (!Utils::isSimulatedUser($this->getSession())) {
            $addressIsValid = true;
            if (
                !empty($this->appliedParameters[self::BILLING])
                && key_exists(Parameters::ADDRESS, $this->appliedParameters[self::BILLING])
                && !empty($this->appliedParameters[self::BILLING][Parameters::ADDRESS])
            ) {
                $response[self::BILLING_VALIDATE] = $this->userService->addressValidate($this->billingAddressValidateParametersGroup);
                if (!$response[self::BILLING_VALIDATE]->getValid()) {
                    $addressIsValid = false;
                }
            }
            if (
                !empty($this->appliedParameters[self::SHIPPING])
                && key_exists(Parameters::ADDRESS, $this->appliedParameters[self::SHIPPING])
                && !empty($this->appliedParameters[self::SHIPPING][Parameters::ADDRESS])
            ) {
                $response[self::SHIPPING_VALIDATE] = $this->userService->addressValidate($this->shippingAddressValidateParametersGroup);
                if (!$response[self::SHIPPING_VALIDATE]->getValid()) {
                    $addressIsValid = false;
                }
            }
            if ($addressIsValid) {
                $themeConfiguration = self::getTheme()->getConfiguration();
                $type = $this->getRequestParam(Parameters::USER_TYPE, false, '');
                if (strlen($type) === 0) {
                    $type = $this->data[self::BILLING][Parameters::USER_TYPE] ?? '';
                }
                $type = is_null($type) ? '' : ucwords(strtolower($type));
                if (Utils::isSessionLoggedIn($this->getSession())) {
                    $dataValidator = 'getUpdateUser' . $type;
                    $dataValidator = $themeConfiguration->getDataValidators()->$dataValidator();
                    $isAccountUpdateBlocked = false;
                    if (!Application::getInstance()->getEcommerceSettings()->getAccountRegisteredUsersSettings()->getCardinalityPlus()) {
                        $responseUpdateUser = $this->userService->updateUser($this->userParametersGroup, $dataValidator);
                    } else {
                        $thisAccountUpdatePermissions = true;
                        if ($this->getSession()?->getBasket()?->getAccountRegisteredUser()?->getType() === MasterType::EMPLOYEE) {
                            $roleId = $this->getSession()?->getBasket()?->getAccountRegisteredUser()?->getRole()?->getId() ?? 0;
                            if ($roleId !== 0) {
                                $companyRole = $this->accountService->getCompanyRole($roleId);
                                $thisAccountUpdatePermissions = $companyRole?->getPermissions()?->getThisAccountUpdate() ?? true;
                            }
                            $isAccountUpdateBlocked = Utils::isAccountUpdateBlocked($thisAccountUpdatePermissions);
                        }

                        if (!$isAccountUpdateBlocked) {
                            $this->accountParametersGroup = UserToAccountFactory::mapUpdateUserToUpdateAccount($this->userParametersGroup, Session::getInstance()?->getBasket()?->getAccountRegisteredUser()?->isMaster() ?? true, $isAccountUpdateBlocked);
                            $responseUpdateUser = $this->accountService->updateUsedAccount(AccountKey::USED, $this->accountParametersGroup, $dataValidator);
                        }
                        $updateAccountRegisteredUsersParametersGroup = new UpdateAccountRegisteredUsersParametersGroup();

                        if (!$this->getSession()?->getBasket()?->getAccountRegisteredUser()?->isMaster()) {
                            $updateAccountRegisteredUsersParametersGroup->setUseShippingAddress($this->data[self::USER][Parameters::USE_SHIPPING_ADDRESS] ?? false);
                            $responseUpdateAccount = $this->accountService->updateAccountRegisteredUser(AccountKey::USED, $this->getSession()->getBasket()->getAccountRegisteredUser()->getRegisteredUserId(), $updateAccountRegisteredUsersParametersGroup, $dataValidator);
                            $this->responseMessageError = Utils::getErrorLabelValue($responseUpdateAccount);
                            $this->responseError = $responseUpdateAccount->getError();
                        }
                    }
                    if (!$isAccountUpdateBlocked) {
                        $this->responseMessageError = Utils::getErrorLabelValue($responseUpdateUser);
                        $this->responseError = $responseUpdateUser->getError();
                        if (
                            is_null($this->responseError) &&
                            count($this->appliedParameters[self::BILLING]) &&
                            isset($this->data[self::BILLING][Parameters::ADDRESS_ID])
                        ) {
                            $response[self::USER] = new User($responseUpdateUser->getBasketUser()->toArray());
                            $this->appliedParameters[self::BILLING][Parameters::ADDRESS_ID] = $this->data[self::BILLING][Parameters::ADDRESS_ID];
                            $dataValidator = 'getUpdateUserBillingAddress' . $type;
                            $dataValidator = $themeConfiguration->getDataValidators()->$dataValidator();
                            $data = UserToAccountFactory::mapBillingAddressFromAccountInvoicingAddressCompatible($this->billingAddressParametersGroup);
                            $response[self::BILLING] = $this->accountService->updateAccountsInvoicingAddresses($this->appliedParameters[self::BILLING][Parameters::ADDRESS_ID], $data, $dataValidator);

                            $this->responseMessageError = Utils::getErrorLabelValue($response[self::BILLING]);
                            $this->responseError = $response[self::BILLING]->getError();
                        }
                    }
                } else {
                    $dataValidator = 'getNewUser' . ($this->getTypeForm() === FormFactory::SET_USER_TYPE_ADD_USER_FAST_REGISTER ? 'FastRegister' : '') . $type;
                    $dataValidator = $themeConfiguration->getDataValidators()->$dataValidator();
                    if (!Application::getInstance()->getEcommerceSettings()->getAccountRegisteredUsersSettings()->getCardinalityPlus()) {
                        $responseUser = $this->userService->createUser($this->userParametersGroup, $dataValidator);
                    } else {
                        if (isset($this->data[self::USER]['createAccount']) && $this->data[self::USER]['createAccount'] == true) {
                            $this->accountParametersGroup = UserToAccountFactory::mapCreateUserToCreateAccount($this->userParametersGroup, self::getTheme()?->getConfiguration()?->getAccount()?->getAccountType());
                            $responseUser = $this->accountService->createAccount($this->accountParametersGroup, $dataValidator);
                        } else {
                            $this->accountParametersGroup = UserToAccountFactory::mapCreateUserToUpdateOmsBasketCustomer($this->userParametersGroup, self::getTheme()?->getConfiguration()?->getAccount()?->getAccountType());
                            $responseUser = Loader::service(Services::BASKET)->updateOmsBasketCustomer($this->accountParametersGroup, $dataValidator);
                        }
                    }
                    if (is_null($responseUser->getError())) {
                        $response[self::USER] = new User($responseUser->getBasketUser()->toArray());
                        if (isset($this->data[self::USER][Parameters::SUBSCRIBED]) && isset($this->data[self::USER][Parameters::EMAIL])) {
                            $newsletterSubscriptionParametersGroup = new NewsletterSubscriptionParametersGroup();
                            $newsletterSubscriptionParametersGroup->setEmail($this->data[self::USER][Parameters::EMAIL]);
                            $response[self::SUBSCRIBED] = $this->userService->newsletterSubscribe($newsletterSubscriptionParametersGroup);
                        }
                    } else {
                        $this->responseMessageError = Utils::getErrorLabelValue($responseUser);
                        $this->responseError = $responseUser->getError();
                    }
                }
            }
        }

        return new class($response, $this->responseError) extends Element {

            public array $response = [];

            public ?Error $error = null;

            public function __construct(array $response, ?Error $error) {
                $this->response[SetUserController::USER] = isset($response[SetUserController::USER]) ? $response[SetUserController::USER] : [];
                $this->response[SetUserController::BILLING] = isset($response[SetUserController::BILLING]) ? $response[SetUserController::BILLING] : [];
                $this->response[SetUserController::BILLING_VALIDATE] = isset($response[SetUserController::BILLING_VALIDATE]) ? $response[SetUserController::BILLING_VALIDATE] : [];
                $this->response[SetUserController::SHIPPING] = isset($response[SetUserController::SHIPPING]) ? $response[SetUserController::SHIPPING] : [];
                $this->response[SetUserController::SHIPPING_VALIDATE] = isset($response[SetUserController::SHIPPING_VALIDATE]) ? $response[SetUserController::SHIPPING_VALIDATE] : [];
                $this->response[SetUserController::SUBSCRIBED] = isset($response[SetUserController::SUBSCRIBED]) ? $response[SetUserController::SUBSCRIBED] : [];
                $this->error = $error;
            }

            public function getError(): ?Error {
                return $this->error;
            }

            public function jsonSerialize(): mixed {
                return $this->response;
            }
        };
    }

    protected function userExists(string $value): bool {
        $userExists = $this->userService->getUserExists($value);
        return $userExists->getExists();
    }

    /**
     * This method parses the given Element and returns it.
     * 
     * @param Element $response
     * 
     * @return \SDK\Core\Dtos\Element
     */
    protected function parseResponseData(Element $response) {
        $data = [
            SetUserController::USER => $response->response[SetUserController::USER],
            SetUserController::BILLING => $response->response[SetUserController::BILLING],
            SetUserController::BILLING_VALIDATE => $response->response[SetUserController::BILLING_VALIDATE],
            SetUserController::SHIPPING => $response->response[SetUserController::SHIPPING],
            SetUserController::SHIPPING_VALIDATE => $response->response[SetUserController::SHIPPING_VALIDATE],
            SetUserController::SUBSCRIBED => $response->response[SetUserController::SUBSCRIBED],
            'error' => $response->getError()
        ];
        if (is_null($this->responseError)) {
            $data['redirect'] = static::getUrlRedirect();
        }
        return $data;
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
    protected function setControllerBaseBatchData(BatchRequests $requests): void {
    }
}
