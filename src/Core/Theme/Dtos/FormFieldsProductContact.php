<?php

namespace FWK\Core\Theme\Dtos;

use FWK\Core\Theme\Dtos\Traits\FiltrableFormFieldsTrait;
use SDK\Core\Dtos\Element;
use SDK\Core\Dtos\Traits\ElementTrait;
use FWK\Enums\Parameters;

/**
 * This is the 'FormFieldsProductContact' class, a DTO class for the theme configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see FormFieldsProductContact::getName()
 * @see FormFieldsProductContact::getFirstName()
 * @see FormFieldsProductContact::getLastName()
 * @see FormFieldsProductContact::getEmail()
 * @see FormFieldsProductContact::getPhone()
 * @see FormFieldsProductContact::getComment()
 * @see FormFieldsProductContact::getSeparator()
 *
 * @see Element
 * @see ElementTrait
 * @see FiltrableFormFieldsTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class FormFieldsProductContact extends Element {
    use ElementTrait, FiltrableFormFieldsTrait;

    public const NAME = Parameters::NAME;

    public const FIRST_NAME = Parameters::FIRST_NAME;

    public const LAST_NAME = Parameters::LAST_NAME;

    public const EMAIL = Parameters::EMAIL;

    public const PHONE = Parameters::PHONE;

    public const COMMENT = Parameters::COMMENT;

    public const SEPARATOR = 'separator';

    private ?FormField $id = null;

    private ?FormField $name = null;

    private ?FormField $firstName = null;

    private ?FormField $lastName = null;

    private ?FormField $email = null;

    private ?FormField $phone = null;

    private ?FormField $comment = null;

    private ?FormField $separator = null;

    /**
     * This method returns if the name FormField.
     *
     * @return FormField|Null
     */
    public function getName(): ?FormField {
        return $this->name;
    }

    private function setName(array $name): void {
        $this->name = new FormField($name);
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
}
