<?php

namespace FWK\Core\Theme\Dtos;

use FWK\Core\Theme\Dtos\Traits\FiltrableFormFieldsTrait;
use SDK\Core\Dtos\Element;
use SDK\Core\Dtos\Traits\ElementTrait;
use FWK\Enums\Parameters;

/**
 * This is the 'FormAccountFields' class, a DTO class for the theme configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see FormAccountFields::getType()
 * @see FormAccountFields::getStatus()
 * @see FormAccountFields::getDateAdded()
 * @see FormAccountFields::getLastUsed()
 * @see FormAccountFields::getPId()
 * @see FormAccountFields::getEmail()
 * @see FormAccountFields::getImage()
 * @see FormAccountFields::getDescription()
 *
 * @see Element
 * @see ElementTrait
 * @see FiltrableFormFieldsTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class FormAccountFields extends Element {
    use ElementTrait, FiltrableFormFieldsTrait;

    public const TYPE = Parameters::TYPE;

    public const STATUS = Parameters::STATUS;

    public const DATE_ADDED = Parameters::DATE_ADDED;

    public const LAST_USED = Parameters::LAST_USED;

    public const P_ID = Parameters::P_ID;

    public const EMAIL = Parameters::EMAIL;

    public const IMAGE = Parameters::IMAGE;

    public const DESCRIPTION = Parameters::DESCRIPTION;

    private ?FormField $type = null;

    private ?FormField $status = null;

    private ?FormField $dateAdded = null;

    private ?FormField $lastUsed = null;

    private ?FormField $pId = null;

    private ?FormField $email = null;

    private ?FormField $image = null;

    private ?FormField $description = null;

    /**
     * This method returns if the type FormField.
     *
     * @return FormField|Null
     */
    public function getType(): ?FormField {
        return $this->type;
    }

    private function setType(array $type): void {
        $this->type = new FormField($type);
    }

    /**
     * This method returns if the status FormField.
     *
     * @return FormField|Null
     */
    public function getStatus(): ?FormField {
        return $this->status;
    }

    private function setStatus(array $status): void {
        $this->status = new FormField($status);
    }

    /**
     * This method returns if the dateAdded FormField.
     *
     * @return FormField|Null
     */
    public function getDateAdded(): ?FormField {
        return $this->dateAdded;
    }

    private function setDateAdded(array $dateAdded): void {
        $this->dateAdded = new FormField($dateAdded);
    }

    /**
     * This method returns if the lastUsed FormField.
     *
     * @return FormField|Null
     */
    public function getLastUsed(): ?FormField {
        return $this->lastUsed;
    }

    private function setLastUsed(array $lastUsed): void {
        $this->lastUsed = new FormField($lastUsed);
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
     * This method returns if the description FormField.
     *
     * @return FormField|Null
     */
    public function getDescription(): ?FormField {
        return $this->description;
    }

    private function setDescription(array $description): void {
        $this->description = new FormField($description);
    }
}
