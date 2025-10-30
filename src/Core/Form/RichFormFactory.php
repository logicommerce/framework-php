<?php

namespace FWK\Core\Form;

use FWK\Core\Resources\Language;
use FWK\Enums\LanguageLabels;
use FWK\Enums\Parameters;

/**
 * This is the RichFormFactory class, a factory of Rich Form instances.
 *
 * @abstract
 *
 * @see RichFormFactory::getLcDataValidation()
 *
 * @package FWK\Core\Form
 */
abstract class RichFormFactory {

    protected static ?Language $language = null;

     /**
     * This static method returns the data-validation attribute to rich the Form.
     *
     * @param string $name
     * @param string $elementType
     * @param array $properties
     *
     * @return string
     */
    public static function getLcDataValidation(string $name, string $elementType, array $properties): string {
        if(is_null(self::$language)){
            self::$language = Language::getInstance();
        }
        $attributeText = '';
        $validationMsgAttribute = '';
        $confirmAttribute = '';
        $arrayName = (explode('_',$name));

        // Array from lc.plugin.js -> setValidationData
        if (in_array($elementType, ['email', 'phone', 'vat', 'vat_es']) ){
            $attributeText = $elementType;
        }
        $regexAttribute = '';
        if (isset($properties['regex']) && strlen($properties['regex'])){
            $regexAttribute = ' data-validation-regexp="' . trim($properties['regex'],'/') . '"';
            $attributeText .= (strlen($attributeText)?',':'') . 'custom';
        }
        if (isset($properties['required']) && $properties['required']){
            $attributeText .= (strlen($attributeText)?',':'') . 'required';
        }

        $find = (array_search(Parameters::PASSWORD, $arrayName));
        if($find != false ){
            $validationMsgAttribute = ' data-validation-error-msg="' . self::$language->getLabelValue(LanguageLabels::PASSWORD_REQUIRED) . '"';
        }
        
        $find = (array_search(Parameters::PASSWORD_RETYPE, $arrayName));
        if($find != false ){
            $attributeText .= (strlen($attributeText)?',':'') . 'confirmation';
            $arrayName[$find] = Parameters::PASSWORD;
            $confirmAttribute = ' data-validation-confirm="' . implode('_', $arrayName) . '" ';
            $validationMsgAttribute = ' data-validation-error-msg="' . self::$language->getLabelValue(LanguageLabels::PASSWORDS_DONT_MATCH) . '"';
        }

        return (strlen($attributeText)?('data-validation="' . $attributeText . '" ' . $regexAttribute):'') . $confirmAttribute . $validationMsgAttribute;

    }

}