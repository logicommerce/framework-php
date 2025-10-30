<?php

namespace FWK\Core\ViewHelpers;

use FWK\Core\Theme\Theme;
use FWK\Core\Resources\Language;
use SDK\Application;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Resources\Session;

/**
 * This is the ViewHelper base class for all the view helpers.
 * The purpose of a ViewHelper is to facilitate to Twig the generation of an item's output, among other things. 
 * For example, the helper of a product, could returns that product converted to a json under a certain format.   
 *
 * @abstract
 * 
 * @see ViewHelper::getApplicationTaxesIncluded()
 * @see ViewHelper::mergeArguments()
 *
 * @package FWK\Core\ViewHelpers
 */
abstract class ViewHelper {

    protected ?Theme $theme = null;

    protected ?Language $languageSheet = null;

    protected ?Session $session = null;

    private static ?bool $showTaxesIncluded = null;

    public function __construct(Language $languageSheet, Theme $theme, ?Session $session) {
        $this->languageSheet = $languageSheet;
        $this->theme = $theme;
        $this->session = $session;
    }

    /**
     * This method returns the setting of 'show taxes included'.
     *
     * @return bool
     */
    public static function getApplicationTaxesIncluded(): bool {
        if (is_null(self::$showTaxesIncluded)) {
            self::$showTaxesIncluded = Theme::getInstance()->getConfiguration()->getCommerce()->showTaxesIncluded();
        }
        return self::$showTaxesIncluded;
    }

    /**
     * This method merges macro arguments with macro associated class properties
     * by reference.
     *
     * @return void
     */
    public static function mergeArguments(&$classObject, array $arguments = []): void {
        $className = get_class($classObject);

        foreach ($arguments as $name => $value) {
            if (property_exists($className, $name)) {
                $classObject->{$name} = $value;
            } else {
                throw new CommerceException("The argument '" . $name . "' not exists in " . $className, CommerceException::VIEW_HELPER_UNDEFINED_ARGUMENT);
            }
        }
    }
}
