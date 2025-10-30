<?php

namespace FWK\Core\Theme\Dtos\Traits;

use FWK\Core\Theme\Dtos\FormField;
use FWK\Core\Theme\Dtos\FormSeparator;

/**
 * This is the separator form fields trait.
 *
 * @internal This trait has been created to share common code between FormFields with location parameter
 *        
 * @see setLocationFields()
 * @see getLocationFields()
 *   
 * @package FWK\Core\Theme\Dtos
 */
trait SeparatosFormFieldsTrait {
        
    /**
     * This method returns if the separator FormField.
     *
     * @return FormField|Null
     */
    public function getSeparator(): ?FormField
    {
        return $this->separator;
    }

    private function setSeparator(array $separator): void
    {
        $this->separator = new FormField($separator);
    }

    /**
     * This method returns if the separator2 FormField.
     *
     * @return FormField|Null
     */
    public function getSeparator2(): ?FormField
    {
        return $this->separator2;
    }

    private function setSeparator2(array $separator2): void
    {
        $this->separator2= new FormField($separator2);
    }

    /**
     * This method returns if the separator3 FormField.
     *
     * @return FormField|Null
     */
    public function getSeparator3(): ?FormField
    {
        return $this->separator3;
    }

    private function setSeparator3(array $separator3): void
    {
        $this->separator3 = new FormField($separator3);
    }


}