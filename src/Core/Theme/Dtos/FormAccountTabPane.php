<?php

namespace FWK\Core\Theme\Dtos;

use FWK\Core\Theme\Dtos\Traits\FiltrableFormFieldsTrait;
use SDK\Core\Dtos\Element;
use SDK\Core\Dtos\Traits\ElementTrait;
use FWK\Enums\Parameters;

/**
 * This is the 'FormAccountTabPane' class, a DTO class for the account tab pane configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 *
 * @see Element
 * @see ElementTrait
 * @see FiltrableFormFieldsTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class FormAccountTabPane extends Element {
    use ElementTrait, FiltrableFormFieldsTrait;

    public const INTERNAL = Parameters::INTERNAL;

    public const EXTERNAL = Parameters::EXTERNAL;

    public const EXISTENT = Parameters::EXISTENT;

    public const NEW = Parameters::NEW;

    private ?FormAccountTabPaneField $internal = null;

    private ?FormAccountTabPaneField $external = null;

    private ?FormAccountTabPaneField $existent = null;

    private ?FormAccountTabPaneField $new = null;

    /**
     * Sets the internal property.
     * 
     * @return ?FormAccountTabPaneField
     */
    public function getInternal(): ?FormAccountTabPaneField {
        return $this->internal;
    }

    private function setInternal(array $internal): void {
        $this->internal = new FormAccountTabPaneField($internal);
    }

    /**
     * Sets the external property.
     * 
     * @return ?FormAccountTabPaneField
     */
    public function getExternal(): ?FormAccountTabPaneField {
        return $this->external;
    }

    private function setExternal(array $external): void {
        $this->external = new FormAccountTabPaneField($external);
    }

    /**
     * Sets the existent property.
     * 
     * @return ?FormAccountTabPaneField
     */
    public function getExistent(): ?FormAccountTabPaneField {
        return $this->existent;
    }

    private function setExistent(array $existent): void {
        $this->existent = new FormAccountTabPaneField($existent);
    }

    /**
     * Sets the new property.
     * 
     * @return ?FormAccountTabPaneField
     */
    public function getNew(): ?FormAccountTabPaneField {
        return $this->new;
    }

    private function setNew(array $new): void {
        $this->new = new FormAccountTabPaneField($new);
    }
}
