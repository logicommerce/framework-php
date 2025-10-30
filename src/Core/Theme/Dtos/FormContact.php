<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Core\Dtos\Element;

/**
 * This is the 'FormContact' class, a DTO class for the form configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see FormContact::getFields()
 * @see FormContact::getDisabledMotive()
 * @see FormContact::getRequired()
 *
 * @see Element
 * @see ElementTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class FormContact extends Element {
    use ElementTrait;

    public const FIELDS = 'fields';

    public const DISABLED_MOTIVE = 'disabledMotive';

    private ?FormFieldsContact $fields = null;

    private bool $disabledMotive = false;

    /**
     * This method returns the array with the fields to set in the set user form.
     * 
     * @return FormFieldsContact|NULL
     */
    public function getFields(): ?FormFieldsContact {
        return $this->fields;
    }

    /**
     * This method returns if the disabled motive option is enabled or not.
     * 
     * @return bool
     */
    public function getDisabledMotive(): bool {
        return $this->disabledMotive;
    }

    private function setFields(array $fields): void {
        $this->fields = new FormFieldsContact($fields);
    }
}
