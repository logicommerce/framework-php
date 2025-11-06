<?php

namespace FWK\Core\Theme\Dtos;

use FWK\Core\Theme\Dtos\Traits\FiltrableFormFieldsTrait;
use SDK\Core\Dtos\Element;
use SDK\Core\Dtos\Traits\ElementTrait;
use FWK\Enums\Parameters;

/**
 * This is the 'FormCompanyRolesFields' class, a DTO class for the theme configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see FormCompanyRolesFields::getPId()
 * @see FormCompanyRolesFields::getImage()
 *
 * @see Element
 * @see ElementTrait
 * @see FiltrableFormFieldsTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class FormCompanyRolesFields extends Element {
    use ElementTrait, FiltrableFormFieldsTrait;

    public const NAME = Parameters::NAME;

    public const DESCRIPTION = Parameters::DESCRIPTION;

    public const P_ID = Parameters::P_ID;

    public const TARGET = Parameters::TARGET;

    public const TARGET_DEFAULT = Parameters::TARGET_DEFAULT;

    private ?FormField $name = null;

    private ?FormField $description = null;

    private ?FormField $target = null;

    private ?FormField $targetDefault = null;


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

    /**
     * This method returns if the target FormField.
     *
     * @return FormField|Null
     */
    public function getTarget(): ?FormField {
        return $this->target;
    }

    private function setTarget(array $target): void {
        $this->target = new FormField($target);
    }

    /**
     * This method returns if the targetDefault FormField.
     *
     * @return FormField|Null
     */
    public function getTargetDefault(): ?FormField {
        return $this->targetDefault;
    }

    private function setTargetDefault(array $targetDefault): void {
        $this->targetDefault = new FormField($targetDefault);
    }
}
