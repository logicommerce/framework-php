<?php

namespace FWK\Core\Theme\Dtos;

use FWK\Core\Theme\Dtos\Traits\LocationFormFieldsTrait;
use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Core\Dtos\Element;

/**
 * This is the 'FormCompanyRoles' class, a DTO class for the form configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see FormSetUser::getFields()
 * @see FormSetUser::getAccountTabPane()
 * @see FormSetUser::getDefaultAccountTabPane()
 *
 * @see Element
 * @see ElementTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class FormCompanyRoles extends Element {
    use ElementTrait;

    public const FIELDS = 'fields';

    private ?FormCompanyRolesFields $fields = null;

    /**
     * This method returns the form company roles fields configurations.
     * 
     * @return FormCompanyRolesFields|NULL
     */
    public function getFields(): ?FormCompanyRolesFields {
        return $this->fields;
    }

    private function setFields(array $fields): void {
        $this->fields = new FormCompanyRolesFields($fields);
    }
}
