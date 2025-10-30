<?php

namespace FWK\Core\Theme\Dtos\Traits;

use FWK\Core\Theme\Dtos\FormField;
use FWK\Core\Theme\Dtos\FormFieldsSetUser;
use FWK\Services\LmsService;

/**
 * This is the filtrable form fields trait.
 *
 * @internal This trait has been created to share common code between FormFields with location parameter
 *        
 * @see setLocationFields()
 * @see getLocationFields()
 *   
 * @package FWK\Core\Theme\Dtos
 */
trait LocationFormFieldsTrait {

    private function setLocationFields(array $fields): array {
        $priorityIncrement = 0;
        $auxFields = [];
        $locationFields = [];
        foreach ($fields as $field => $fieldValue) {
            if ($field === FormFieldsSetUser::LOCATION) {
                $locationPriority = (isset($fieldValue[FormField::PRIORITY]) ? $fieldValue[FormField::PRIORITY] : 0);
                $locationFields = $this->getLocationFields($locationPriority, $fieldValue[FormField::INCLUDED]);
                foreach ($locationFields as $locationField => $locationValue) {
                    $auxFields[$locationField] = $locationValue;
                    $priorityIncrement++;
                }
            }
        }
        if ($priorityIncrement > 0) {
            foreach ($fields as $field => $fieldValue) {
                if (isset($fieldValue[FormField::PRIORITY]) && $fieldValue[FormField::PRIORITY] > $locationPriority) {
                    $fieldValue[FormField::PRIORITY] += $priorityIncrement;
                }
                if ($field != FormFieldsSetUser::LOCATION || ($field === FormFieldsSetUser::LOCATION && count($locationFields) == 1)) {
                    $auxFields[$field] = $fieldValue;
                }
            }
        } else {
            $auxFields = $fields;
        }

        return $auxFields;
    }

    /**
     * This method returns the array with the fields used to make the location section.
     * 
     * @return array
     */
    public function getLocationFields(int $priority, bool $included): array {
        if (LmsService::getLocationSearchCityLicense() || LmsService::getLocationSearchZipCityLicense()) {
            return [
                FormFieldsSetUser::COUNTRY => [FormField::INCLUDED => $included, FormField::PRIORITY => $priority + 1, FormField::REQUIRED => true]
            ];
        }
        return [
            FormFieldsSetUser::COUNTRY => [FormField::INCLUDED => $included, FormField::PRIORITY => $priority + 1, FormField::REQUIRED => true],
            FormFieldsSetUser::STATE => [FormField::INCLUDED => $included, FormField::PRIORITY => $priority + 2, FormField::REQUIRED => true],
            FormFieldsSetUser::CITY => [FormField::INCLUDED => $included, FormField::PRIORITY => $priority + 3, FormField::REQUIRED => true],
            FormFieldsSetUser::POSTAL_CODE => [FormField::INCLUDED => $included, FormField::PRIORITY => $priority + 4, FormField::REQUIRED => true]
        ];
    }
}
