<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Element;
use SDK\Core\Dtos\Traits\ElementTrait;

/**
 * This is the 'FormCompanyDivisionGeneral' class, a DTO class for the theme configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see FormCompanyDivisionGeneral::getPId()
 *
 * @see Element
 * @see ElementTrait
 * @see FiltrableFormFieldsTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class FormCompanyDivisionGeneral extends Element {
    use ElementTrait;

    public const FORM_GENERAL_FIELDS = "formGeneralFields";

    public const ENABLED = "enabled";

    private ?FormCompanyDivisionGeneralFields $formGeneralFields = null;

    private bool $enabled = true;

    /**
     * This method returns the master fields configuration.
     * 
     * @return FormCompanyDivisionGeneralFields|NULL
     */
    public function getFormGeneralFields(): ?FormCompanyDivisionGeneralFields {
        return $this->formGeneralFields;
    }

    private function setFormGeneralFields(array $formGeneralFields): void {
        $this->formGeneralFields = new FormCompanyDivisionGeneralFields($formGeneralFields);
    }
    /**
     * Sets the enabled property.
     * 
     * @return bool
     */
    public function getEnabled(): bool {
        return $this->enabled;
    }
}
