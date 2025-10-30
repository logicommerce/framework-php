<?php

namespace FWK\Core\Theme\Dtos;

use FWK\Core\Theme\Dtos\Traits\FiltrableFormFieldsTrait;
use SDK\Core\Dtos\Element;
use SDK\Core\Dtos\Traits\ElementTrait;
use FWK\Enums\Parameters;

/**
 * This is the 'FormFieldsSetUser' class, a DTO class for the theme configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see FormFieldsSetUser::getAddress()
 * @see FormFieldsSetUser::getAddressAdditionalInformation()
 * @see FormFieldsSetUser::getAlias()
 * @see FormFieldsSetUser::getBirthday()
 * @see FormFieldsSetUser::getCity()
 * @see FormFieldsSetUser::getCompany()
 * @see FormFieldsSetUser::getCountry()
 * @see FormFieldsSetUser::getCreateAccount()
 * @see FormFieldsSetUser::getDefaultAddress()
 * @see FormFieldsSetUser::getEmail()
 * @see FormFieldsSetUser::getFax()
 * @see FormFieldsSetUser::getFirstName()
 * @see FormFieldsSetUser::getGender()
 * @see FormFieldsSetUser::getGodfatherCode()
 * @see FormFieldsSetUser::getImage()
 * @see FormFieldsSetUser::getLastName()
 * @see FormFieldsSetUser::getMobile()
 * @see FormFieldsSetUser::getNick()
 * @see FormFieldsSetUser::getNif()
 * @see FormFieldsSetUser::getNumber()
 * @see FormFieldsSetUser::getPId()
 * @see FormFieldsSetUser::getPassword()
 * @see FormFieldsSetUser::getPasswordRetype()
 * @see FormFieldsSetUser::getPhone()
 * @see FormFieldsSetUser::getPostalCode()
 * @see FormFieldsSetUser::getSeparator()
 * @see FormFieldsSetUser::getSeparator2()
 * @see FormFieldsSetUser::getSeparator3()
 * @see FormFieldsSetUser::getState()
 * @see FormFieldsSetUser::getSubscribed()
 * @see FormFieldsSetUser::getUseShippingAddress()
 * @see FormFieldsSetUser::getUserType()
 * @see FormFieldsSetUser::getVat()
 * @see FormFieldsSetUser::getRe()
 *
 * @see Element
 * @see ElementTrait
 * @see FiltrableFormFieldsTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class FormFieldsSetUser extends Element {
    use ElementTrait {
        __construct as superConstruct;
    }

    use FiltrableFormFieldsTrait;

    public const ADDRESS = Parameters::ADDRESS;

    public const ADDRESS_ADDITIONAL_INFORMATION = Parameters::ADDRESS_ADDITIONAL_INFORMATION;

    public const ALIAS = Parameters::ALIAS;

    public const BIRTHDAY = Parameters::BIRTHDAY;

    public const CITY = Parameters::CITY;

    public const COMPANY = Parameters::COMPANY;

    public const LOCATION = Parameters::LOCATION;

    public const COUNTRY = Parameters::COUNTRY;

    public const CREATE_ACCOUNT = Parameters::CREATE_ACCOUNT;

    public const CUSTOM_TAGS = Parameters::CUSTOM_TAGS;

    public const DEFAULT_ADDRESS = Parameters::DEFAULT_ADDRESS;

    public const EMAIL = Parameters::EMAIL;

    public const FAX = Parameters::FAX;

    public const FIRST_NAME = Parameters::FIRST_NAME;

    public const GENDER = Parameters::GENDER;

    public const GODFATHER_CODE = Parameters::GODFATHER_CODE;

    public const IMAGE = Parameters::IMAGE;

    public const LAST_NAME = Parameters::LAST_NAME;

    public const MOBILE = Parameters::MOBILE;

    public const NICK = Parameters::NICK;

    public const NIF = Parameters::NIF;

    public const NUMBER = Parameters::NUMBER;

    public const P_ID = Parameters::P_ID;

    public const PASSWORD = Parameters::PASSWORD;

    public const PASSWORD_RETYPE = Parameters::PASSWORD_RETYPE;

    public const PHONE = Parameters::PHONE;

    public const POSTAL_CODE = Parameters::POSTAL_CODE;

    public const STATE = Parameters::STATE;

    public const SUBSCRIBED = Parameters::SUBSCRIBED;

    public const USE_SHIPPING_ADDRESS = Parameters::USE_SHIPPING_ADDRESS;

    public const VAT = Parameters::VAT;

    public const RE = Parameters::RE;

    public const SEPARATOR = 'separator';

    public const SEPARATOR_2 = self::SEPARATOR . '2';

    public const SEPARATOR_3 = self::SEPARATOR . '3';

    private ?FormField $address = null;

    private ?FormField $addressAdditionalInformation = null;

    private ?FormField $alias = null;

    private ?FormField $birthday = null;

    private ?FormField $city = null;

    private ?FormField $company = null;

    private ?FormField $country = null;

    private ?FormField $createAccount = null;

    private ?FormField $customTags = null;

    private ?FormField $defaultAddress = null;

    private ?FormField $email = null;

    private ?FormField $fax = null;

    private ?FormField $firstName = null;

    private ?FormField $gender = null;

    private ?FormField $godfatherCode = null;

    private ?FormField $image = null;

    private ?FormField $lastName = null;

    private ?FormField $location = null;

    private ?FormField $mobile = null;

    private ?FormField $nick = null;

    private ?FormField $nif = null;

    private ?FormField $number = null;

    private ?FormField $pId = null;

    private ?FormField $password = null;

    private ?FormField $passwordRetype = null;

    private ?FormField $phone = null;

    private ?FormField $postalCode = null;

    private ?FormField $state = null;

    private ?FormField $subscribed = null;

    private ?FormField $useShippingAddress = null;

    private ?FormField $userType = null;

    private ?FormField $vat = null;

    private ?FormField $re = null;

    private ?FormField $separator = null;

    private ?FormField $separator2 = null;

    private ?FormField $separator3 = null;

    /**
     *
     * @see \SDK\Core\Dtos\Traits\ElementTrait::__construct()
     */
    public function __construct(array $data = [], $requiredUserType = true) {
        $this->superConstruct($data);
        if ($requiredUserType) {
            $this->userType = new FormField([FormField::INCLUDED => true, FormField::PRIORITY => 0]);
        }
    }

    /**
     * This method returns if the address FormField.
     *
     * @return FormField|Null
     */
    public function getAddress(): ?FormField {
        return $this->address;
    }

    private function setAddress(array $address): void {
        $this->address = new FormField($address);
    }

    /**
     * This method returns if the addressAdditionalInformation FormField.
     *
     * @return FormField|Null
     */
    public function getAddressAdditionalInformation(): ?FormField {
        return $this->addressAdditionalInformation;
    }

    private function setAddressAdditionalInformation(array $addressAdditionalInformation): void {
        $this->addressAdditionalInformation = new FormField($addressAdditionalInformation);
    }

    /**
     * This method returns if the alias FormField.
     *
     * @return FormField|Null
     */
    public function getAlias(): ?FormField {
        return $this->alias;
    }

    private function setAlias(array $alias): void {
        $this->alias = new FormField($alias);
    }

    /**
     * This method returns if the birthday FormField.
     *
     * @return FormField|Null
     */
    public function getBirthday(): ?FormField {
        return $this->birthday;
    }

    private function setBirthday(array $birthday): void {
        $this->birthday = new FormField($birthday);
    }
    /**
     * This method returns if the city FormField.
     *
     * @return FormField|Null
     */
    public function getCity(): ?FormField {
        return $this->city;
    }

    private function setCity(array $city): void {
        $this->city = new FormField($city);
    }

    /**
     * This method returns if the company FormField.
     *
     * @return FormField|Null
     */
    public function getCompany(): ?FormField {
        return $this->company;
    }

    private function setCompany(array $company): void {
        $this->company = new FormField($company);
    }

    /**
     * This method returns if the country FormField.
     *
     * @return FormField|Null
     */
    public function getCountry(): ?FormField {
        return $this->country;
    }

    private function setCountry(array $country): void {
        $this->country = new FormField($country);
    }

    /**
     * This method returns if the createAccount FormField.
     *
     * @return FormField|Null
     */
    public function getCreateAccount(): ?FormField {
        return $this->createAccount;
    }

    private function setCreateAccount(array $createAccount): void {
        $this->createAccount = new FormField($createAccount);
    }

    /**
     * This method returns if the customTags FormField.
     *
     * @return FormField|Null
     */
    public function getCustomTags(): ?FormField {
        return $this->customTags;
    }

    private function setCustomTags(array $customTags): void {
        $this->customTags = new FormField($customTags);
    }

    /**
     * This method returns if the defaultAddress FormField.
     *
     * @return FormField|Null
     */
    public function getDefaultAddress(): ?FormField {
        return $this->defaultAddress;
    }

    private function setDefaultAddress(array $defaultAddress): void {
        $this->defaultAddress = new FormField($defaultAddress);
    }

    /**
     * This method returns if the email FormField.
     *
     * @return FormField|Null
     */
    public function getEmail(): ?FormField {
        return $this->email;
    }

    private function setEmail(array $email): void {
        $this->email = new FormField($email);
    }

    /**
     * This method returns if the fax FormField.
     *
     * @return FormField|Null
     */
    public function getFax(): ?FormField {
        return $this->fax;
    }

    private function setFax(array $fax): void {
        $this->fax = new FormField($fax);
    }

    /**
     * This method returns if the firstName FormField.
     *
     * @return FormField|Null
     */
    public function getFirstName(): ?FormField {
        return $this->firstName;
    }

    private function setFirstName(array $firstName): void {
        $this->firstName = new FormField($firstName);
    }

    /**
     * This method returns if the gender FormField.
     *
     * @return FormField|Null
     */
    public function getGender(): ?FormField {
        return $this->gender;
    }

    private function setGender(array $gender): void {
        $this->gender = new FormField($gender);
    }

    /**
     * This method returns if the godfatherCode FormField.
     *
     * @return FormField|Null
     */
    public function getGodfatherCode(): ?FormField {
        return $this->godfatherCode;
    }

    private function setGodfatherCode(array $godfatherCode): void {
        $this->godfatherCode = new FormField($godfatherCode);
    }

    /**
     * This method returns if the image FormField.
     *
     * @return FormField|Null
     */
    public function getImage(): ?FormField {
        return $this->image;
    }

    private function setImage(array $image): void {
        $this->image = new FormField($image);
    }

    /**
     * This method returns if the lastName FormField.
     *
     * @return FormField|Null
     */
    public function getLastName(): ?FormField {
        return $this->lastName;
    }

    private function setLastName(array $lastName): void {
        $this->lastName = new FormField($lastName);
    }

    /**
     * This method returns if the location FormField.
     *
     * @return FormField|Null
     */
    public function getLocation(): ?FormField {
        return $this->location;
    }

    private function setLocation(array $location): void {
        $this->location = new FormField($location);
    }

    /**
     * This method returns if the mobile FormField.
     *
     * @return FormField|Null
     */
    public function getMobile(): ?FormField {
        return $this->mobile;
    }

    private function setMobile(array $mobile): void {
        $this->mobile = new FormField($mobile);
    }

    /**
     * This method returns if the nick FormField.
     *
     * @return FormField|Null
     */
    public function getNick(): ?FormField {
        return $this->nick;
    }

    private function setNick(array $nick): void {
        $this->nick = new FormField($nick);
    }

    /**
     * This method returns if the nif FormField.
     *
     * @return FormField|Null
     */
    public function getNif(): ?FormField {
        return $this->nif;
    }

    private function setNif(array $nif): void {
        $this->nif = new FormField($nif);
    }

    /**
     * This method returns if the number FormField.
     *
     * @return FormField|Null
     */
    public function getNumber(): ?FormField {
        return $this->number;
    }

    private function setNumber(array $number): void {
        $this->number = new FormField($number);
    }

    /**
     * This method returns if the pId FormField.
     *
     * @return FormField|Null
     */
    public function getPId(): ?FormField {
        return $this->pId;
    }

    private function setPId(array $pId): void {
        $this->pId = new FormField($pId);
    }

    /**
     * This method returns if the password FormField.
     *
     * @return FormField|Null
     */
    public function getPassword(): ?FormField {
        return $this->password;
    }

    private function setPassword(array $password): void {
        $this->password = new FormField($password);
    }

    /**
     * This method returns if the passwordRetype FormField.
     *
     * @return FormField|Null
     */
    public function getPasswordRetype(): ?FormField {
        return $this->passwordRetype;
    }

    private function setPasswordRetype(array $passwordRetype): void {
        $this->passwordRetype = new FormField($passwordRetype);
    }

    /**
     * This method returns if the phone FormField.
     *
     * @return FormField|Null
     */
    public function getPhone(): ?FormField {
        return $this->phone;
    }

    private function setPhone(array $phone): void {
        $this->phone = new FormField($phone);
    }

    /**
     * This method returns if the postalCode FormField.
     *
     * @return FormField|Null
     */
    public function getPostalCode(): ?FormField {
        return $this->postalCode;
    }

    private function setPostalCode(array $postalCode): void {
        $this->postalCode = new FormField($postalCode);
    }

    /**
     * This method returns if the state FormField.
     *
     * @return FormField|Null
     */
    public function getState(): ?FormField {
        return $this->state;
    }

    private function setState(array $state): void {
        $this->state = new FormField($state);
    }

    /**
     * This method returns if the subscribed FormField.
     *
     * @return FormField|Null
     */
    public function getSubscribed(): ?FormField {
        return $this->subscribed;
    }

    private function setSubscribed(array $subscribed): void {
        $this->subscribed = new FormField($subscribed);
    }

    /**
     * This method returns if the useShippingAddress FormField.
     *
     * @return FormField|Null
     */
    public function getUseShippingAddress(): ?FormField {
        return $this->useShippingAddress;
    }

    private function setUseShippingAddress(array $useShippingAddress): void {
        $this->useShippingAddress = new FormField($useShippingAddress);
    }

    /**
     * This method returns if the userType FormField.
     *
     * @return FormField|Null
     */
    public function getUserType(): ?FormField {
        return $this->userType;
    }

    /**
     * This method returns if the vat FormField.
     *
     * @return FormField|Null
     */
    public function getVat(): ?FormField {
        return $this->vat;
    }

    private function setVat(array $vat): void {
        $this->vat = new FormField($vat);
    }

    /**
     * This method returns if the re FormField.
     *
     * @return FormField|Null
     */
    public function getRe(): ?FormField {
        return $this->re;
    }

    private function setRe(array $re): void {
        $this->re = new FormField($re);
    }

    /**
     * This method returns if the separator FormField.
     *
     * @return FormField|Null
     */
    public function getSeparator(): ?FormField {
        return $this->separator;
    }

    private function setSeparator(array $separator): void {
        $this->separator = new FormField($separator);
    }

    /**
     * This method returns if the separator2 FormField.
     *
     * @return FormField|Null
     */
    public function getSeparator2(): ?FormField {
        return $this->separator2;
    }

    private function setSeparator2(array $separator2): void {
        $this->separator2 = new FormField($separator2);
    }

    /**
     * This method returns if the separator3 FormField.
     *
     * @return FormField|Null
     */
    public function getSeparator3(): ?FormField {
        return $this->separator3;
    }

    private function setSeparator3(array $separator3): void {
        $this->separator3 = new FormField($separator3);
    }
}
