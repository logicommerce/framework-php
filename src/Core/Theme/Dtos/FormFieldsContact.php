<?php

namespace FWK\Core\Theme\Dtos;

use FWK\Core\Theme\Dtos\Traits\FiltrableFormFieldsTrait;
use SDK\Core\Dtos\Element;
use SDK\Core\Dtos\Traits\ElementTrait;
use FWK\Enums\Parameters;

/**
 * This is the 'FormFieldsContact' class, a DTO class for the theme configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see FormFieldsContact::getName()
 * @see FormFieldsContact::getFirstName()
 * @see FormFieldsContact::getLastName()
 * @see FormFieldsContact::getEmail()
 * @see FormFieldsContact::getPhone()
 * @see FormFieldsContact::getComment()
 * @see FormFieldsContact::getSeparator()
 * @see FormFieldsContact::getSeparator2()
 * @see FormFieldsContact::getSeparator3()
 *
 * @see Element
 * @see ElementTrait
 * @see FiltrableFormFieldsTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class FormFieldsContact extends Element {
    use ElementTrait, FiltrableFormFieldsTrait;

    public const FIRST_NAME = Parameters::FIRST_NAME;

    public const LAST_NAME = Parameters::LAST_NAME;

    public const COMPANY = Parameters::COMPANY;

    public const ADDRESS = Parameters::ADDRESS;

    public const ADDRESS_ADDITIONAL_INFORMATION = Parameters::ADDRESS_ADDITIONAL_INFORMATION;

    public const NUMBER = Parameters::NUMBER;

    public const CITY = Parameters::CITY;

    public const SEPARATOR = 'separator';

    public const SEPARATOR_2 = self::SEPARATOR . '2';

    public const SEPARATOR_3 = self::SEPARATOR . '3';

    public const STATE = Parameters::STATE;

    public const POSTAL_CODE = Parameters::POSTAL_CODE;

    public const VAT = Parameters::VAT;

    public const NIF = Parameters::NIF;

    public const PHONE = Parameters::PHONE;

    public const MOBILE = Parameters::MOBILE;

    public const FAX = Parameters::FAX;

    public const EMAIL = Parameters::EMAIL;

    public const MOTIVE_ID = Parameters::MOTIVE_ID;

    public const COMMENT = Parameters::COMMENT;

    private ?FormField $firstName = null;

    private ?FormField $lastName = null;

    private ?FormField $company = null;

    private ?FormField $address = null;

    private ?FormField $addressAdditionalInformation = null;

    private ?FormField $number = null;

    private ?FormField $city = null;

    private ?FormField $state = null;

    private ?FormField $postalCode = null;

    private ?FormField $vat = null;

    private ?FormField $nif = null;

    private ?FormField $phone = null;

    private ?FormField $mobile = null;

    private ?FormField $fax = null;

    private ?FormField $email = null;

    private ?FormField $motiveId = null;

    private ?FormField $comment = null;

    private ?FormField $separator = null;

    private ?FormField $separator2 = null;

    private ?FormField $separator3 = null;

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
     * This method returns if the motiveId FormField.
     *
     * @return FormField|Null
     */
    public function getMotiveId(): ?FormField {
        return $this->motiveId;
    }

    private function setMotiveId(array $motiveId): void {
        $this->motiveId = new FormField($motiveId);
    }

    /**
     * This method returns if the comment FormField.
     *
     * @return FormField|Null
     */
    public function getComment(): ?FormField {
        return $this->comment;
    }

    private function setComment(array $comment): void {
        $this->comment = new FormField($comment);
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
