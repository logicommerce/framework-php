<?php

namespace FWK\Core\Theme\Dtos;

use FWK\Core\Theme\Dtos\Traits\FiltrableFormFieldsTrait;
use SDK\Core\Dtos\Element;
use SDK\Core\Dtos\Traits\ElementTrait;
use FWK\Enums\Parameters;

/**
 * This is the 'FormAccountTabPaneField' class, a DTO class for the account tab pane configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see FormFieldsRegisteredUser::getIncluded()
 * 
 *
 * @see Element
 * @see ElementTrait
 * @see FiltrableFormFieldsTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class FormAccountTabPaneField extends Element {
    use ElementTrait, FiltrableFormFieldsTrait;

    public const INCLUDED = "INCLUDED";

    private bool $included = true;

    /**
     * This method returns if the field is included. 
     *
     * @return bool
     */
    public function getIncluded(): bool {
        return $this->included;
    }
}
