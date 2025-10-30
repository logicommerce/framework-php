<?php

namespace FWK\Core\Theme\Dtos\Traits;

use FWK\Core\Theme\Dtos\FormField;

/**
 * This is the filtrable form fields trait.
 *
 * @internal This trait has been created to share common code between FormFields classes
 *        
 * @see getSortFilterArrayFormFields()
 * @see getSortFilterArrayFields()
 *   
 * @package FWK\Core\Theme\Dtos
 */
trait FiltrableFormFieldsTrait {

    /**
     * This method returns and array with the FormFields sorted and filtered.
     *
     * @return FormField array
     */
    public function getSortFilterArrayFormFields(bool $asc = true, bool $included = true): array {
        $sortFileds = [];
        foreach (get_object_vars($this) as $name => $formField) {
            if ($formField !== null && $formField->getIncluded() === $included) {
                $field = $formField;
                $field->setKeyName($name);
                $sortFileds[] = $field;
            }
        }
        $fieldA = $fieldB = null;
        if ($asc) {
            usort($sortFileds, fn (FormField $fieldA, FormField $fieldB) => ($fieldA->getPriority() <=> $fieldB->getPriority()));
        } else {
            usort($sortFileds, fn (FormField $fieldA, FormField $fieldB) => - ($fieldA->getPriority() <=> $fieldB->getPriority()));
        }
        $result = [];
        foreach ($sortFileds as $formField) {
            $keyName = $formField->getKeyName();
            $result[$keyName] = $formField;
            unset($keyName);
        }
        return $result;
    }

    /**
     * This method returns and array with the fields sorted and filtered.
     *
     * @return array
     */
    public function getSortFilterArrayFields(bool $asc = true, bool $included = true): array {
        return  array_keys(self::getSortFilterArrayFormFields($asc, $included));
    }


    /**
     * This method returns if some field has priority
     *
     * @return bool
     */
    public function hasPriority(): bool {
        foreach (get_object_vars($this) as $formField) {
            if ($formField !== null && $formField->getPriority() > 0) {
                return true;
            }
        }
        return false;
    }
}
