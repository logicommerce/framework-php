<?php

namespace FWK\Core\Theme\Dtos;

use FWK\Core\Theme\Dtos\Traits\FiltrableFormFieldsTrait;
use SDK\Core\Dtos\Element;
use SDK\Core\Dtos\Traits\ElementTrait;
use FWK\Enums\Parameters;

/**
 * This is the 'FormCompanyDivisionGeneralFields' class, a DTO class for the theme configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see FormCompanyDivisionGeneralFields::getPId()
 * @see FormCompanyDivisionGeneralFields::getImage()
 *
 * @see Element
 * @see ElementTrait
 * @see FiltrableFormFieldsTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class FormCompanyDivisionGeneralFields extends Element {
    use ElementTrait, FiltrableFormFieldsTrait;

    public const P_ID = Parameters::P_ID;

    public const IMAGE = Parameters::IMAGE;

    private ?FormField $pId = null;

    private ?FormField $image = null;

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
