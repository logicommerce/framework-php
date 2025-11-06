<?php

namespace FWK\Core\Theme\Dtos;

use FWK\Core\Theme\Dtos\Traits\FiltrableFormFieldsTrait;
use SDK\Core\Dtos\Element;
use SDK\Core\Dtos\Traits\ElementTrait;
use FWK\Enums\Parameters;

/**
 * This is the 'FormCompanyDivisionInvoicingFields' class, a DTO class for the theme configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see FormCompanyDivisionInvoicingFields::getLocation()
 * @see FormCompanyDivisionInvoicingFields::getAddress()
 * @see FormCompanyDivisionInvoicingFields::getAddressAdditionalInformation()
 * @see FormCompanyDivisionInvoicingFields::getNumber()
 * @see FormCompanyDivisionInvoicingFields::getPhone()
 * @see FormCompanyDivisionInvoicingFields::getMobile()
 * @see FormCompanyDivisionInvoicingFields::getCompany()
 * @see FormCompanyDivisionInvoicingFields::getVat()
 * @see FormCompanyDivisionInvoicingFields::getImage()
 *
 * @see Element
 * @see ElementTrait
 * @see FiltrableFormFieldsTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class FormCompanyDivisionInvoicingFields extends Element {
    use ElementTrait, FiltrableFormFieldsTrait;

    public const LOCATION = Parameters::LOCATION;

    public const ADDRESS = Parameters::ADDRESS;

    public const ADDRESS_ADDITIONAL_INFORMATION = Parameters::ADDRESS_ADDITIONAL_INFORMATION;

    public const NUMBER = Parameters::NUMBER;

    public const PHONE = Parameters::PHONE;

    public const MOBILE = Parameters::MOBILE;

    public const COMPANY = Parameters::COMPANY;

    public const VAT = Parameters::VAT;

    public const IMAGE = Parameters::IMAGE;

    private ?FormField $location = null;

    private ?FormField $address = null;

    private ?FormField $addressAdditionalInformation = null;

    private ?FormField $number = null;

    private ?FormField $phone = null;

    private ?FormField $mobile = null;

    private ?FormField $company = null;

    private ?FormField $vat = null;

    private ?FormField $image = null;

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
}
